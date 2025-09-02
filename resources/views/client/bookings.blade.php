<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Bookings</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      @if(session('success'))
        <div class="mb-4 rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
      @endif
      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($bookings as $b)
              <tr>
                <td class="px-6 py-4 text-sm text-gray-900">#{{ $b->id }}</td>
                <td class="px-6 py-4 text-sm text-emerald-700"><a href="{{ route('jobs.show', $b->job?->slug ?? '') }}" class="hover:underline">{{ $b->job?->name ?? '—' }}</a></td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $b->package?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $b->quantity }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $b->start_date ? \Carbon\Carbon::parse($b->start_date)->format('Y-m-d') : '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ number_format($b->total_amount, 2) }} {{ $b->currency }}</td>
                <td class="px-6 py-4 text-sm">
                  @php($badge = match($b->status){
                    'pending' => 'bg-amber-100 text-amber-800',
                    'partially_paid' => 'bg-blue-100 text-blue-800',
                    'paid' => 'bg-emerald-100 text-emerald-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                    default => 'bg-slate-100 text-slate-800',
                  })
                  <span class="px-2 py-1 text-xs rounded {{ $badge }}">{{ ucfirst($b->status) }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $b->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-6 py-4 text-sm text-right">
                  @if($b->status !== 'paid')
                    <a href="{{ route('client.bookings.checkout', $b) }}" class="inline-block mr-2 rounded border border-emerald-600 px-3 py-1.5 text-emerald-700 text-sm hover:bg-emerald-50">Checkout</a>
                  @else
                    <span class="text-xs text-gray-500">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-6 py-6 text-sm text-gray-500">You have no bookings yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $bookings->links() }}</div>
    </div>
  </div>
</x-app-layout>
