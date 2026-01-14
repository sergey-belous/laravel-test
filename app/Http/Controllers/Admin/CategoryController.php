<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('parent')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(),
            'categories' => Category::orderBy('name')->get(),
            'attributes' => Attribute::orderBy('name')->get(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = Category::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'parent_id' => $data['parent_id'] ?? null,
            'position' => $data['position'] ?? 0,
        ]);

        $this->syncAttributes($category, $data['attributes'] ?? []);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Категория создана');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category->load('attributes'),
            'categories' => Category::whereKeyNot($category->id)->orderBy('name')->get(),
            'attributes' => Attribute::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        $category->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'parent_id' => $data['parent_id'] ?? null,
            'position' => $data['position'] ?? 0,
        ]);

        $this->syncAttributes($category, $data['attributes'] ?? []);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Категория обновлена');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Категория удалена');
    }

    private function syncAttributes(Category $category, array $attributes): void
    {
        $syncData = [];
        $order = 0;

        foreach ($attributes as $attributeId => $data) {
            $selected = (bool)($data['selected'] ?? false);
            if (! $selected) {
                continue;
            }

            $syncData[$attributeId] = [
                'is_required' => (bool)($data['is_required'] ?? false),
                'display_order' => $order++,
            ];
        }

        $category->attributes()->sync($syncData);
    }
}

