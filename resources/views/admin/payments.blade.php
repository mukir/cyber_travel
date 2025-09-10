<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payments & Invoicing</h2>
      <div class="text-sm text-gray-600">Today: <span class="font-semibold">{{ number_format($today, 2) }}</span> • This month: <span class="font-semibold">{{ number_format($month, 2) }}</span></div>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
      <div class="bg-white p-4 shadow sm:rounded-lg">
        <form method="GET" class="grid md:grid-cols-5 gap-3 text-sm">
          <div>
            <label class="block text-gray-600">Method</label>
            <select name="method" class="mt-1 w-full rounded border p-2">
              <option value="">All</option>
              <option value="mpesa" @selected(request('method')==='mpesa')>M-PESA</option>
              <option value="paypal" @selected(request('method')==='paypal')>PayPal</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-600">Status</label>
            <select name="status" class="mt-1 w-full rounded border p-2">
              <option value="">All</option>
              <option value="pending" @selected(request('status')==='pending')>Pending</option>
              <option value="paid" @selected(request('status')==='paid')>Paid</option>
              <option value="failed" @selected(request('status')==='failed')>Failed</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-600">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="mt-1 w-full rounded border p-2" />
          </div>
          <div>
            <label class="block text-gray-600">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="mt-1 w-full rounded border p-2" />
          </div>
          <div class="flex items-end gap-2">
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Filter</button>
            <a href="{{ route('admin.payments.export.csv', request()->query()) }}" class="rounded border px-3 py-2 text-sm">Export CSV</a>
            <a href="{{ route('admin.payments.export.pdf', request()->query()) }}" class="rounded border px-3 py-2 text-sm">Export PDF</a>
            <a href="{{ route('admin.payments.overdue') }}" class="ml-auto text-amber-700 hover:underline">Overdue & Reminders</a>
          </div>
        </form>
      </div>

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($payments as $p)
              <tr>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $p->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-6 py-4 text-sm text-emerald-700">BK{{ $p->booking_id }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $p->booking?->customer_name ?? '—' }}</td>
                <td class="px-6 py-4 text-sm capitalize">{{ $p->method }}</td>
                <td class="px-6 py-4 text-sm">
                  @php($badge = match($p->status){
                    'pending' => 'bg-amber-100 text-amber-800',
                    'paid' => 'bg-emerald-100 text-emerald-800',
                    'failed' => 'bg-red-100 text-red-800',
                    default => 'bg-slate-100 text-slate-800',
                  })
                  <span class="px-2 py-1 text-xs rounded {{ $badge }}">{{ ucfirst($p->status) }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-right font-medium">{{ number_format($p->amount, 2) }}</td>
                @php($outstanding = $p->booking ? max(((float)$p->booking->total_amount) - ((float)$p->booking->amount_paid), 0) : 0)
                <td class="px-6 py-4 text-sm text-right">{{ number_format($outstanding, 2) }} {{ $p->booking?->currency }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $p->receipt_number ?? $p->reference }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-6 py-6 text-sm text-gray-500">No payments found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div>{{ $payments->links() }}</div>
    </div>
  </div>
</x-app-layout>
