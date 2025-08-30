<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jobs</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-5xl mx-auto px-4">
      <h1 class="text-2xl font-bold">Jobs</h1>
      <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($jobs as $job)
          <a href="{{ route('jobs.show', $job->slug) }}" class="block rounded-xl border bg-white p-5 hover:shadow">
            <h2 class="text-lg font-semibold">{{ $job->name }}</h2>
            @if($job->base_price > 0)
              <div class="mt-1 text-sm text-slate-600">From {{ number_format($job->base_price, 2) }} {{ env('APP_CURRENCY', 'KES') }}</div>
            @endif
            <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($job->description, 120) }}</p>
          </a>
        @empty
          <div class="col-span-full text-slate-600">No jobs available.</div>
        @endforelse
      </div>
    </div>
  </div>
</x-app-layout>

