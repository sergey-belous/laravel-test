@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Название" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="slug" value="Slug" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $category->slug)" required />
        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="parent_id" value="Родительская категория" />
        <select name="parent_id" id="parent_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Без родителя</option>
            @foreach ($categories as $item)
                <option value="{{ $item->id }}" @selected(old('parent_id', $category->parent_id) == $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="position" value="Позиция" />
        <x-text-input id="position" name="position" type="number" min="0" class="mt-1 block w-full" :value="old('position', $category->position ?? 0)" />
        <x-input-error :messages="$errors->get('position')" class="mt-2" />
    </div>
</div>

<div class="mt-6">
    <h3 class="font-semibold mb-2">Характеристики категории</h3>
    <div class="space-y-2">
        @foreach ($attributes as $attribute)
            @php
                $pivot = $category->attributes->firstWhere('id', $attribute->id)?->pivot;
            @endphp
            <div class="flex items-center space-x-3 border rounded p-3">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="attributes[{{ $attribute->id }}][selected]" value="1" @checked(old("attributes.{$attribute->id}.selected", $pivot?->is_required !== null)) />
                    <span>{{ $attribute->name }} ({{ $attribute->type }})</span>
                </div>
                <label class="flex items-center space-x-1">
                    <input type="checkbox" name="attributes[{{ $attribute->id }}][is_required]" value="1" @checked(old("attributes.{$attribute->id}.is_required", $pivot?->is_required)) />
                    <span class="text-sm text-gray-600">Обязательное</span>
                </label>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-6">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
</div>

