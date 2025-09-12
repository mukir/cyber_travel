<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <form method="GET" action="{{ route('admin.clients') }}" class="flex items-center gap-2">
                        <label class="text-sm text-gray-700">Filter by Staff:</label>
                        <select name="rep" class="rounded border p-1 text-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="unassigned" {{ (isset($selectedRep) && $selectedRep==='unassigned') ? 'selected' : '' }}>Unassigned</option>
                            @foreach(($staff ?? []) as $rep)
                                <option value="{{ $rep->id }}" {{ (isset($selectedRep) && (string)$selectedRep===(string)$rep->id) ? 'selected' : '' }}>{{ $rep->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('admin.clients.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Client</a>
                </div>

                <form method="POST" action="{{ route('admin.clients.assign') }}" class="space-y-3">
                    @csrf
                    <div class="flex items-center gap-2 mb-2">
                        <label class="text-sm text-gray-700">Assign selected to:</label>
                        <select name="assign_to" class="rounded border p-1 text-sm" required>
                            @foreach(($staff ?? []) as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                            @endforeach
                        </select>
                        <button class="rounded bg-emerald-600 px-3 py-1.5 text-white text-sm">Assign</button>
                    </div>
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left"><input type="checkbox" id="select-all"></th>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2 text-left">Category</th>
                            <th class="px-4 py-2 text-left">Assigned To</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr class="border-t align-top">
                                <td class="px-4 py-2"><input type="checkbox" name="client_ids[]" value="{{ $client->id }}" class="row-check"></td>
                                <td class="px-4 py-2">{{ $client->name }}</td>
                                <td class="px-4 py-2">{{ $client->email }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $profile = \App\Models\ClientProfile::where('user_id', $client->id)->first();
                                    @endphp
                                    {{ $profile?->phone ?: '—' }}
                                </td>
                                <td class="px-4 py-2">
                                    @php
                                        $category = (strtolower((string)($profile?->status ?? '')) === 'confirmed') ? 'Confirmed' : 'New';
                                        $badge = $category === 'Confirmed' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200';
                                    @endphp
                                    <span class="text-xs font-medium px-2 py-1 rounded border {{ $badge }}">{{ $category }}</span>
                                </td>
                                <td class="px-4 py-2">{{ optional(\App\Models\User::find($profile?->sales_rep_id))->name ?: '—' }}</td>
                                <td class="px-4 py-2 space-x-3 whitespace-nowrap">
                                    <a href="{{ route('admin.clients.show', $client) }}" class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-2">No clients found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </form>
                <script>
                  document.getElementById('select-all')?.addEventListener('change', function(){
                    document.querySelectorAll('.row-check').forEach(cb => { cb.checked = this.checked; });
                  });
                </script>
            </div>
        </div>
    </div>
</x-app-layout>

