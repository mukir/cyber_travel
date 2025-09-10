<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 rounded bg-emerald-50 text-emerald-800 px-3 py-2 text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 rounded bg-red-50 text-red-800 px-3 py-2 text-sm">{{ session('error') }}</div>
                @endif

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Name</div>
                        <div class="font-semibold text-lg">{{ $staff->name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Email</div>
                        <div class="font-mono">{{ $staff->email }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Phone</div>
                        <div class="font-mono">{{ $staff->phone ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Status</div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ $staff->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ $staff->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid md:grid-cols-4 gap-4">
                    <div class="p-3 border rounded">
                        <div class="text-xs text-gray-500">Assigned Clients</div>
                        <div class="text-xl font-semibold">{{ $stats['clients'] }}</div>
                    </div>
                    <div class="p-3 border rounded">
                        <div class="text-xs text-gray-500">Open Leads</div>
                        <div class="text-xl font-semibold">{{ $stats['openLeads'] }}</div>
                    </div>
                    <div class="p-3 border rounded">
                        <div class="text-xs text-gray-500">Target</div>
                        <div class="text-xl font-semibold">{{ number_format($stats['target'], 2) }}</div>
                    </div>
                    <div class="p-3 border rounded">
                        <div class="text-xs text-gray-500">Achieved</div>
                        <div class="text-xl font-semibold">{{ number_format($stats['achieved'], 2) }}</div>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-2">
                    <a href="{{ route('admin.staff.edit', $staff) }}" class="px-4 py-2 rounded text-white bg-indigo-600">Edit</a>
                    <form method="POST" action="{{ route('admin.staff.invite', $staff) }}">
                        @csrf
                        <button class="px-4 py-2 rounded text-white bg-emerald-600">Invite / Reset Password</button>
                    </form>
                    <form method="POST" action="{{ route('admin.staff.toggle', $staff) }}">
                        @csrf
                        <button class="px-4 py-2 rounded text-white {{ $staff->is_active ? 'bg-gray-700' : 'bg-emerald-700' }}">{{ $staff->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.staff.promote', $staff) }}" onsubmit="return confirm('Promote this staff member to admin?');">
                        @csrf
                        <button class="px-4 py-2 rounded text-white bg-gray-800">Make Admin</button>
                    </form>
                    <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" onsubmit="return confirm('Delete this staff member? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button class="px-4 py-2 rounded text-white bg-red-600">Delete</button>
                    </form>
                    <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 rounded border">Back</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

