<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($products->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Товаров пока нет.
                    </div>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden border">
                            @if ($product->images->isNotEmpty())
                                <img src="{{ $product->images->first()->path }}" alt="{{ $product->title }}" class="w-full h-40 object-cover">
                            @endif
                            <div class="p-4 space-y-2">
                                <div class="text-sm text-gray-500">{{ $product->category?->name ?? 'Без категории' }}</div>
                                <div class="text-lg font-semibold">
                                    <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->title }}</a>
                                </div>
                                <div class="text-gray-800 font-bold">
                                    {{ number_format($product->price, 2) }} {{ $product->currency }}
                                </div>
                                <div class="text-sm text-gray-600">Продавец: {{ $product->user?->name ?? '—' }}</div>
                                <div class="text-xs uppercase {{ $product->status === 'published' ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $product->status }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
