<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reception Dashboard</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <form method="GET" action="{{ route('reception.clients') }}" class="flex gap-2 items-end text-sm">
          <div class="flex-1">
            <label class="block text-gray-600">Quick Search Clients</label>
            <input type="text" name="q" placeholder="Search name, email, phone, ID" class="mt-1 w-full rounded border p-2" />
          </div>
          <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Search</button>
          <a href="{{ route('reception.clients') }}" class="px-3 py-2 rounded border">Full Client List</a>
          <a href="{{ route('reception.visitors') }}" class="px-3 py-2 rounded border">Visitors Book</a>
        </form>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white p-6 shadow sm:rounded-lg">
          <div class="text-gray-600">Visitors today</div>
          <div class="text-3xl font-semibold">{{ $todayVisitors }}</div>
        </div>
        <div class="bg-white p-6 shadow sm:rounded-lg">
          <div class="text-gray-600">Open client applications</div>
          <div class="text-3xl font-semibold">{{ $openClients }}</div>
        </div>
        <div class="bg-white p-6 shadow sm:rounded-lg">
          <div class="text-gray-600">Quick Links</div>
          <div class="mt-2 space-x-2">
            <a href="{{ route('reception.clients') }}" class="underline">Client Status</a>
            <a href="{{ route('reception.visitors') }}" class="underline">Visitors Book</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
