<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reception Dashboard</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid md:grid-cols-2 gap-4">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <div class="text-gray-600">Visitors today</div>
        <div class="text-3xl font-semibold">{{ $todayVisitors }}</div>
      </div>
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <div class="text-gray-600">Open client applications</div>
        <div class="text-3xl font-semibold">{{ $openClients }}</div>
      </div>
    </div>
  </div>
</x-app-layout>

