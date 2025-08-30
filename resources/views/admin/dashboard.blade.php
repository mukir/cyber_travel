<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Clients</div>
                    <div class="text-2xl font-semibold">{{ $clients }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Pending Documents</div>
                    <div class="text-2xl font-semibold">{{ $pendingDocs }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Payments Today</div>
                    <div class="text-2xl font-semibold">{{ number_format($paymentsToday, 2) }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Payments This Month</div>
                    <div class="text-2xl font-semibold">{{ number_format($paymentsMonth, 2) }}</div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Payments (Last 7 days)</h3>
                <div class="mt-4">
                    <div class="grid grid-cols-7 gap-2 text-xs text-gray-600">
                        @foreach($daily as $d)
                            <div class="text-center">
                                <div class="h-24 flex items-end">
                                    @php($h = min(96, max(2, round(($d['amount'] ?? 0) * 96 / max(1, collect($daily)->max('amount'))))))
                                    <div class="w-6 bg-emerald-500 mx-auto" style="height: {{ $h }}px;"></div>
                                </div>
                                <div class="mt-1">{{ \Carbon\Carbon::parse($d['date'])->format('d M') }}</div>
                                <div class="text-[10px] text-gray-500">{{ number_format($d['amount'], 0) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
