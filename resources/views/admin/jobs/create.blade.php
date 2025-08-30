<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Job</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <form method="POST" action="{{ route('admin.jobs.store') }}" class="space-y-4">
          @csrf

          <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" class="mt-1 w-full rounded border p-2" value="{{ old('name') }}" required />
            @error('name') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium">Slug (optional)</label>
            <input name="slug" class="mt-1 w-full rounded border p-2" value="{{ old('slug') }}" />
            @error('slug') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium">Base Price</label>
            <input type="number" step="0.01" min="0" name="base_price" class="mt-1 w-full rounded border p-2" value="{{ old('base_price', 0) }}" />
            @error('base_price') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium">Description</label>
            <textarea name="description" rows="4" class="mt-1 w-full rounded border p-2">{{ old('description') }}</textarea>
            @error('description') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
          </div>

          <div class="flex items-center gap-2">
            <input id="active" type="checkbox" name="active" value="1" class="rounded border-gray-300" checked />
            <label for="active">Active</label>
          </div>

          <div class="pt-4">
            <button class="rounded bg-emerald-600 px-5 py-2 text-white font-semibold hover:bg-emerald-700">Save</button>
            <a href="{{ route('admin.jobs.index') }}" class="ml-2 text-gray-700 hover:underline">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>

