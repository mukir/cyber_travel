<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Sales Target</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.targets.store') }}" class="grid gap-4 text-sm">
                    @csrf
                    <div>
                        <label class="block text-gray-600">Sales Rep</label>
                        <select name="staff_id" class="mt-1 w-full rounded border p-2" required>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-gray-600">Period</label>
                            <select name="period" class="mt-1 w-full rounded border p-2">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600">Start date</label>
                            <input type="date" name="start_date" class="mt-1 w-full rounded border p-2" required />
                        </div>
                        <div>
                            <label class="block text-gray-600">End date</label>
                            <input type="date" name="end_date" class="mt-1 w-full rounded border p-2" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-600">Target amount</label>
                        <input type="number" step="0.01" name="target_amount" class="mt-1 w-full rounded border p-2" required />
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Save</button>
                        <a href="{{ route('admin.targets.index') }}" class="text-slate-700 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

