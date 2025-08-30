<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Documents') }} - {{ $client->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2">File</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $doc->type)) }}</td>
                                <td class="px-4 py-2"><a href="{{ Storage::url($doc->path) }}" target="_blank" class="text-blue-500">View</a></td>
                                <td class="px-4 py-2">{{ $doc->validated ? 'Validated' : 'Pending' }}</td>
                                <td class="px-4 py-2">
                                    @if(!$doc->validated)
                                        <form method="POST" action="{{ route('admin.clients.documents.validate', [$client, $doc]) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Validate</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2">No documents uploaded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    <a href="{{ route('admin.clients') }}" class="text-gray-600">Back to Clients</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

