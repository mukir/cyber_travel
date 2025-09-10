<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold">Add Staff</h3>
                        <p class="text-sm text-gray-500">Create a new staff account. Password is required.</p>
                        @if(session('success'))
                            <div class="mt-3 rounded bg-emerald-50 text-emerald-800 px-3 py-2 text-sm">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="mt-3 rounded bg-red-50 text-red-800 px-3 py-2 text-sm">{{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="mt-3 rounded bg-red-50 text-red-800 px-3 py-2 text-sm">
                                <ul class="list-disc pl-4">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('admin.staff.store') }}" class="grid md:grid-cols-5 gap-3 mt-3">
                            @csrf
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2" placeholder="e.g. 07XXXXXXXX or 2547XXXXXXXX" />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium">Password</label>
                                <input type="password" name="password" class="w-full border rounded px-3 py-2" required />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required />
                            </div>
                            <div class="md:col-span-4 flex items-center gap-3">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="hidden" name="is_active" value="0" />
                                    <input type="checkbox" name="is_active" value="1" class="rounded" {{ old('is_active', '1') == '1' ? 'checked' : '' }} />
                                    <span>Active (include in round-robin)</span>
                                </label>
                            </div>
                            <div class="md:col-span-1 text-right">
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded w-full md:w-auto">Add Staff</button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold">All Staff</h3>
                        <p class="text-sm text-gray-500">Toggle active status to include/exclude from round-robin.</p>
                        <form method="GET" action="{{ route('admin.staff.index') }}" class="mt-3 grid md:grid-cols-4 gap-3 text-sm">
                            <div class="md:col-span-2">
                                <label class="block">Search</label>
                                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="w-full border rounded px-3 py-2" placeholder="Search name or email" />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block">Status</label>
                                <select name="status" class="w-full border rounded px-3 py-2">
                                    @php($status = $filters['status'] ?? 'all')
                                    <option value="all" @selected($status==='all')>All</option>
                                    <option value="active" @selected($status==='active')>Active</option>
                                    <option value="inactive" @selected($status==='inactive')>Inactive</option>
                                </select>
                            </div>
                            <div class="md:col-span-1 flex items-end gap-2">
                                <button class="bg-gray-800 text-white px-4 py-2 rounded">Filter</button>
                                <a href="{{ route('admin.staff.index') }}" class="px-3 py-2 rounded border">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Role</th>
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
                                <td class="px-4 py-2"><a href="{{ route('admin.staff.show', $user) }}" class="text-indigo-700 hover:underline">{{ $user->name }}</a></td>
                                <td class="px-4 py-2">{{ $user->email }}</td>
                                <td class="px-4 py-2">
                                    @php($roleLabel = (method_exists($user,'is_reception') && $user->is_reception()) ? 'Reception' : 'Staff')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-800">{{ $roleLabel }}</span>
                                </td>
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
                                    <a href="{{ route('admin.staff.show', $user) }}" class="px-3 py-1 rounded text-white text-sm bg-indigo-600">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-4">No staff found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
