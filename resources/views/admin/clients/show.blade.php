<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Client Details</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                @php($__category = (strtolower((string)($profile?->status ?? '')) === 'confirmed') ? 'Confirmed' : 'New')
                @php($__badge = $__category === 'Confirmed' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200')
                <span class="text-sm text-gray-600">Category:</span>
                <span class="text-xs font-medium px-2 py-1 rounded border {{ $__badge }}">{{ $__category }}</span>
            </div>
            @include('partials.client_detail', ['context' => 'admin'])
        </div>
    </div>
</x-app-layout>
