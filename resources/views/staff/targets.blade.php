<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Target vs Achievement</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6 text-sm">
                @if($target)
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-gray-600">Period</div>
                            <div class="font-semibold">{{ $target->start_date->format('Y-m-d') }} – {{ $target->end_date->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-500 capitalize mt-1">{{ $target->period }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Target amount</div>
                            <div class="font-semibold">{{ number_format($target->target_amount, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Achieved</div>
                            <div class="font-semibold">{{ number_format($achieved, 2) }}</div>
                        </div>
                    </div>
                    @php($pct = min(100, max(0, round(($achieved ?? 0) * 100 / max(1, ($target->target_amount ?? 1))))))
                    <div class="mt-4 w-full h-4 bg-gray-200 rounded">
                        <div class="h-4 bg-emerald-600 rounded" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="mt-2 text-sm">Progress: {{ $pct }}%</div>
                @else
                    <div>No active target set for the current period.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
