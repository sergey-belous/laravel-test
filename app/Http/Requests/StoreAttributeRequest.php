<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttributeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $options = $this->input('options', []);
        if (is_array($options)) {
            $options = array_values(array_filter($options, fn ($opt) => $opt !== null && $opt !== ''));
        }

        $this->merge([
            'options' => $options,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('attributes', 'slug')],
            'type' => ['required', Rule::in(['text', 'number', 'boolean', 'select', 'multiselect'])],
            'unit' => ['nullable', 'string', 'max:50'],
            'is_required' => ['sometimes', 'boolean'],
            'is_filterable' => ['sometimes', 'boolean'],
            'options' => ['nullable', 'array', 'required_if:type,select,multiselect', 'min:1'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'categories' => ['nullable', 'array'],
            'categories.*.selected' => ['nullable', 'boolean'],
            'categories.*.is_required' => ['nullable', 'boolean'],
        ];
    }
}

