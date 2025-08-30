<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales Targets</h2>
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
                            <label class="block text-gray-600">Period</label>
                            <select name="period" class="mt-1 rounded border p-2">
                                <option value="">All</option>
                                @foreach(['monthly','quarterly'] as $s)
                                    <option value="{{ $s }}" @selected(request('period')===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="rounded bg-slate-700 px-4 py-2 text-white font-semibold">Filter</button>
                    </form>
                    <a href="{{ route('admin.targets.create') }}" class="rounded bg-emerald-600 px-4 py-2 text-white text-sm font-semibold">Create Target</a>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Rep</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Amount</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($targets as $t)
                            <tr>
                                <td class="px-6 py-3">{{ optional($t->staff)->name }}</td>
                                <td class="px-6 py-3 capitalize">{{ $t->period }}</td>
                                <td class="px-6 py-3">{{ $t->start_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-3">{{ $t->end_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-3">{{ number_format($t->target_amount, 2) }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.targets.edit', $t) }}" class="underline">Edit</a>
                                    <form action="{{ route('admin.targets.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Delete this target?')">
                                        @csrf @method('DELETE')
                                        <button class="underline text-red-600 ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-gray-500">No targets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $targets->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

