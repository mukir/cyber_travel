<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Checkout</h2>
      <a href="{{ route('client.bookings') }}" class="text-sm text-emerald-700 hover:underline">&larr; My Bookings</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid md:grid-cols-3 gap-6">
      @if(session('success'))
        <div class="md:col-span-3 rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="md:col-span-3 rounded bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
      @endif

      <div class="md:col-span-2 bg-white shadow sm:rounded-lg p-6 space-y-6">
        <div>
          <h3 class="text-lg font-semibold">Order Summary</h3>
          <dl class="mt-4 grid grid-cols-2 gap-2 text-sm">
            <dt class="text-gray-600">Reference</dt>
            <dd class="text-gray-900">#BK{{ $booking->id }}</dd>
            <dt class="text-gray-600">Job</dt>
            <dd class="text-gray-900">{{ $booking->job?->name ?? '—' }}</dd>
            <dt class="text-gray-600">Package</dt>
            <dd class="text-gray-900">{{ $booking->package?->name ?? '—' }}</dd>
            <dt class="text-gray-600">Quantity</dt>
            <dd class="text-gray-900">{{ $booking->quantity }}</dd>
            <dt class="text-gray-600">Total</dt>
            <dd class="text-gray-900 font-semibold">{{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</dd>
            <dt class="text-gray-600">Paid</dt>
            <dd class="text-gray-900">{{ number_format($booking->amount_paid, 2) }} {{ $booking->currency }}</dd>
            <dt class="text-gray-600">Balance</dt>
            <dd class="text-gray-900">{{ number_format(max($booking->total_amount - $booking->amount_paid, 0), 2) }} {{ $booking->currency }}</dd>
          </dl>

          <div class="mt-4 flex items-center gap-3 text-sm">
            <a class="text-emerald-700 hover:underline" href="{{ route('client.bookings.invoice', $booking) }}">Download Invoice</a>
            @if($booking->amount_paid > 0)
              <span class="text-gray-300">|</span>
              <a class="text-emerald-700 hover:underline" href="{{ route('client.bookings.receipt', $booking) }}">Download Receipt</a>
            @endif
          </div>
        </div>

        <div>
          <h3 class="text-lg font-semibold">Pay with M-PESA</h3>
          <form action="{{ route('client.bookings.pay', $booking) }}" method="POST" class="mt-3 grid sm:grid-cols-3 gap-3 items-end">
            @csrf
            <div>
              <label class="block text-sm text-gray-700">Phone (07XXXXXXXX)</label>
              <input type="tel" name="phone" value="{{ old('phone', $booking->customer_phone) }}" class="mt-1 w-full rounded border p-2" required />
            </div>
            <div>
              <label class="block text-sm text-gray-700">Amount</label>
              <input type="number" step="0.01" min="1" max="{{ max($booking->total_amount - $booking->amount_paid, 0) }}" name="amount" value="{{ number_format(max($booking->total_amount - $booking->amount_paid, 0), 2, '.', '') }}" class="mt-1 w-full rounded border p-2" />
            </div>
            <div>
              <button class="w-full rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Send STK</button>
            </div>
          </form>
          @if($booking->mpesa_checkout_id)
            <form action="{{ route('client.bookings.verify', $booking) }}" method="POST" class="mt-2">
              @csrf
              <button class="text-sm text-gray-600 hover:underline">Verify latest M-PESA payment</button>
            </form>
            <div id="mpesa-status" class="mt-2 text-sm text-gray-700"></div>
          @endif
        </div>

        <div>
          <h3 class="text-lg font-semibold">Pay with PayPal</h3>
          <div class="mt-2">
            <label class="block text-sm text-gray-700">Amount ({{ $currency }})</label>
            <input id="pp-amount" type="number" step="0.01" min="1" max="{{ max($booking->total_amount - $booking->amount_paid, 0) }}" value="{{ number_format(max($booking->total_amount - $booking->amount_paid, 0), 2, '.', '') }}" class="mt-1 w-48 rounded border p-2" />
          </div>
          <div id="paypal-buttons" class="mt-3"></div>
        </div>
      </div>

      <div class="bg-white shadow sm:rounded-lg p-6 h-max">
        <h3 class="text-lg font-semibold">Payment Status</h3>
        <p class="mt-2 text-sm">Current: <span class="font-medium capitalize">{{ $booking->payment_status }}</span></p>
        <p class="mt-1 text-sm">Booking Status: <span class="font-medium capitalize">{{ $booking->status }}</span></p>
        <p class="mt-1 text-sm">Paid at: <span class="font-medium">{{ $booking->paid_at ? $booking->paid_at->format('Y-m-d H:i') : '—' }}</span></p>
      </div>
    </div>
  </div>

  @if($paypalClientId)
    <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency={{ $currency }}"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('pp-amount');
        if (window.paypal) {
          paypal.Buttons({
            createOrder: (data, actions) => {
              return actions.order.create({
                purchase_units: [{ amount: { value: (amountInput.value || '1.00') } }]
              });
            },
            onApprove: async (data, actions) => {
              const details = await actions.order.capture();
              const payload = {
                order_id: data.orderID,
                amount: amountInput.value,
                details
              };
              const res = await fetch('{{ route('client.bookings.paypalComplete', $booking) }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
              });
              if (res.ok) {
                window.location.reload();
              } else {
                alert('Failed to record PayPal payment');
              }
            }
          }).render('#paypal-buttons');
        }
      });
    </script>
  @endif

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const checkoutId = @json($booking->mpesa_checkout_id);
      if (!checkoutId) return;

      const statusEl = document.getElementById('mpesa-status');
      const routeTemplate = @json(route('payments.mpesa.status', ['ref' => '__REF__']));
      const statusUrl = routeTemplate.replace('__REF__', encodeURIComponent(checkoutId));

      let tries = 0;
      const maxTries = 45; // ~3 minutes at 4s interval
      const intervalMs = 4000;
      let timer = null;

      const setStatus = (text, tone = 'neutral') => {
        if (!statusEl) return;
        statusEl.classList.remove('text-gray-700', 'text-emerald-700', 'text-red-700');
        statusEl.classList.add(tone === 'success' ? 'text-emerald-700' : tone === 'error' ? 'text-red-700' : 'text-gray-700');
        statusEl.textContent = text;
      };

      const poll = async () => {
        tries++;
        setStatus('Checking M-PESA status...');
        try {
          const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
          if (!res.ok) {
            throw new Error('Network error');
          }
          const data = await res.json();
          if (data.status === 'success') {
            setStatus('Payment confirmed. Updating...', 'success');
            clearInterval(timer);
            setTimeout(() => window.location.reload(), 800);
          } else if (data.status === 'failed') {
            setStatus('Payment failed: ' + (data.message || 'Declined'), 'error');
            clearInterval(timer);
          } else {
            setStatus('Awaiting confirmation... (keep your phone nearby)');
            if (tries >= maxTries) {
              clearInterval(timer);
              setStatus('Still pending. You can verify again or retry STK.');
            }
          }
        } catch (e) {
          if (tries >= maxTries) {
            clearInterval(timer);
            setStatus('Could not verify at the moment. Try again later.', 'error');
          }
        }
      };

      // kick off immediately, then interval
      poll();
      timer = setInterval(poll, intervalMs);
    });
  </script>
</x-app-layout>
