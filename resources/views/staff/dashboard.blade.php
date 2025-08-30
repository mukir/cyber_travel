<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">My Leads</div>
                    <div class="text-2xl font-semibold">{{ $leadsCount ?? 0 }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Due Follow-ups</div>
                    <div class="text-2xl font-semibold">{{ $dueCount ?? 0 }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">This Month Commission</div>
                    <div class="text-2xl font-semibold">{{ number_format($commissionMonth ?? 0, 2) }}</div>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Conversions (Won)</div>
                    <div class="text-2xl font-semibold">{{ $wonCount ?? 0 }}</div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Target vs Achievement</h3>
                <div class="mt-3 text-sm">Period: {{ $targetPeriod ?? '—' }}</div>
                <div class="mt-2 w-full h-4 bg-gray-200 rounded">
                    @php($pct = min(100, max(0, round(($achieved ?? 0) * 100 / max(1, ($target ?? 1))))))
                    <div class="h-4 bg-emerald-600 rounded" style="width: {{ $pct }}%"></div>
                </div>
                <div class="mt-2 text-sm">{{ number_format($achieved ?? 0, 2) }} / {{ number_format($target ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
