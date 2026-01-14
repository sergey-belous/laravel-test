<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published'],
            'image_urls' => ['array'],
            'image_urls.*' => ['nullable', 'string', 'max:500'],
        ];
    }
}

