<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('admin.sales.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-500 text-white rounded">Add Sale</a>
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Client</th>
                            <th class="px-4 py-2">Staff</th>
                            <th class="px-4 py-2">Amount</th>
                            <th class="px-4 py-2">Outstanding</th>
                            <th class="px-4 py-2">Commission</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td class="border px-4 py-2">{{ $sale->client->name }}</td>
                                <td class="border px-4 py-2">{{ $sale->staff->name }}</td>
                                <td class="border px-4 py-2">{{ $sale->amount }}</td>
                                @php($out = max(((float)optional($sale->booking)->total_amount) - ((float)optional($sale->booking)->amount_paid), 0))
                                <td class="border px-4 py-2">{{ number_format($out, 2) }} {{ optional($sale->booking)->currency }}</td>
                                <td class="border px-4 py-2">{{ $sale->commission }}</td>
                                <td class="border px-4 py-2">{{ $sale->status }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('admin.sales.edit', $sale) }}" class="text-blue-500">Edit</a>
                                    <form action="{{ route('admin.sales.destroy', $sale) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
