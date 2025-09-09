<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">All Staff</h3>
                        <p class="text-sm text-gray-500">Toggle active status to include/exclude from round-robin.</p>
                    </div>
                </div>

                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Active</th>
                            <th class="px-4 py-2">Assigned Clients</th>
                            <th class="px-4 py-2">Open Leads</th>
                            <th class="px-4 py-2">Target</th>
                            <th class="px-4 py-2">Achieved</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $user)
                            @php($s = $stats[$user->id] ?? ['clients'=>0,'openLeads'=>0,'target'=>0,'achieved'=>0])
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $user->name }}</td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $s['clients'] }}</td>
                                <td class="px-4 py-2">{{ $s['openLeads'] }}</td>
                                <td class="px-4 py-2">{{ number_format($s['target'], 2) }}</td>
                                <td class="px-4 py-2">{{ number_format($s['achieved'], 2) }}</td>
                                <td class="px-4 py-2">
                                    <form method="POST" action="{{ route('admin.staff.toggle', $user) }}">
                                        @csrf
                                        <button class="px-3 py-1 rounded text-white text-sm {{ $user->is_active ? 'bg-gray-600' : 'bg-emerald-600' }}">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-4">No staff found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

