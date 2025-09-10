<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Visitors Book</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        @if(session('success'))
          <div class="mb-3 rounded bg-emerald-100 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="mb-3 rounded bg-red-100 text-red-800 px-4 py-2 text-sm">
            <ul class="list-disc pl-4">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form method="POST" action="{{ route('reception.visitors') }}" class="grid md:grid-cols-5 gap-3 text-sm">
          @csrf
          <div>
            <label class="block text-gray-600">Full Name</label>
            <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded border p-2" required />
          </div>
          <div>
            <label class="block text-gray-600">National ID</label>
            <input name="national_id" value="{{ old('national_id') }}" class="mt-1 w-full rounded border p-2" required />
          </div>
          <div>
            <label class="block text-gray-600">Phone</label>
            <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded border p-2" required />
          </div>
          <div>
            <label class="block text-gray-600">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded border p-2" />
          </div>
          <div class="md:col-span-5">
            <label class="block text-gray-600">Notes (optional)</label>
            <input name="notes" value="{{ old('notes') }}" class="mt-1 w-full rounded border p-2" />
          </div>
          <div class="md:col-span-5 text-right">
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Add Visitor</button>
          </div>
        </form>
      </div>

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
          <form method="GET" action="{{ route('reception.visitors') }}" class="flex gap-2 items-end text-sm">
            <div>
              <label class="block text-gray-600">Search Visitors</label>
              <input type="text" name="q" value="{{ $q ?? '' }}" class="mt-1 rounded border p-2" placeholder="Name, ID, Phone, Email" />
            </div>
            <button class="rounded bg-slate-700 px-4 py-2 text-white font-semibold">Filter</button>
            @if(!empty($q))<a href="{{ route('reception.visitors') }}" class="px-3 py-2 rounded border">Clear</a>@endif
          </form>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">When</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($visitors as $v)
              <tr>
                <td class="px-6 py-3">{{ $v->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-6 py-3">{{ $v->name }}</td>
                <td class="px-6 py-3">{{ $v->national_id }}</td>
                <td class="px-6 py-3">
                  <div>{{ $v->phone }}</div>
                  <div class="text-xs text-gray-500">{{ $v->email ?: '—' }}</div>
                </td>
                <td class="px-6 py-3">{{ $v->notes ?: '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="px-6 py-4 text-gray-500">No visitors yet.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="px-6 py-4">{{ $visitors->links() }}</div>
      </div>
    </div>
  </div>
</x-app-layout>
