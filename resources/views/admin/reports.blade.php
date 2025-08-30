<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Automated Reporting') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Financial Reports</h3>
                <div class="mt-3 grid sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 rounded border">
                        <div class="font-medium">Daily</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.payments.export.csv', ['from'=>now()->startOfDay()->format('Y-m-d'), 'to'=>now()->endOfDay()->format('Y-m-d')]) }}">CSV</a>
                            <a class="underline" href="{{ route('admin.payments.export.pdf', ['from'=>now()->startOfDay()->format('Y-m-d'), 'to'=>now()->endOfDay()->format('Y-m-d')]) }}">PDF</a>
                        </div>
                    </div>
                    <div class="p-3 rounded border">
                        <div class="font-medium">Weekly</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.payments.export.csv', ['from'=>now()->startOfWeek()->format('Y-m-d'), 'to'=>now()->endOfWeek()->format('Y-m-d')]) }}">CSV</a>
                            <a class="underline" href="{{ route('admin.payments.export.pdf', ['from'=>now()->startOfWeek()->format('Y-m-d'), 'to'=>now()->endOfWeek()->format('Y-m-d')]) }}">PDF</a>
                        </div>
                    </div>
                    <div class="p-3 rounded border">
                        <div class="font-medium">Monthly</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.payments.export.csv', ['from'=>now()->startOfMonth()->format('Y-m-d'), 'to'=>now()->endOfMonth()->format('Y-m-d')]) }}">CSV</a>
                            <a class="underline" href="{{ route('admin.payments.export.pdf', ['from'=>now()->startOfMonth()->format('Y-m-d'), 'to'=>now()->endOfMonth()->format('Y-m-d')]) }}">PDF</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Commission Reports</h3>
                <div class="mt-3 grid sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 rounded border">
                        <div class="font-medium">Daily</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.reports.commissions.csv', 'daily') }}">CSV</a>
                            <a class="underline" href="{{ route('admin.reports.commissions.pdf', 'daily') }}">PDF</a>
                        </div>
                    </div>
                    <div class="p-3 rounded border">
                        <div class="font-medium">Weekly</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.reports.commissions.csv', 'weekly') }}">CSV</a>
                            <a class="underline" href="{{ route('admin.reports.commissions.pdf', 'weekly') }}">PDF</a>
                        </div>
                    </div>
                    <div class="p-3 rounded border">
                        <div class="font-medium">Monthly</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.reports.commissions.csv', 'monthly') }}">CSV</a>
                            <a class="underline" href="{{ route('admin.reports.commissions.pdf', 'monthly') }}">PDF</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Operational Reports</h3>
                <div class="mt-3 grid sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 rounded border">
                        <div class="font-medium">Daily Bookings</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.reports.bookings.csv', 'daily') }}">CSV</a>
                            <a class="underline" href="{{ route('admin.reports.bookings.pdf', 'daily') }}">PDF</a>
                        </div>
                    </div>
                    <div class="p-3 rounded border">
                        <div class="font-medium">Weekly Bookings</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.reports.bookings.csv', 'weekly') }}">CSV</a>
                            <a class="underline" href="{{ route('admin.reports.bookings.pdf', 'weekly') }}">PDF</a>
                        </div>
                    </div>
                    <div class="p-3 rounded border">
                        <div class="font-medium">Monthly Bookings</div>
                        <div class="mt-2 space-x-2">
                            <a class="underline" href="{{ route('admin.reports.bookings.csv', 'monthly') }}">CSV</a>
                            <a class="underline" href="{{ route('admin.reports.bookings.pdf', 'monthly') }}">PDF</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Auto Email</h3>
                <form action="{{ route('admin.reports.email') }}" method="POST" class="mt-3 grid sm:grid-cols-4 gap-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-gray-600">Type</label>
                        <select name="type" class="mt-1 w-full rounded border p-2">
                            <option value="payments">Payments</option>
                            <option value="bookings">Bookings</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600">Period</label>
                        <select name="period" class="mt-1 w-full rounded border p-2">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600">Email</label>
                        <input type="email" name="email" class="mt-1 w-full rounded border p-2" placeholder="reports@example.com" required />
                    </div>
                    <div class="flex items-end">
                        <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Send Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
