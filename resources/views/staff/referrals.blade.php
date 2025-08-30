<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Referral Tracking</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="text-sm">
                        <div class="text-gray-600">Your referral link</div>
                        <div class="mt-1 font-mono break-all">{{ $link }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="copyBtn" class="rounded bg-emerald-600 px-4 py-2 text-white text-sm font-semibold">Copy</button>
                        <span id="copyMsg" class="text-xs text-gray-500"></span>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-700">
                    Total referred bookings: <span class="font-semibold">{{ $referredCount }}</span>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b text-sm font-semibold">Recent referred bookings</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        @forelse(($bookings ?? []) as $b)
                            <tr>
                                <td class="px-6 py-3">BK{{ $b->id }}</td>
                                <td class="px-6 py-3">
                                    <div>{{ $b->customer_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $b->customer_email }}</div>
                                </td>
                                <td class="px-6 py-3">{{ optional($b->job)->name }} @if($b->package) <span class="text-xs text-gray-500">/ {{ $b->package->name }}</span> @endif</td>
                                <td class="px-6 py-3">{{ number_format($b->total_amount, 2) }} {{ $b->currency }}</td>
                                <td class="px-6 py-3">{{ number_format($b->amount_paid, 2) }} {{ $b->currency }}</td>
                                <td class="px-6 py-3 capitalize">{{ $b->payment_status ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-gray-500">No referred bookings yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if(isset($bookings))
                    <div class="px-6 py-4">{{ $bookings->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('copyBtn')?.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(@json($link));
                const el = document.getElementById('copyMsg');
                if (el) { el.textContent = 'Copied!'; setTimeout(()=> el.textContent='', 1500); }
            } catch (_) {}
        });
    </script>
</x-app-layout>
