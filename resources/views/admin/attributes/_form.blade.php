@csrf

<div 
    x-data="attributeForm({
        type: @js(old('type', $attribute->type ?? 'text')),
        options: @js(old('options', $attribute->options ?? [])),
    })"
    x-init="init()"
    class="space-y-6"
>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="name" value="Название" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $attribute->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="slug" value="Slug" />
            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $attribute->slug)" required />
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="type" value="Тип" />
            <select name="type" id="type" x-model="type" @change="handleTypeChange" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach (['text' => 'Текст', 'number' => 'Число', 'boolean' => 'Да/Нет', 'select' => 'Селект', 'multiselect' => 'Множественный выбор'] as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('type')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="unit" value="Единица измерения (опционально)" />
            <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full" :value="old('unit', $attribute->unit)" />
            <x-input-error :messages="$errors->get('unit')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <label class="flex items-center space-x-2">
            <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $attribute->is_required))>
            <span>Обязательное</span>
        </label>
        <label class="flex items-center space-x-2">
            <input type="checkbox" name="is_filterable" value="1" @checked(old('is_filterable', $attribute->is_filterable))>
            <span>Доступно в фильтрах</span>
        </label>
    </div>

    <div x-show="showOptions" x-cloak class="space-y-2">
        <div class="flex items-center justify-between">
            <x-input-label value="Опции (для select/multiselect)" />
            <button type="button" class="text-sm text-indigo-600 hover:underline" @click="addOption">Добавить опцию</button>
        </div>
        <template x-for="(option, index) in options" :key="index">
            <div class="flex items-center space-x-2">
                <x-text-input x-bind:name="`options[${index}]`" type="text" class="block w-full" x-model="options[index]" x-bind:placeholder="`Опция ${index + 1}`" />
                <button type="button" class="text-red-600 text-sm hover:underline" @click="removeOption(index)" x-show="options.length > 1">Удалить</button>
            </div>
        </template>
        <x-input-error :messages="collect($errors->get('options.*'))->flatten()->all()" class="mt-1" />
        <x-input-error :messages="$errors->get('options')" class="mt-1" />
    </div>

    <div class="pt-2">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('attributeForm', (initial) => ({
                type: initial.type ?? 'text',
                options: Array.isArray(initial.options) && initial.options.length ? initial.options : [''],
                get showOptions() {
                    return this.type === 'select' || this.type === 'multiselect';
                },
                init() {
                    this.handleTypeChange();
                },
                handleTypeChange() {
                    if (!this.showOptions) {
                        this.options = [''];
                    } else if (this.options.length === 0) {
                        this.options = [''];
                    }
                },
                addOption() {
                    this.options.push('');
                },
                removeOption(index) {
                    if (this.options.length > 1) {
                        this.options.splice(index, 1);
                    }
                },
            }));
        });
    </script>
</div>

