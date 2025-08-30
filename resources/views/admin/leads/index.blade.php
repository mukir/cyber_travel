<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leads</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <form method="GET" class="text-sm flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-gray-600">Sales Rep</label>
                            <select name="staff_id" class="mt-1 rounded border p-2">
                                <option value="">All</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" @selected(request('staff_id')==$s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600">Stage</label>
                            <select name="stage" class="mt-1 rounded border p-2">
                                <option value="">All</option>
                                @foreach(['new','contacted','qualified','won','lost'] as $s)
                                    <option value="{{ $s }}" @selected(request('stage')===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600">Status</label>
                            <select name="status" class="mt-1 rounded border p-2">
                                <option value="">All</option>
                                @foreach(['open','closed'] as $s)
                                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="rounded bg-slate-700 px-4 py-2 text-white font-semibold">Filter</button>
                    </form>
                    <a href="{{ route('admin.leads.create') }}" class="rounded bg-emerald-600 px-4 py-2 text-white text-sm font-semibold">Create Lead</a>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Rep</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage / Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Follow-up</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($leads as $lead)
                            <tr>
                                <td class="px-6 py-3">{{ $lead->name }}</td>
                                <td class="px-6 py-3">
                                    <div>{{ $lead->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $lead->phone }}</div>
                                </td>
                                <td class="px-6 py-3">{{ optional($lead->salesRep)->name }}</td>
                                <td class="px-6 py-3 capitalize">{{ $lead->stage }} / {{ $lead->status }}</td>
                                <td class="px-6 py-3">{{ $lead->next_follow_up ? $lead->next_follow_up->format('Y-m-d') : '-' }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.leads.edit', $lead) }}" class="underline">Edit</a>
                                    <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('Delete this lead?')">
                                        @csrf @method('DELETE')
                                        <button class="underline text-red-600 ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-gray-500">No leads found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $leads->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

