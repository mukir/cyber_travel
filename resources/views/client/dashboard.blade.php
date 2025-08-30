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
