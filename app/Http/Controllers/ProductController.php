<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $categoryId = request('category_id');
        $selectedCategory = $categoryId ? Category::with('attributes')->find($categoryId) : null;
        $attributes = $selectedCategory?->attributes ?? collect();

        return view('products.create', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'attributes' => $attributes,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = Category::with('attributes')->findOrFail($data['category_id']);

        $product = Product::create([
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'currency' => strtoupper($data['currency']),
            'stock' => $data['stock'] ?? 0,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        $this->syncAttributeValues(
            $product,
            $category->attributes,
            $request->input('attribute_values', [])
        );

        $this->syncImages($product, $data['image_urls'] ?? []);

        return redirect()
            ->route('products.show', $product)
            ->with('status', 'Товар создан');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'user', 'attributeValues.attribute', 'images']);

        return view('products.show', compact('product'));
    }

    private function syncAttributeValues(Product $product, Collection $attributes, array $inputs): void
    {
        foreach ($attributes as $attribute) {
            $rawValue = $inputs[$attribute->id] ?? null;
            $isRequired = ($attribute->pivot->is_required ?? false) || $attribute->is_required;

            if ($isRequired && ($rawValue === null || $rawValue === '' || $rawValue === [])) {
                throw ValidationException::withMessages([
                    "attribute_values.{$attribute->id}" => "Поле {$attribute->name} обязательно",
                ]);
            }

            if ($rawValue === null || $rawValue === '' || $rawValue === []) {
                continue;
            }

            [$valueText, $valueNumber, $valueBoolean, $valueJson] = $this->normalizeAttributeValue($attribute, $rawValue);

            ProductAttributeValue::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                ],
                [
                    'value_text' => $valueText,
                    'value_number' => $valueNumber,
                    'value_boolean' => $valueBoolean,
                    'value_json' => $valueJson,
                ]
            );
        }
    }

    private function normalizeAttributeValue(Attribute $attribute, mixed $rawValue): array
    {
        return match ($attribute->type) {
            'text' => [$this->stringValue($rawValue), null, null, null],
            'number' => [null, $this->numericValue($attribute, $rawValue), null, null],
            'boolean' => [null, null, $this->booleanValue($rawValue), null],
            'select' => [$this->selectValue($attribute, $rawValue), null, null, null],
            'multiselect' => [null, null, null, $this->multiSelectValue($attribute, $rawValue)],
            default => [null, null, null, null],
        };
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : (string) $value;
    }

    private function numericValue(Attribute $attribute, mixed $value): float
    {
        if (!is_numeric($value)) {
            throw ValidationException::withMessages([
                "attribute_values.{$attribute->id}" => "Поле {$attribute->name} должно быть числом",
            ]);
        }

        return (float) $value;
    }

    private function booleanValue(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    private function selectValue(Attribute $attribute, mixed $value): string
    {
        $options = $attribute->options ?? [];
        if (! in_array($value, $options, true)) {
            throw ValidationException::withMessages([
                "attribute_values.{$attribute->id}" => "Некорректное значение для {$attribute->name}",
            ]);
        }

        return (string) $value;
    }

    private function multiSelectValue(Attribute $attribute, mixed $value): array
    {
        $options = $attribute->options ?? [];
        $values = is_array($value) ? $value : (array) $value;
        $invalid = array_diff($values, $options);
        if ($invalid) {
            throw ValidationException::withMessages([
                "attribute_values.{$attribute->id}" => "Некорректное значение для {$attribute->name}",
            ]);
        }

        return array_values($values);
    }

    private function syncImages(Product $product, array $imageUrls): void
    {
        $product->images()->delete();
        $order = 0;
        foreach ($imageUrls as $url) {
            if (! $url) {
                continue;
            }

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $url,
                'sort_order' => $order++,
            ]);
        }
    }
}

