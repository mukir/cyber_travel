<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Clients</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <form method="GET" action="{{ route('staff.clients') }}" class="mb-4 flex items-center gap-2">
          <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name, email or phone" class="rounded border p-2 w-64" />
          <button class="rounded bg-emerald-600 px-3 py-2 text-white text-sm">Search</button>
          @if(!empty($q))
            <a href="{{ route('staff.clients') }}" class="text-sm text-gray-600">Clear</a>
          @endif
        </form>
        <table class="w-full table-auto">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-4 py-2 text-left">Name</th>
              <th class="px-4 py-2 text-left">Email</th>
              <th class="px-4 py-2 text-left">Phone</th>
              <th class="px-4 py-2 text-left">Latest Application</th>
              <th class="px-4 py-2 text-left">Balance</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @forelse($clients as $c)
              @php($p = $profiles->get($c->id))
              @php($b = optional($latestBookings->get($c->id))->first())
              @php($balance = $b ? max(((float)$b->total_amount) - ((float)$b->amount_paid), 0) : null)
              <tr class="border-t align-top">
                <td class="px-4 py-2">{{ $c->name }}</td>
                <td class="px-4 py-2">{{ $c->email }}</td>
                <td class="px-4 py-2">{{ $p?->phone ?: '—' }}</td>
                <td class="px-4 py-2">{{ optional($b?->job)->name ?: '—' }}</td>
                <td class="px-4 py-2">{{ $b ? (number_format($balance, 2).' '.$b->currency) : '—' }}</td>
                <td class="px-4 py-2 whitespace-nowrap text-sm">
                  @php($waDigits = \App\Helpers\Phone::toE164Digits($p?->phone))
                  @if($b)
                    <a href="{{ route('client.applications.checkout', $b) }}" class="text-emerald-700 hover:underline mr-2">Checkout</a>
                  @endif
                  @if($waDigits)
                    <a href="https://wa.me/{{ $waDigits }}" target="_blank" rel="noopener" class="text-emerald-700 hover:underline mr-2">WhatsApp</a>
                  @endif
                  @if(!empty($p?->phone))
                    <a href="tel:{{ $p->phone }}" class="text-sky-700 hover:underline">Call</a>
                  @endif
                  @if(!$b && empty($waDigits) && empty($p?->phone))
                    <span class="text-gray-400">No booking</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="px-4 py-4 text-gray-500">No assigned clients yet.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-4">{{ $clients->links() }}</div>
      </div>
    </div>
  </div>
</x-app-layout>
