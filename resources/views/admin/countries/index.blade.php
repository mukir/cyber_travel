<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Countries</h2>
      <a href="{{ route('admin.countries.create') }}" class="rounded bg-emerald-600 px-4 py-2 text-white text-sm font-semibold">Add Country</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if(session('success'))<div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>@endif

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <form method="GET" action="{{ route('admin.countries.index') }}" class="flex gap-3 text-sm items-end">
          <div>
            <label class="block text-gray-600">Search</label>
            <input type="text" name="q" value="{{ $q }}" class="mt-1 rounded border p-2" placeholder="Name or code" />
          </div>
          <div>
            <label class="block text-gray-600">Region</label>
            @php($r = $region)
            <select name="region" class="mt-1 rounded border p-2">
              <option value="">All</option>
              <option value="europe" @selected($r==='europe')>Europe</option>
              <option value="gulf" @selected($r==='gulf')>Gulf</option>
              <option value="americas" @selected($r==='americas')>Americas</option>
              <option value="other" @selected($r==='other')>Other</option>
            </select>
          </div>
          <button class="rounded bg-slate-700 px-4 py-2 text-white font-semibold">Filter</button>
          <a href="{{ route('admin.countries.index') }}" class="px-3 py-2 rounded border">Clear</a>
        </form>
      </div>

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Region</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($countries as $c)
              <tr>
                <td class="px-6 py-3">{{ $c->name }}</td>
                <td class="px-6 py-3 uppercase">{{ $c->code }}</td>
                <td class="px-6 py-3 capitalize">{{ $c->region ?: '—' }}</td>
                <td class="px-6 py-3 text-right">
                  <a href="{{ route('admin.countries.edit', $c) }}" class="underline">Edit</a>
                  <form action="{{ route('admin.countries.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Delete this country?')">
                    @csrf @method('DELETE')
                    <button class="underline text-red-600 ml-2">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-6 py-6 text-gray-500">No countries found.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="px-6 py-4">{{ $countries->links() }}</div>
      </div>
    </div>
  </div>
</x-app-layout>

