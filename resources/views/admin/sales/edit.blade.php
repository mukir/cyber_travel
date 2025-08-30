<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.sales.update', $sale) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block">Client</label>
                        <select name="client_id" class="w-full border px-4 py-2">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected($client->id == $sale->client_id)>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block">Staff</label>
                        <select name="staff_id" class="w-full border px-4 py-2">
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" @selected($member->id == $sale->staff_id)>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block">Amount</label>
                        <input type="number" step="0.01" name="amount" class="w-full border px-4 py-2" value="{{ $sale->amount }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="block">Commission</label>
                        <input type="number" step="0.01" name="commission" class="w-full border px-4 py-2" value="{{ $sale->commission }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="block">Status</label>
                        <input type="text" name="status" class="w-full border px-4 py-2" value="{{ $sale->status }}" required>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
