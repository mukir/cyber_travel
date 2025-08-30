<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Job Details</h2>
  </x-slot>

  <div class="py-8">
  <div class="max-w-3xl mx-auto px-4">
    @if(session('success'))
      <div class="mb-4 rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <a href="{{ route('jobs.index') }}" class="text-sm text-emerald-700 hover:underline">&larr; All jobs</a>

    <h1 class="mt-2 text-2xl font-bold">{{ $job->name }}</h1>
    @if($job->description)
      <p class="mt-2 text-slate-700">{{ $job->description }}</p>
    @endif

    <div class="mt-6">
      <h2 class="text-lg font-semibold">Instant Booking</h2>
      <form action="{{ route('bookings.store') }}" method="POST" class="mt-4 space-y-4" id="booking-form" data-currency="{{ env('APP_CURRENCY','KES') }}">
        @csrf
        <input type="hidden" name="job_id" value="{{ $job->id }}" />

        <div>
          <label class="block text-sm font-medium">Package</label>
          <select name="job_package_id" id="package" class="mt-1 w-full rounded border p-2">
            @foreach($job->packages as $pkg)
              <option value="{{ $pkg->id }}" data-price="{{ $pkg->price }}" @if($pkg->is_default) selected @endif>{{ $pkg->name }} — {{ number_format($pkg->price, 2) }}</option>
            @endforeach
          </select>
          @error('job_package_id')
            <div class="text-sm text-red-600">{{ $message }}</div>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-medium">Quantity</label>
          <input type="number" name="quantity" id="quantity" min="1" value="1" class="mt-1 w-32 rounded border p-2" />
          @error('quantity')
            <div class="text-sm text-red-600">{{ $message }}</div>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-medium">Start date</label>
          <input type="date" name="start_date" class="mt-1 rounded border p-2" />
          @error('start_date')
            <div class="text-sm text-red-600">{{ $message }}</div>
          @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Your name</label>
            <input type="text" name="customer_name" class="mt-1 w-full rounded border p-2" required />
            @error('customer_name')
              <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
          </div>
          <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="customer_email" class="mt-1 w-full rounded border p-2" required />
            @error('customer_email')
              <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium">Phone</label>
          <input type="text" name="customer_phone" class="mt-1 w-full rounded border p-2" />
          @error('customer_phone')
            <div class="text-sm text-red-600">{{ $message }}</div>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-medium">Notes</label>
          <textarea name="notes" class="mt-1 w-full rounded border p-2" rows="3"></textarea>
          @error('notes')
            <div class="text-sm text-red-600">{{ $message }}</div>
          @enderror
        </div>

        <div class="flex items-center justify-between py-4">
          <div class="text-xl font-semibold">Total: <span id="total">0.00</span> <span id="currency">{{ env('APP_CURRENCY','KES') }}</span></div>
          <button type="submit" class="rounded bg-emerald-600 px-5 py-3 font-semibold text-white hover:bg-emerald-700">Book now</button>
        </div>
      </form>
    </div>
  </div>
  </div>
</div>
</x-app-layout>
