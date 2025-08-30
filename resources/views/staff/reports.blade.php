<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sales & Commission Reports</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('staff.reports') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <label class="block text-gray-600">From</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="mt-1 w-full rounded border p-2" />
                    </div>
                    <div>
                        <label class="block text-gray-600">To</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="mt-1 w-full rounded border p-2" />
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button class="rounded bg-slate-700 px-4 py-2 text-white font-semibold">Apply</button>
                        <a class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold" href="{{ route('staff.reports.commissions.csv', request()->query()) }}">Download Commissions CSV</a>
                        <a class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold" href="{{ route('staff.reports.commissions.pdf', request()->query()) }}">Download Commissions PDF</a>
                    </div>
                </form>
                <p class="mt-4 text-xs text-gray-500">Default range is current month if no dates provided.</p>
            </div>
        </div>
    </div>
</x-app-layout>
