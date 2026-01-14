<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Характеристики
            </h2>
            <a href="{{ route('admin.attributes.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Добавить
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категории</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обяз.</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Фильтр</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($attributes as $attribute)
                                <tr>
                                    <td class="px-4 py-2">{{ $attribute->name }}</td>
                                    <td class="px-4 py-2">{{ $attribute->type }}</td>
                                    <td class="px-4 py-2">
                                        @if ($attribute->categories->isEmpty())
                                            <span class="text-gray-500">—</span>
                                        @else
                                            <div class="space-y-1">
                                                @foreach ($attribute->categories as $category)
                                                    <div class="text-sm">
                                                        {{ $category->name }}
                                                        @if ($category->pivot?->is_required)
                                                            <span class="text-xs text-red-600">(обяз.)</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $attribute->is_required ? 'Да' : 'Нет' }}</td>
                                    <td class="px-4 py-2">{{ $attribute->is_filterable ? 'Да' : 'Нет' }}</td>
                                    <td class="px-4 py-2 text-right space-x-2">
                                        <a href="{{ route('admin.attributes.edit', $attribute) }}" class="text-indigo-600 hover:underline">Редактировать</a>
                                        <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline" onclick="return confirm('Удалить характеристику?')">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Характеристик нет</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $attributes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

