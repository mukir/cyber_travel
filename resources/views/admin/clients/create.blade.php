<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" class="border rounded w-full" required>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" class="border rounded w-full" required>
                    </div>
                    <div>
                        <label>Assign Staff (Point of Contact)</label>
                        <select name="sales_rep_id" class="border rounded w-full">
                            <option value="">-- Unassigned --</option>
                            @foreach(($staff ?? []) as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Password</label>
                        <input type="password" name="password" class="border rounded w-full" required>
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                        <a href="{{ route('admin.clients') }}" class="ml-2 text-gray-600">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
