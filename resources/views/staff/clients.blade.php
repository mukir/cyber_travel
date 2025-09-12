<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Clients</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        @php($requiredDocs = \App\Models\DocumentType::where('active', true)->where('required', true)->pluck('key')->all())
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
              <th class="px-4 py-2 text-left">Category</th>
              <th class="px-4 py-2 text-left">Latest Application</th>
              <th class="px-4 py-2 text-left">Outstanding</th>
              <th class="px-4 py-2 text-left">Docs Missing</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @if(($clients ?? collect())->count())
              @foreach($clients as $c)
                @php($p = $profiles->get($c->id))
                @php($b = optional($latestBookings->get($c->id))->first())
                @php($balance = $b ? max(((float)$b->total_amount) - ((float)$b->amount_paid), 0) : null)
                @php($docs = \App\Models\ClientDocument::where('user_id', $c->id)->pluck('type')->all())
                @php($missing = array_values(array_diff($requiredDocs, $docs)))
                <tr class="border-t align-top">
                  <td class="px-4 py-2">{{ $c->name }}</td>
                  <td class="px-4 py-2">{{ $c->email }}</td>
                  <td class="px-4 py-2">{{ $p?->phone ?: '—' }}</td>
                  <td class="px-4 py-2">
                    @php
                      $category = (strtolower((string)($p?->status ?? '')) === 'confirmed') ? 'Confirmed' : 'New';
                      $badge = $category === 'Confirmed' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200';
                    @endphp
                    <span class="text-xs font-medium px-2 py-1 rounded border {{ $badge }}">{{ $category }}</span>
                  </td>
                  <td class="px-4 py-2">{{ optional($b?->job)->name ?: '—' }}</td>
                  <td class="px-4 py-2">{{ $b ? (number_format($balance, 2).' '.$b->currency) : '—' }}</td>
                  <td class="px-4 py-2">{{ count($missing) }}</td>
                  <td class="px-4 py-2 whitespace-nowrap text-sm">
                    <a href="{{ route('staff.clients.show', $c) }}" class="text-indigo-700 hover:underline">View</a>
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="8" class="px-4 py-4 text-gray-500">No assigned clients yet.</td>
              </tr>
            @endif
          </tbody>
        </table>
        <div class="mt-4">{{ $clients->links() }}</div>
      </div>
    </div>
  </div>
</x-app-layout>
