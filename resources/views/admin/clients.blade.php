<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('admin.clients.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Client</a>
                </div>

                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr class="border-t align-top">
                                <td class="px-4 py-2">{{ $client->name }}</td>
                                <td class="px-4 py-2">{{ $client->email }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $profile = \App\Models\ClientProfile::where('user_id', $client->id)->first();
                                    @endphp
                                    {{ $profile?->phone ?: '—' }}
                                </td>
                                <td class="px-4 py-2 space-x-3 whitespace-nowrap">
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <a href="{{ route('admin.clients.documents', $client) }}" class="text-green-600 hover:underline">Documents</a>
                                    @php
                                        $firstName = explode(' ', trim(($profile->name ?? $client->name) ?: ''))[0] ?? '';
                                        $latest = \App\Models\Booking::with(['job','package'])
                                            ->where('user_id', $client->id)
                                            ->latest()
                                            ->first();
                                        $jobName = $latest?->job?->name;
                                        $pkgName = $latest?->package?->name;
                                        $balance = $latest ? max(((float)$latest->total_amount) - ((float)$latest->amount_paid), 0) : null;
                                        $currency = $latest?->currency ?? \App\Helpers\Settings::get('default_currency', config('app.currency', env('APP_CURRENCY', 'KES')));
                                        $docs = \App\Models\ClientDocument::where('user_id', $client->id)->pluck('type')->all();
                                        $required = ['passport','good_conduct','cv','photo'];
                                        $missing = array_values(array_diff($required, $docs));
                                        $pendingText = empty($missing) ? 'none' : implode(', ', $missing);
                                        $checkoutUrl = $latest ? route('client.bookings.checkout', $latest) : route('jobs.index');

                                        $title = ($jobName || $pkgName)
                                            ? trim(($jobName ?: 'package') . ($pkgName ? (' - ' . $pkgName) : ''))
                                            : null;

                                        $lines = [];
                                        $lines[] = $title
                                            ? ("Hi {$firstName}, quick follow-up on your {$title}.")
                                            : ("Hi {$firstName}, quick follow-up on your application.");
                                        $lines[] = 'Pending: ' . $pendingText . '.';
                                        if (isset($balance) && $balance > 0.01) {
                                            $lines[] = 'Balance: ' . number_format($balance, 2) . ' ' . $currency . '. Pay: ' . $checkoutUrl;
                                        } else {
                                            $lines[] = 'You are almost set. Details: ' . $checkoutUrl;
                                        }
                                        $message = implode("\n", $lines);
                                        $to = \App\Helpers\Phone::toE164Digits($profile?->phone);
                                        $isValidWa = $to && preg_match('/^[1-9]\d{7,14}$/', $to);
                                        $waLink = $isValidWa ? ('https://wa.me/' . $to . '?text=' . rawurlencode($message)) : null;
                                    @endphp
                                    @if(!empty($waLink))
                                        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="text-emerald-600 hover:underline">WhatsApp</a>
                                    @else
                                        <span class="text-gray-400" title="{{ empty($to) ? 'No phone on profile' : 'Invalid phone for WhatsApp' }}">WhatsApp</span>
                                    @endif
                                    @if($latest && $balance > 0.01)
                                        <a href="{{ route('admin.bookings.manualPayment', $latest) }}" class="text-amber-700 hover:underline">Record Payment</a>
                                    @endif
                                    <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('Delete client?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2">No clients found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
