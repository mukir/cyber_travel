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
      {{-- Dynamic M-PESA banner --}}
      <div id="mpesa-banner" class="md:col-span-3 hidden rounded border px-4 py-3 text-sm" aria-live="polite"></div>

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
          <form id="form-stk" action="{{ route('client.bookings.pay', $booking) }}" method="POST" class="mt-3 grid sm:grid-cols-3 gap-3 items-end">
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
              <button id="btn-stk" class="w-full rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Send STK</button>
            </div>
          </form>
          @if($booking->mpesa_checkout_id)
            <form action="{{ route('client.bookings.verify', $booking) }}" method="POST" class="mt-2">
              @csrf
              <button id="btn-verify" class="text-sm text-gray-600 hover:underline">Verify latest M-PESA payment</button>
            </form>
          @endif
          <div id="mpesa-status" class="mt-2 text-sm text-gray-700 flex items-center">
            <span id="mpesa-spinner" class="hidden inline-block w-4 h-4 mr-2 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
            <span id="mpesa-status-text"></span>
          </div>
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
      let checkoutId = @json($booking->mpesa_checkout_id);
      const formStk = document.getElementById('form-stk');
      const statusEl = document.getElementById('mpesa-status');
      const statusTextEl = document.getElementById('mpesa-status-text');
      const spinnerEl = document.getElementById('mpesa-spinner');
      const bannerEl = document.getElementById('mpesa-banner');
      const btnStk = document.getElementById('btn-stk');
      const btnVerify = document.getElementById('btn-verify');
      const routeTemplate = @json(route('payments.mpesa.status', ['ref' => '__REF__']));
      let statusUrl = checkoutId ? routeTemplate.replace('__REF__', encodeURIComponent(checkoutId)) : null;

      let tries = 0;
      const maxTries = 60; // ~4 minutes at 4s interval
      const intervalMs = 4000;
      let timer = null;

      const setStatus = (text, tone = 'neutral', spinning = false) => {
        if (!statusEl) return;
        statusEl.classList.remove('text-gray-700', 'text-emerald-700', 'text-red-700');
        statusEl.classList.add(tone === 'success' ? 'text-emerald-700' : tone === 'error' ? 'text-red-700' : 'text-gray-700');
        if (statusTextEl) statusTextEl.textContent = text;
        if (spinnerEl) spinnerEl.classList.toggle('hidden', !spinning);
      };

      const showBanner = (tone = 'info', text = '') => {
        if (!bannerEl) return;
        bannerEl.classList.remove('hidden', 'bg-emerald-100','text-emerald-800','border-emerald-200','bg-red-100','text-red-800','border-red-200','bg-amber-50','text-amber-800','border-amber-200','bg-blue-50','text-blue-800','border-blue-200');
        if (tone === 'success') {
          bannerEl.classList.add('bg-emerald-100','text-emerald-800','border','border-emerald-200');
        } else if (tone === 'error') {
          bannerEl.classList.add('bg-red-100','text-red-800','border','border-red-200');
        } else {
          bannerEl.classList.add('bg-blue-50','text-blue-800','border','border-blue-200');
        }
        bannerEl.textContent = text;
      };

      const setButtonsDisabled = (disabled) => {
        const toggle = (btn) => {
          if (!btn) return;
          btn.disabled = disabled;
          btn.classList.toggle('opacity-60', disabled);
          btn.classList.toggle('cursor-not-allowed', disabled);
        };
        toggle(btnStk);
        toggle(btnVerify);
      };

      const poll = async () => {
        if (!checkoutId || !statusUrl) {
          showBanner('info', 'No M-PESA request yet. Send STK first.');
          return;
        }
        tries++;
        setStatus('Checking M-PESA status...', 'neutral', true);
        showBanner('info', 'Awaiting M-PESA confirmation. Please enter your PIN when prompted on your phone.');
        setButtonsDisabled(true);
        try {
          const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
          if (!res.ok) {
            throw new Error('Network error');
          }
          const data = await res.json();
          if (data.status === 'success') {
            setStatus('Payment confirmed. Updating...', 'success', false);
            showBanner('success', 'Payment confirmed. Updating your booking...');
            clearInterval(timer);
            setTimeout(() => window.location.reload(), 800);
          } else if (data.status === 'failed') {
            setStatus('Payment failed: ' + (data.message || 'Declined'), 'error', false);
            showBanner('error', 'Payment failed: ' + (data.message || 'Declined'));
            clearInterval(timer);
            setButtonsDisabled(false);
          } else {
            setStatus('Awaiting confirmation... (keep your phone nearby)', 'neutral', true);
            if (tries >= maxTries) {
              clearInterval(timer);
              setStatus('Still pending. You can verify again or retry STK.', 'neutral', false);
              showBanner('info', 'Still pending. You can click Verify again or send a new STK request.');
              setButtonsDisabled(false);
            }
          }
        } catch (e) {
          if (tries >= maxTries) {
            clearInterval(timer);
            setStatus('Could not verify at the moment. Try again later.', 'error', false);
            showBanner('error', 'We could not reach M-PESA to verify. Please try again.');
            setButtonsDisabled(false);
          }
        }
      };

      // kick off immediately, then interval if there is a pending request
      if (checkoutId) {
        poll();
        timer = setInterval(poll, intervalMs);
      }

      // Intercept STK form submit to initiate via fetch JSON
      if (formStk) {
        formStk.addEventListener('submit', async function (e) {
          e.preventDefault();
          const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          const phone = formStk.querySelector('input[name="phone"]').value;
          const amount = formStk.querySelector('input[name="amount"]').value;

          setButtonsDisabled(true);
          setStatus('Sending STK push...', 'neutral', true);
          showBanner('info', 'Sending STK push. Check your phone for the prompt.');

          try {
            const res = await fetch(formStk.action, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
              },
              body: JSON.stringify({ phone, amount })
            });
            const data = await res.json().catch(() => ({}));
            if (data && data.status === 'success') {
              const newCheckout = data.checkoutId || (data.data && data.data.CheckoutRequestID);
              if (newCheckout) {
                checkoutId = newCheckout;
                statusUrl = routeTemplate.replace('__REF__', encodeURIComponent(checkoutId));
                tries = 0;
                if (timer) clearInterval(timer);
                poll();
                timer = setInterval(poll, intervalMs);
              }
            } else {
              const msg = (data && data.message) ? data.message : 'Failed to initiate payment.';
              showBanner('error', msg);
              setStatus('Payment initiation failed.', 'error', false);
              setButtonsDisabled(false);
            }
          } catch (err) {
            showBanner('error', 'Network error while initiating payment.');
            setStatus('Network error while initiating payment.', 'error', false);
            setButtonsDisabled(false);
          }
        });
      }

      // Intercept manual Verify button to trigger one-off poll
      if (btnVerify) {
        btnVerify.addEventListener('click', function (e) {
          e.preventDefault();
          tries = 0;
          poll();
        });
      }
    });
  </script>
</x-app-layout>
