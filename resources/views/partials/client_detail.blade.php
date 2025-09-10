<div class="space-y-8">
@php
  // Compute variables once in a safe block
  $latest = ($bookings ?? collect())->first();
  $balanceLatest = $latest ? max(((float)($latest->total_amount ?? 0)) - ((float)($latest->amount_paid ?? 0)), 0) : null;
  $currencyDefault = \App\Helpers\Settings::get('default_currency', config('app.currency', env('APP_CURRENCY','KES')));
  $firstName = explode(' ', trim((($profile->name ?? $client->name ?? '') ?: '')))[0] ?? '';
  $titleLatest = optional($latest?->job)->name ?: (optional($latest?->package)->name ?: null);
  $docsUploaded = ($documents ?? collect())->pluck('type')->map(function($t){ return strtolower((string)$t); })->all();
  $required = \App\Models\DocumentType::where('active', true)->where('required', true)->pluck('key')->all();
  $missing = array_values(array_diff($required, $docsUploaded));
  $pendingText = empty($missing) ? 'none' : implode(', ', $missing);
  $lines = [];
  $lines[] = $titleLatest ? ("Hi {$firstName}, quick follow-up on your {$titleLatest}.") : ("Hi {$firstName}, quick follow-up on your application.");
  $lines[] = 'Pending: ' . $pendingText . '.';
  $checkoutUrl = $latest ? route('client.applications.checkout', $latest) : route('jobs.index');
  if (!is_null($balanceLatest) && $balanceLatest > 0.01) {
      $lines[] = 'Balance: ' . number_format($balanceLatest, 2) . ' ' . ($latest?->currency ?: $currencyDefault) . '. Pay: ' . $checkoutUrl;
  } else {
      $lines[] = 'You are almost set. Details: ' . $checkoutUrl;
  }
  $toDigits = \App\Helpers\Phone::toE164Digits($profile->phone ?? null);
  $waLink = ($toDigits && preg_match('/^[1-9]\d{7,14}$/', $toDigits)) ? ('https://wa.me/' . $toDigits . '?text=' . rawurlencode(implode("\n", $lines))) : null;
