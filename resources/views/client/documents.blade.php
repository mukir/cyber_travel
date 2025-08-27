<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Documents') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('client.documents.upload') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label>Document Type</label>
                        <select name="type" class="border rounded w-full">
                            <option value="passport">Passport</option>
                            <option value="good_conduct">Good Conduct</option>
                            <option value="cv">CV/Academic certs</option>
                            <option value="photo">Passport Size Photo</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <input type="file" name="file" class="border rounded w-full"/>
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium mb-4">Uploaded Documents</h3>
                <ul>
                    @forelse($documents as $doc)
                        <li class="mb-2">{{ ucfirst(str_replace('_', ' ', $doc->type)) }} - <a href="{{ Storage::url($doc->path) }}" class="text-blue-500" target="_blank">View</a></li>
                    @empty
                        <li>No documents uploaded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
