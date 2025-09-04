<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
            @endif
            <!-- Support quick access -->
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-emerald-200 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-800" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-9 4a1 1 0 102 0 1 1 0 00-2 0zm.25-7a.75.75 0 000 1.5h.5a1.25 1.25 0 011.25 1.25v.25a1 1 0 01-2 0 .75.75 0 00-1.5 0 2.5 2.5 0 005 0v-.25A2.75 2.75 0 0010.75 7h-.5A.75.75 0 009.25 6z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-emerald-900">Need help?</div>
                        <div class="text-sm text-emerald-800">Send a support request and our team will assist you.</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('client.support.tickets') }}" class="text-sm text-emerald-800 hover:underline">View My Tickets</a>
                    <a href="{{ route('client.support') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-white font-medium hover:bg-emerald-700">
                        Open Support Form
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h8.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L12.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
                    </a>
                    @php
                        $companyWhats = \App\Helpers\Settings::get('company.whatsapp_number');
                        $firstName = explode(' ', $profile->name ?: (auth()->user()->name ?? ''))[0] ?? '';
                        $jobName = $latestBooking?->job?->name;
                        $pkgName = $latestBooking?->package?->name;
                        $balance = $latestBooking ? max(((float)$latestBooking->total_amount) - ((float)$latestBooking->amount_paid), 0) : null;
                        $currency = $latestBooking?->currency ?? \App\Helpers\Settings::get('default_currency', config('app.currency', env('APP_CURRENCY', 'KES')));
                        $pendingText = $missingDocs->isNotEmpty() ? $missingDocs->implode(', ') : 'none';
                        $checkoutUrl = $latestBooking ? route('client.bookings.checkout', $latestBooking) : route('jobs.index');
                        $parts = [];
                        $parts[] = "Hi, this is {$firstName}. I need assistance" . ($jobName || $pkgName ? " for my {$jobName}" . ($pkgName ? " – {$pkgName}" : '') : '') . '.';
                        $parts[] = 'Pending: ' . $pendingText . '.';
                        if (isset($balance) && $balance > 0.01) {
                            $parts[] = 'Balance: ' . number_format($balance, 2) . ' ' . $currency . '. Checkout: ' . $checkoutUrl;
                        }
                        $clientMsg = rawurlencode(implode("\n", $parts));
                    @endphp
                    @if(!empty($companyWhats))
                        <a href="https://wa.me/{{ preg_replace('/[^\d]/','',$companyWhats) }}?text={{ $clientMsg }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-emerald-600 px-4 py-2 text-emerald-700 hover:bg-emerald-50">
                            WhatsApp Support
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.52 3.48A11.94 11.94 0 0012 0C5.38 0 0 5.38 0 12c0 2.11.55 4.11 1.6 5.9L0 24l6.27-1.64A11.95 11.95 0 0012 24c6.62 0 12-5.38 12-12 0-3.2-1.25-6.22-3.48-8.52zM12 22.05c-1.94 0-3.82-.51-5.47-1.49l-.39-.23-3.72.97.99-3.63-.25-.37A9.96 9.96 0 012.05 12 9.95 9.95 0 1122.05 12 10.05 10.05 0 0112 22.05zm5.47-7.48c-.3-.15-1.79-.88-2.06-.98-.28-.11-.48-.15-.68.15-.2.29-.78.98-.96 1.18-.18.2-.36.22-.66.08-.3-.15-1.26-.46-2.4-1.48-.88-.78-1.47-1.74-1.64-2.03-.17-.29-.02-.45.13-.6.13-.13.3-.34.45-.51.15-.17.2-.29.3-.49.1-.2.05-.37-.02-.52-.08-.15-.68-1.64-.93-2.25-.25-.6-.5-.52-.68-.53l-.58-.01c-.2 0-.52.08-.79.37-.27.29-1.04 1.02-1.04 2.49 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.09 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.79-.73 2.05-1.43.25-.7.25-1.3.18-1.43-.07-.13-.26-.2-.56-.35z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
            <!-- Progress -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Progress</h3>
                    @if(!empty($nextMilestone))
                        @php($pill = match($nextMilestone){
                            'Payment' => 'bg-amber-100 text-amber-800',
                            'Processing' => 'bg-blue-100 text-blue-800',
                            'Complete' => 'bg-emerald-100 text-emerald-800',
                            default => 'bg-slate-100 text-slate-800',
                        })
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $pill }}">Next: {{ $nextMilestone }}</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">All done</span>
                    @endif
                </div>
                <div class="flex items-center">
                    @foreach($stages as $i => $s)
                        <div class="flex-1 flex items-center">
                            <div class="relative flex flex-col items-center">
                                <div class="{{ $s['done'] ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-600' }} rounded-full h-8 w-8 flex items-center justify-center font-semibold">
                                    {{ $i + 1 }}
                                </div>
                                <div class="mt-2 text-xs text-center {{ $s['done'] ? 'text-emerald-700' : 'text-gray-600' }}">{{ $s['label'] }}</div>
                            </div>
                            @if(!$loop->last)
                                <div class="{{ ($stages[$i+1]['done'] ?? false) ? 'bg-emerald-600' : 'bg-gray-300' }} h-1 flex-1 mx-2"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status, key dates, latest booking -->
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold">Service Status</h3>
                    <dl class="mt-4 space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Package</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $profile->service_package ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Current Status</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $profile->status ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold">Key Dates</h3>
                    <dl class="mt-4 space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Application</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $profile->application_date ? \Carbon\Carbon::parse($profile->application_date)->format('Y-m-d') : '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Interview</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $profile->interview_date ? \Carbon\Carbon::parse($profile->interview_date)->format('Y-m-d') : '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Travel</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $profile->travel_date ? \Carbon\Carbon::parse($profile->travel_date)->format('Y-m-d') : '—' }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold">Latest Booking</h3>
                    @if($latestBooking)
                        <dl class="mt-4 space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Job</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $latestBooking->job?->name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Package</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $latestBooking->package?->name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Total</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($latestBooking->total_amount, 2) }} {{ $latestBooking->currency }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-600">Status</dt>
                                @php($badge = match($latestBooking->status){
                                  'pending' => 'bg-amber-100 text-amber-800',
                                  'paid' => 'bg-emerald-100 text-emerald-800',
                                  'cancelled' => 'bg-gray-100 text-gray-800',
                                  default => 'bg-slate-100 text-slate-800',
                                })
                                <dd class="text-sm font-medium"><span class="px-2 py-1 rounded {{ $badge }}">{{ ucfirst($latestBooking->status) }}</span></dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex items-center justify-between">
                            @if($latestBooking->status === 'pending')
                                <form action="{{ route('client.bookings.pay', $latestBooking) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="tel" name="phone" value="{{ old('phone', $profile->phone ?? $latestBooking->customer_phone) }}" placeholder="07XXXXXXXX" class="w-40 rounded border p-1 text-sm" required />
                                    <input type="number" step="0.01" min="1" max="{{ max($latestBooking->total_amount - $latestBooking->amount_paid, 0) }}" name="amount" value="{{ number_format(max($latestBooking->total_amount - $latestBooking->amount_paid, 0), 2, '.', '') }}" class="w-28 rounded border p-1 text-sm" />
                                    <button class="rounded bg-emerald-600 px-3 py-1.5 text-white text-sm hover:bg-emerald-700">M-PESA</button>
                                </form>
                                @if($latestBooking->mpesa_checkout_id)
                                    <form action="{{ route('client.bookings.verify', $latestBooking) }}" method="POST">
                                        @csrf
                                        <button class="ml-3 text-xs text-gray-600 hover:underline">Verify payment</button>
                                    </form>
                                @endif
                            @endif
                            <div class="ml-auto space-x-4">
                              @if($latestBooking->status === 'pending')
                                <a href="{{ route('client.bookings.checkout', $latestBooking) }}" class="text-sm text-emerald-700 hover:underline">Go to Checkout</a>
                              @endif
                              <a href="{{ route('client.bookings') }}" class="text-sm text-emerald-700 hover:underline">View all bookings</a>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-sm text-gray-600">No bookings yet. <a class="text-emerald-700 hover:underline" href="{{ route('jobs.index') }}">Book a package</a>.</p>
                    @endif
                </div>
            </div>

            <!-- Pending actions / Missing docs -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Pending Actions</h3>
                @if(!empty($pendingActions))
                    <ul class="mt-4 list-disc pl-5 space-y-2 text-sm">
                        @foreach($pendingActions as $action)
                            <li>
                                @if(!empty($action['route']))
                                    <a href="{{ $action['route'] }}" class="text-emerald-700 hover:underline">{{ $action['label'] }}</a>
                                @else
                                    <span class="text-gray-800">{{ $action['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 text-sm text-gray-600">You are all set. No pending actions right now.</p>
                @endif

                @if($missingDocs->isNotEmpty())
                    <div class="mt-6 rounded border border-amber-300 bg-amber-50 text-amber-800 p-4">
                        <p class="font-semibold">Missing Documents</p>
                        <p class="text-sm mt-1">Please upload: {{ $missingDocs->implode(', ') }}</p>
                        <a href="{{ route('client.documents') }}" class="inline-block mt-3 rounded bg-amber-600 px-4 py-2 text-white text-sm hover:bg-amber-700">Go to Documents</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
