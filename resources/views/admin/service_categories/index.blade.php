<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Service Categories</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">Add Category</h3>
        <form action="{{ route('admin.service_categories.store') }}" method="POST" class="mt-4 grid md:grid-cols-2 gap-4">
          @csrf
          <div>
            <label class="block text-sm text-gray-700">Slug</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded border p-2 font-mono" placeholder="e.g. travel" required />
            @error('slug')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded border p-2" placeholder="Travel" required />
            @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm text-gray-700">Description</label>
            <textarea name="description" class="mt-1 w-full rounded border p-2" rows="2">{{ old('description') }}</textarea>
          </div>
          <div class="md:col-span-2">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="active" value="1" class="rounded border" checked />
              <span class="text-sm text-gray-700">Active</span>
            </label>
          </div>
          <div class="md:col-span-2">
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold">Add</button>
          </div>
        </form>
      </div>

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">Existing Categories</h3>
        <table class="mt-4 min-w-full text-sm">
          <thead>
            <tr class="bg-gray-50">
              <th class="text-left p-2">Slug</th>
              <th class="text-left p-2">Name</th>
              <th class="text-left p-2">Active</th>
              <th class="p-2"></th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($categories as $cat)
              <tr>
                <td class="p-2 font-mono text-xs">{{ $cat->slug }}</td>
                <td class="p-2">
                  <form action="{{ route('admin.service_categories.update', $cat) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $cat->name }}" class="w-64 rounded border p-1" />
                    <input type="hidden" name="description" value="{{ $cat->description }}" />
                    <label class="inline-flex items-center gap-2">
                      <input type="checkbox" name="active" value="1" @checked($cat->active) />
                      <span>Active</span>
                    </label>
                    <button class="rounded bg-emerald-600 px-3 py-1.5 text-white text-xs font-semibold">Save</button>
                  </form>
                </td>
                <td class="p-2">{{ $cat->active ? 'Yes' : 'No' }}</td>
                <td class="p-2 text-right whitespace-nowrap">
                  <form action="{{ route('admin.service_categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                    @csrf
                    @method('DELETE')
                    <button class="rounded bg-red-600 px-3 py-1.5 text-white text-xs font-semibold">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="p-2 text-gray-500">No categories yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>

