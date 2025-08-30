<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" value="{{ $client->name }}" class="border rounded w-full" required>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="{{ $client->email }}" class="border rounded w-full" required>
                    </div>
                    <div>
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="border rounded w-full">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
                        <a href="{{ route('admin.clients') }}" class="ml-2 text-gray-600">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

