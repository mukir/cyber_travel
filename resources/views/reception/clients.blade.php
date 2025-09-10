<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Client Status</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
      <div class="bg-white p-4 shadow sm:rounded-lg">
        <form method="GET" class="flex gap-2 text-sm">
          <input type="text" name="q" value="{{ $q }}" placeholder="Search name, email, phone, ID" class="rounded border p-2 w-72" />
          <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Search</button>
          @if(!empty($q))<a href="{{ route('reception.clients') }}" class="px-3 py-2 rounded border">Clear</a>@endif
        </form>
      </div>

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Staff</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($clients as $c)
              <tr>
                <td class="px-6 py-3">
                  <div class="font-medium">{{ $c->name }}</div>
                  <div class="text-xs text-gray-500">{{ $c->email }} &middot; {{ $c->phone }}</div>
                </td>
                <td class="px-6 py-3">{{ $c->status ?? '-' }}</td>
                <td class="px-6 py-3">
                  @php($staff = optional($c->salesRep))
                  @if($staff)
                    <div>{{ $staff->name }}</div>
                    <div class="text-xs text-gray-500">{{ $staff->email }} @if($staff->phone)&middot; {{ $staff->phone }}@endif</div>
                  @else
                    <span class="text-gray-500">Unassigned</span>
                  @endif
                </td>
                <td class="px-6 py-3 text-right">
                  @if($staff)
                    <a href="mailto:{{ $staff->email }}" class="underline text-indigo-700">Email Staff</a>
                    @if($staff->phone)
                      <a href="tel:{{ $staff->phone }}" class="underline text-indigo-700 ml-2">Call Staff</a>
                    @endif
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-6 py-4 text-gray-500">No clients found.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="px-6 py-4">{{ $clients->links() }}</div>
      </div>
    </div>
  </div>
</x-app-layout>