@endphp

  <div class="bg-white shadow sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-3">Actions</h3>
    <div class="flex flex-wrap gap-2 text-sm">
      @if(!empty($waLink))
        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-1.5 rounded bg-emerald-600 text-white">WhatsApp</a>
      @endif
      @if(!empty($profile?->phone))
        <a href="tel:{{ $profile->phone }}" class="inline-flex items-center px-3 py-1.5 rounded bg-sky-600 text-white">Call</a>
      @endif
      @if(!empty($latest))
        <a href="{{ route('client.applications.checkout', $latest) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-indigo-600 text-white">Checkout</a>
      @endif

      @if(($context ?? 'admin') === 'admin')
        <a href="{{ route('admin.clients.documents', $client) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-gray-700 text-white">Documents</a>
        @if(!empty($latest) && !empty($balanceLatest) && $balanceLatest > 0.01)
          <a href="{{ route('admin.applications.manualPayment', $latest) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-amber-700 text-white">Record Payment</a>
        @endif
        <a href="{{ route('admin.clients.edit', $client) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-blue-600 text-white">Edit</a>
        <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('Delete client?');" class="inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded bg-red-600 text-white">Delete</button>
        </form>
      @endif
    </div>
  </div>

  <div class="bg-white shadow sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-3">Profile</h3>
    <div class="grid md:grid-cols-3 gap-4 text-sm">
      <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $client->name }}</span></div>
      <div><span class="text-gray-500">Email:</span> <span class="font-medium">{{ $client->email }}</span></div>
      <div><span class="text-gray-500">Phone:</span> <span class="font-medium">{{ $profile?->phone ?: '—' }}</span></div>
      <div><span class="text-gray-500">Gender:</span> <span class="font-medium">{{ $profile?->gender ?: '—' }}</span></div>
      <div><span class="text-gray-500">ID/Passport:</span> <span class="font-medium">{{ $profile?->id_no ?: '—' }}</span></div>
      <div><span class="text-gray-500">County:</span> <span class="font-medium">{{ $profile?->county ?: '—' }}</span></div>
      <div><span class="text-gray-500">Status:</span> <span class="font-medium">{{ $profile?->status ?: '—' }}</span></div>
      <div><span class="text-gray-500">Application Date:</span> <span class="font-medium">{{ $profile?->application_date ?: '—' }}</span></div>
      <div><span class="text-gray-500">Interview Date:</span> <span class="font-medium">{{ $profile?->interview_date ?: '—' }}</span></div>
      <div><span class="text-gray-500">Travel Date:</span> <span class="font-medium">{{ $profile?->travel_date ?: '—' }}</span></div>
      <div><span class="text-gray-500">Service Package:</span> <span class="font-medium">{{ $profile?->service_package ?: '—' }}</span></div>
      <div><span class="text-gray-500">Assigned Staff:</span> <span class="font-medium">{{ optional(\App\Models\User::find($profile?->sales_rep_id))->name ?: '—' }}</span></div>
    </div>
    @if(($context ?? 'admin') === 'admin')
      <div class="mt-4">
        <a href="{{ route('admin.clients.edit', $client) }}" class="text-blue-600 hover:underline text-sm">Edit Client</a>
      </div>
    @endif
  </div>

  <div class="bg-white shadow sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-3">Documents</h3>
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="px-3 py-2">Type</th>
          <th class="px-3 py-2">Note</th>
          <th class="px-3 py-2">Validated</th>
          <th class="px-3 py-2">Uploaded</th>
          <th class="px-3 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($documents as $doc)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $doc->type }}</td>
            <td class="px-3 py-2">{{ $doc->note }}</td>
            <td class="px-3 py-2">{!! $doc->validated ? '<span class="text-emerald-700">Yes</span>' : '<span class="text-gray-500">No</span>' !!}</td>
            <td class="px-3 py-2">{{ $doc->created_at->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2">
              @if(($context ?? 'admin') === 'admin')
                <a href="{{ route('admin.clients.documents.view', [$client, $doc]) }}" target="_blank" class="text-blue-600 hover:underline">View</a>
              @else
                <a href="{{ route('client.documents') }}" class="text-blue-600 hover:underline">View</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-3 py-3 text-gray-500">No documents uploaded.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="bg-white shadow sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-3">Applications</h3>
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="px-3 py-2">Date</th>
          <th class="px-3 py-2">Job</th>
          <th class="px-3 py-2">Package</th>
          <th class="px-3 py-2">Status</th>
          <th class="px-3 py-2">Total</th>
          <th class="px-3 py-2">Paid</th>
          <th class="px-3 py-2">Outstanding</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bookings as $b)
          @php($balance = max(((float)($b->total_amount ?? 0)) - ((float)($b->amount_paid ?? 0)), 0))
          <tr class="border-t">
            <td class="px-3 py-2">{{ $b->created_at->format('Y-m-d') }}</td>
            <td class="px-3 py-2">{{ optional($b->job)->name ?: '—' }}</td>
            <td class="px-3 py-2">{{ optional($b->package)->name ?: '—' }}</td>
            <td class="px-3 py-2">{{ ucfirst($b->status ?? 'pending') }}</td>
            <td class="px-3 py-2">{{ number_format((float)($b->total_amount ?? 0),2) }} {{ $b->currency ?: $currencyDefault }}</td>
            <td class="px-3 py-2">{{ number_format((float)($b->amount_paid ?? 0),2) }} {{ $b->currency ?: $currencyDefault }}</td>
            <td class="px-3 py-2">{{ number_format($balance,2) }} {{ $b->currency ?: $currencyDefault }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-3 py-3 text-gray-500">No applications found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="bg-white shadow sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-3">Payments</h3>
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="px-3 py-2">Date</th>
          <th class="px-3 py-2">Booking</th>
          <th class="px-3 py-2">Method</th>
          <th class="px-3 py-2">Amount</th>
          <th class="px-3 py-2">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payments as $p)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $p->created_at->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2">BK{{ $p->booking_id }}</td>
            <td class="px-3 py-2">{{ strtoupper($p->method ?? 'n/a') }}</td>
            <td class="px-3 py-2">{{ number_format((float)($p->amount ?? 0), 2) }} {{ optional($p->booking)->currency ?: $currencyDefault }}</td>
            <td class="px-3 py-2">{{ ucfirst($p->status ?? 'paid') }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-3 py-3 text-gray-500">No payments recorded.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="bg-white shadow sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-3">Leads &amp; Notes</h3>
    <div class="space-y-4">
      @forelse($leads as $lead)
        <div class="border rounded p-3">
          <div class="flex items-center justify-between text-sm">
            <div>
              <span class="font-medium">{{ $lead->name ?: $client->name }}</span>
              <span class="text-gray-500"> • {{ ucfirst($lead->stage) }} • {{ ucfirst($lead->status) }}</span>
            </div>
            <div class="text-gray-500">{{ $lead->created_at->format('Y-m-d') }}</div>
          </div>
          @if($lead->notes && $lead->notes->count())
            <ul class="mt-2 list-disc list-inside text-sm text-gray-700">
              @foreach($lead->notes as $n)
                <li>{{ $n->content }} <span class="text-gray-400">— {{ $n->created_at->format('Y-m-d H:i') }}</span></li>
              @endforeach
            </ul>
          @else
            <div class="mt-2 text-sm text-gray-500">No notes yet.</div>
          @endif
        </div>
      @empty
        <div class="text-gray-500">No leads for this client.</div>
      @endforelse
    </div>
  </div>
</div>
