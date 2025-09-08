<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Job</h2>
      <a href="{{ route('admin.jobs.index') }}" class="text-sm text-emerald-700 hover:underline">&larr; Back to Jobs</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold mb-4">Service Details</h3>
        <form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="space-y-4">
          @csrf
          @method('PUT')

          <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" class="mt-1 w-full rounded border p-2" value="{{ old('name', $job->name) }}" required />
            @error('name') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium">Slug (optional)</label>
            <input name="slug" class="mt-1 w-full rounded border p-2" value="{{ old('slug', $job->slug) }}" />
            @error('slug') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium">Base Price</label>
            <input type="number" step="0.01" min="0" name="base_price" class="mt-1 w-full rounded border p-2" value="{{ old('base_price', $job->base_price) }}" />
            @error('base_price') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="4" class="mt-1 w-full rounded border p-2">{{ old('description', $job->description) }}</textarea>
            @error('description') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium">Categories</label>
            <div class="mt-2 flex flex-wrap gap-3">
              @foreach(($categories ?? []) as $cat)
                @php($checked = $job->categories->contains('id', $cat->id))
                <label class="inline-flex items-center gap-2">
                  <input type="checkbox" name="categories[]" value="{{ $cat->id }}" class="rounded border" @checked($checked) />
                  <span class="text-sm">{{ $cat->name }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <div class="flex items-center gap-2">
            <input id="active" type="checkbox" name="active" value="1" class="rounded border-gray-300" @checked(old('active', $job->active)) />
            <label for="active">Active</label>
          </div>

          <div class="pt-4">
            <button class="rounded bg-emerald-600 px-5 py-2 text-white font-semibold hover:bg-emerald-700">Save changes</button>
          </div>
        </form>
      </div>

      <div class="bg-white p-6 shadow sm:rounded-lg">
        <h3 class="text-lg font-semibold">Packages</h3>
        <p class="text-sm text-gray-600 mb-4">Manage pricing options for this job. Mark one as default to preselect on the public page.</p>

        <div class="space-y-6">
          @forelse($job->packages as $package)
            <div class="border rounded p-4">
              <form action="{{ route('admin.jobs.packages.update', [$job, $package]) }}" method="POST" class="grid md:grid-cols-2 gap-4 items-start">
                @csrf
                @method('PUT')
                <div>
                  <label class="block text-sm font-medium">Name</label>
                  <input name="name" class="mt-1 w-full rounded border p-2" value="{{ $package->name }}" required />
                </div>
                <div>
                  <label class="block text-sm font-medium">Price</label>
                  <input type="number" step="0.01" min="0" name="price" class="mt-1 w-full rounded border p-2" value="{{ $package->price }}" required />
                </div>
                <div>
                  <label class="block text-sm font-medium">Duration (days)</label>
                  <input type="number" min="0" name="duration_days" class="mt-1 w-full rounded border p-2" value="{{ $package->duration_days }}" />
                </div>
                <div class="flex items-center gap-2 mt-6">
                  <input id="default-{{ $package->id }}" type="checkbox" name="is_default" value="1" class="rounded border-gray-300" @checked($package->is_default) />
                  <label for="default-{{ $package->id }}">Default</label>
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium">Description</label>
                  <textarea name="description" rows="2" class="mt-1 w-full rounded border p-2">{{ $package->description }}</textarea>
                </div>
                <div class="md:col-span-2 flex items-center justify-between">
                  <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Update Package</button>

                  <form action="{{ route('admin.jobs.packages.destroy', [$job, $package]) }}" method="POST" onsubmit="return confirm('Delete this package?')">
                    @csrf
                    @method('DELETE')
                    <button class="rounded bg-red-600 px-4 py-2 text-white font-semibold hover:bg-red-700">Delete</button>
                  </form>
                </div>
              </form>
            </div>
          @empty
            <div class="text-sm text-gray-600">No packages yet.</div>
          @endforelse
        </div>

        <div class="mt-8">
          <h4 class="text-md font-semibold mb-2">Add Package</h4>
          <form action="{{ route('admin.jobs.packages.store', $job) }}" method="POST" class="grid md:grid-cols-2 gap-4">
            @csrf
            <div>
              <label class="block text-sm font-medium">Name</label>
              <input name="name" class="mt-1 w-full rounded border p-2" required />
            </div>
            <div>
              <label class="block text-sm font-medium">Price</label>
              <input type="number" step="0.01" min="0" name="price" class="mt-1 w-full rounded border p-2" required />
            </div>
            <div>
              <label class="block text-sm font-medium">Duration (days)</label>
              <input type="number" min="0" name="duration_days" class="mt-1 w-full rounded border p-2" />
            </div>
            <div class="flex items-center gap-2 mt-6">
              <input id="new-default" type="checkbox" name="is_default" value="1" class="rounded border-gray-300" />
              <label for="new-default">Default</label>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium">Description</label>
              <textarea name="description" rows="2" class="mt-1 w-full rounded border p-2"></textarea>
            </div>
            <div class="md:col-span-2">
              <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Add Package</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
