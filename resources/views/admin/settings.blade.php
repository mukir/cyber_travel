<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">General</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-4 grid gap-4">
          @csrf
          <div>
            <label class="block text-sm text-gray-700">Default Currency</label>
            <input type="text" name="default_currency" value="{{ old('default_currency', $defaultCurrency) }}" class="mt-1 w-40 rounded border p-2 uppercase" maxlength="10" placeholder="KES" />
            <p class="mt-1 text-xs text-gray-500">Used when creating new bookings.</p>
            @error('default_currency')
              <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div>
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Save Settings</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>

