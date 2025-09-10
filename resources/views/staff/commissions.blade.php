<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Commission Tracker</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-gray-600">Default rate</div>
                        <div class="text-xl font-semibold">{{ number_format($rate, 2) }}%</div>
                    </div>
                    <div class="text-right">
                        <div class="text-gray-600">Total payments (referred)</div>
                        <div class="text-xl font-semibold">{{ number_format($total, 2) }}</div>
                        <div class="text-gray-600 mt-2">Total commission (all rules)</div>
                        <div class="text-xl font-semibold">{{ number_format($commission, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b text-sm font-semibold">Recent payments</div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse(($payments ?? []) as $p)
                            <?php $comm = \App\Models\Commission::where('payment_id', $p->id)->first(); ?>
                            <tr>
                                <td class="px-6 py-3">{{ $p->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-3">BK{{ $p->booking_id }}</td>
                                <td class="px-6 py-3">{{ optional($p->booking)->customer_name }}</td>
                                <td class="px-6 py-3 uppercase">{{ $p->method }}</td>
                                <td class="px-6 py-3">{{ number_format($p->amount, 2) }}</td>
                                <td class="px-6 py-3">
                                    @if($comm)
                                        {{ number_format($comm->amount, 2) }}
                                        <span class="text-xs text-gray-500">
                                            {{ $comm->type === 'region_fixed' ? 'fixed' : (number_format($comm->rate, 2).'%' ) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-gray-500">No payments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if(isset($payments))
                    <div class="px-6 py-4">{{ $payments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
