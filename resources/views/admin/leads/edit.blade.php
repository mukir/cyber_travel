<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Lead</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="grid gap-4 text-sm">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-gray-600">Sales Rep</label>
                        <select name="sales_rep_id" class="mt-1 w-full rounded border p-2" required>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}" @selected($lead->sales_rep_id==$s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600">Name</label>
                        <input name="name" class="mt-1 w-full rounded border p-2" value="{{ $lead->name }}" required />
                    </div>
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600">Email</label>
                            <input name="email" class="mt-1 w-full rounded border p-2" value="{{ $lead->email }}" />
                        </div>
                        <div>
                            <label class="block text-gray-600">Phone</label>
                            <input name="phone" class="mt-1 w-full rounded border p-2" value="{{ $lead->phone }}" />
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-gray-600">Stage</label>
                            <select name="stage" class="mt-1 w-full rounded border p-2">
                                @foreach(['new','contacted','qualified','won','lost'] as $s)
                                    <option value="{{ $s }}" @selected($lead->stage===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600">Status</label>
                            <select name="status" class="mt-1 w-full rounded border p-2">
                                @foreach(['open','closed'] as $s)
                                    <option value="{{ $s }}" @selected($lead->status===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600">Next follow-up</label>
                            <input type="date" name="next_follow_up" value="{{ $lead->next_follow_up ? $lead->next_follow_up->format('Y-m-d') : '' }}" class="mt-1 w-full rounded border p-2" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-600">Notes</label>
                        <textarea name="notes" class="mt-1 w-full rounded border p-2" rows="4">{{ $lead->notes }}</textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Save</button>
                        <a href="{{ route('admin.leads.index') }}" class="text-slate-700 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

