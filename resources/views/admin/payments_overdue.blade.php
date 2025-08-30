<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Overdue Payments</h2>
      <a href="{{ route('admin.payments') }}" class="text-sm text-emerald-700 hover:underline">Back to Ledger</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif
      <form action="{{ route('admin.payments.reminders') }}" method="POST" class="bg-white p-4 shadow sm:rounded-lg">
        @csrf
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3"><input type="checkbox" onclick="document.querySelectorAll('.rowchk').forEach(c=>c.checked=this.checked)" /></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @forelse($overdue as $b)
                <tr>
                  <td class="px-6 py-4"><input class="rowchk" type="checkbox" name="booking_ids[]" value="{{ $b->id }}" /></td>
                  <td class="px-6 py-4 text-sm">BK{{ $b->id }}</td>
                  <td class="px-6 py-4 text-sm">{{ $b->customer_name }}<div class="text-xs text-gray-500">{{ $b->customer_email }}</div></td>
                  <td class="px-6 py-4 text-sm">{{ $b->start_date ?? $b->created_at->addDays(7)->format('Y-m-d') }}</td>
                  <td class="px-6 py-4 text-sm text-right font-semibold">{{ number_format(max($b->total_amount - $b->amount_paid, 0), 2) }} {{ $b->currency }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-6 py-6 text-sm text-gray-500">No overdue items.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-4 flex items-center justify-between">
          <div>{{ $overdue->links() }}</div>
          <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Send Reminders</button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>

