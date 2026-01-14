<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttributeController extends Controller
{
    public function index(): View
    {
        $attributes = Attribute::orderBy('name')->paginate(20);

        return view('admin.attributes.index', compact('attributes'));
    }

    public function create(): View
    {
        return view('admin.attributes.create', ['attribute' => new Attribute()]);
    }

    public function store(StoreAttributeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->normalizeOptions($data);

        Attribute::create($data);

        return redirect()->route('admin.attributes.index')->with('status', 'Характеристика создана');
    }

    public function edit(Attribute $attribute): View
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute): RedirectResponse
    {
        $data = $request->validated();
        $this->normalizeOptions($data);

        $attribute->update($data);

        return redirect()->route('admin.attributes.index')->with('status', 'Характеристика обновлена');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('status', 'Характеристика удалена');
    }

    private function normalizeOptions(array &$data): void
    {
        if (in_array($data['type'], ['select', 'multiselect'], true)) {
            $options = $data['options'] ?? [];
            $data['options'] = array_values(array_filter($options, fn ($opt) => $opt !== null && $opt !== ''));
        } else {
            $data['options'] = null;
        }

        $data['is_required'] = (bool)($data['is_required'] ?? false);
        $data['is_filterable'] = (bool)($data['is_filterable'] ?? false);
    }
}

