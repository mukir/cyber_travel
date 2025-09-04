<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Record Manual Payment</h2>
      <a href="{{ route('admin.payments') }}" class="text-sm text-emerald-700 hover:underline">Back to Payments</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="rounded bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
      @endif
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold">Booking Details</h3>
        <dl class="mt-3 grid sm:grid-cols-2 gap-3 text-sm">
          <div>
            <dt class="text-gray-500">Reference</dt>
            <dd class="font-medium">BK{{ $booking->id }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Client</dt>
            <dd class="font-medium">{{ $booking->customer_name }} <span class="text-gray-500">{{ $booking->customer_email }}</span></dd>
          </div>
          <div>
            <dt class="text-gray-500">Package</dt>
            <dd class="font-medium">{{ $booking->job?->name }} @if($booking->package) — {{ $booking->package->name }} @endif</dd>
          </div>
          <div>
            <dt class="text-gray-500">Totals</dt>
            <dd class="font-medium">{{ number_format($booking->amount_paid, 2) }} / {{ number_format($booking->total_amount, 2) }} {{ $booking->currency }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Remaining</dt>
            <dd class="font-medium">{{ number_format($remaining, 2) }} {{ $booking->currency }}</dd>
          </div>
          <div>
            <dt class="text-gray-500">Status</dt>
            <dd class="font-medium capitalize">{{ $booking->status }}</dd>
          </div>
        </dl>

        <form action="{{ route('admin.bookings.manualPayment.store', $booking) }}" method="POST" class="mt-6 grid gap-4">
          @csrf
          <div>
            <label class="block text-sm text-gray-700">Method</label>
            <select name="method" class="mt-1 w-full rounded border p-2">
              <option value="cash">Cash</option>
              <option value="bank">Bank</option>
              <option value="manual">Manual</option>
            </select>
            @error('method')
              <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700">Amount</label>
            <div class="flex items-center gap-2">
              <input id="amount" type="number" step="0.01" min="0.01" max="{{ number_format($remaining, 2, '.', '') }}" name="amount" value="{{ number_format($remaining, 2, '.', '') }}" class="mt-1 w-48 rounded border p-2" />
              <button type="button" onclick="document.getElementById('amount').value='{{ number_format($remaining, 2, '.', '') }}'" class="rounded border px-3 py-2 text-sm">Pay Remaining</button>
            </div>
            @error('amount')
              <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-700">Reference</label>
              <input type="text" name="reference" class="mt-1 w-full rounded border p-2" />
              @error('reference')
                <div class="text-sm text-red-600">{{ $message }}</div>
              @enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Receipt Number</label>
              <input type="text" name="receipt_number" class="mt-1 w-full rounded border p-2" />
              @error('receipt_number')
                <div class="text-sm text-red-600">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="pt-2 flex items-center gap-3">
            <button type="submit" class="rounded bg-emerald-600 px-5 py-2 text-white font-semibold">Record Payment</button>
            <a href="{{ route('admin.payments') }}" class="text-sm text-gray-700 hover:underline">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>

