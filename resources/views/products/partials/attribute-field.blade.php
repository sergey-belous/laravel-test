@php
    $name = "attribute_values.{$attribute->id}";
    $oldValue = old(str_replace('.', '_', $name));
@endphp

@switch($attribute->type)
    @case('text')
        <x-text-input name="attribute_values[{{ $attribute->id }}]" type="text" class="block w-full" :value="$oldValue" />
        @break
    @case('number')
        <x-text-input name="attribute_values[{{ $attribute->id }}]" type="number" step="0.01" class="block w-full" :value="$oldValue" />
        @break
    @case('boolean')
        <input type="hidden" name="attribute_values[{{ $attribute->id }}]" value="0">
        <label class="inline-flex items-center space-x-2">
            <input type="checkbox" name="attribute_values[{{ $attribute->id }}]" value="1" @checked(old("attribute_values.{$attribute->id}", false))>
            <span>Да</span>
        </label>
        @break
    @case('select')
        <select name="attribute_values[{{ $attribute->id }}]" class="block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Выберите...</option>
            @foreach (($attribute->options ?? []) as $option)
                <option value="{{ $option }}" @selected(old("attribute_values.{$attribute->id}") == $option)>{{ $option }}</option>
            @endforeach
        </select>
        @break
    @case('multiselect')
        <select name="attribute_values[{{ $attribute->id }}][]" multiple class="block w-full border-gray-300 rounded-md shadow-sm">
            @foreach (($attribute->options ?? []) as $option)
                <option value="{{ $option }}" @selected(collect(old("attribute_values.{$attribute->id}", []))->contains($option))>{{ $option }}</option>
            @endforeach
        </select>
        @break
    @default
        <x-text-input name="attribute_values[{{ $attribute->id }}]" type="text" class="block w-full" :value="$oldValue" />
@endswitch

