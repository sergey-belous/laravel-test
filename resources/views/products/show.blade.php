<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-2xl font-semibold">{{ $product->title }}</div>
                        <div class="text-gray-600 text-sm">Категория: {{ $product->category?->name }}</div>
                        <div class="text-gray-600 text-sm">Продавец: {{ $product->user?->name }}</div>
                    </div>
                    <div class="text-xl font-bold">
                        {{ number_format($product->price, 2) }} {{ $product->currency }}
                    </div>
                </div>

                @if ($product->images->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($product->images as $image)
                            <div class="border rounded overflow-hidden">
                                <img src="{{ $image->path }}" alt="{{ $image->alt ?? $product->title }}" class="w-full h-40 object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($product->description)
                    <div>
                        <h3 class="font-semibold mb-2">Описание</h3>
                        <p class="text-gray-800 whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                @endif

                <div>
                    <h3 class="font-semibold mb-2">Характеристики</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach ($product->attributeValues as $value)
                            <div class="flex justify-between bg-gray-50 p-3 rounded">
                                <span>{{ $value->attribute?->name }}</span>
                                <span class="font-medium">
                                    @php
                                        $attribute = $value->attribute;
                                        $unit = $attribute?->unit ? ' '.$attribute->unit : '';
                                    @endphp
                                    @switch($attribute?->type)
                                        @case('number')
                                            {{ $value->value_number }}{{ $unit }}
                                            @break
                                        @case('boolean')
                                            {{ $value->value_boolean ? 'Да' : 'Нет' }}
                                            @break
                                        @case('select')
                                            {{ $value->value_text }}{{ $unit }}
                                            @break
                                        @case('multiselect')
                                            {{ implode(', ', $value->value_json ?? []) }}
                                            @break
                                        @default
                                            {{ $value->value_text }}{{ $unit }}
                                    @endswitch
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

