<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Новая характеристика
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.attributes.store') }}">
                    @include('admin.attributes._form', ['submitLabel' => 'Создать'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

