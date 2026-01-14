<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Новая карточка товара
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="GET" action="{{ route('products.create') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <x-input-label for="category_id" value="Категория" />
                        <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Выберите категорию</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-primary-button>Загрузить характеристики</x-primary-button>
                    </div>
                </form>
            </div>

            @if ($selectedCategory)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $selectedCategory->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="title" value="Название товара" />
                                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="price" value="Цена" />
                                <div class="flex space-x-2">
                                    <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price')" required />
                                    <x-text-input id="currency" name="currency" type="text" class="mt-1 block w-20" :value="old('currency', 'USD')" required />
                                </div>
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="stock" value="Остаток" />
                                <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full" :value="old('stock', 0)" />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" value="Статус" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="draft" @selected(old('status') === 'draft')>Черновик</option>
                                    <option value="published" @selected(old('status') === 'published')>Опубликован</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" value="Описание" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-6 space-y-4">
                            <h3 class="text-lg font-semibold">Характеристики категории: {{ $selectedCategory->name }}</h3>
                            @forelse ($attributes as $attribute)
                                <div class="border rounded p-4 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium">{{ $attribute->name }}</div>
                                            <div class="text-sm text-gray-500">Тип: {{ $attribute->type }} {{ $attribute->unit ? '(' . $attribute->unit . ')' : '' }}</div>
                                        </div>
                                        @if (($attribute->pivot->is_required ?? false) || $attribute->is_required)
                                            <span class="text-xs text-red-600 uppercase">Обязательное</span>
                                        @endif
                                    </div>

                                    <div>
                                        @include('products.partials.attribute-field', [
                                            'attribute' => $attribute,
                                        ])
                                        <x-input-error :messages="$errors->get('attribute_values.' . $attribute->id)" class="mt-1" />
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-500">Для категории нет характеристик.</div>
                            @endforelse
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-semibold">Изображения (URL)</h3>
                            <div class="space-y-2">
                                @for ($i = 0; $i < 3; $i++)
                                    <x-text-input name="image_urls[]" type="text" class="block w-full" :value="old('image_urls.' . $i)" placeholder="https://example.com/image{{ $i + 1 }}.jpg" />
                                @endfor
                            </div>
                            <x-input-error :messages="$errors->get('image_urls.*')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-primary-button>Сохранить товар</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

