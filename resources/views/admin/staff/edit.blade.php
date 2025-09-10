<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 rounded bg-emerald-50 text-emerald-800 px-3 py-2 text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 rounded bg-red-50 text-red-800 px-3 py-2 text-sm">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded bg-red-50 text-red-800 px-3 py-2 text-sm">
                        <ul class="list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="grid gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input type="text" name="name" value="{{ old('name', $staff->name) }}" class="w-full border rounded px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $staff->email) }}" class="w-full border rounded px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="w-full border rounded px-3 py-2" placeholder="e.g. 07XXXXXXXX or 2547XXXXXXXX" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">New Password (optional)</label>
                        <input type="password" name="password" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="hidden" name="is_active" value="0" />
                            <input type="checkbox" name="is_active" value="1" class="rounded" {{ old('is_active', $staff->is_active ? '1' : '0') == '1' ? 'checked' : '' }} />
                            <span>Active (include in round-robin)</span>
                        </label>
                    </div>
                    <div class="flex gap-3">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded">Save</button>
                        <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                    </div>
                </form>

                <div class="mt-8 grid gap-2 text-sm text-gray-600">
                    <div>Assigned Clients: <strong>{{ $clients }}</strong></div>
                    <div>Open Leads: <strong>{{ $openLeads }}</strong></div>
                </div>
                <div class="mt-4 flex gap-3">
                    <form method="POST" action="{{ route('admin.staff.invite', $staff) }}">
                        @csrf
                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded">Send Invite / Reset Link</button>
                    </form>
                    <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" onsubmit="return confirm('Delete this staff member? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
