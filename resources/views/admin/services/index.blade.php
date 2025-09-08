<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Services</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">Add Service</h3>
        <form action="{{ route('admin.services.store') }}" method="POST" class="mt-4 grid md:grid-cols-2 gap-4">
          @csrf
          <div>
            <label class="block text-sm text-gray-700">Slug (unique)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded border p-2 font-mono" placeholder="international-travel-planning" required />
            @error('slug')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="mt-1 w-full rounded border p-2" placeholder="International Travel Planning" required />
            @error('title')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm text-gray-700">Summary</label>
            <textarea name="summary" class="mt-1 w-full rounded border p-2" rows="3" placeholder="Short description">{{ old('summary') }}</textarea>
            @error('summary')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="flex items-center gap-4">
            <div>
              <label class="block text-sm text-gray-700">Position</label>
              <input type="number" name="position" value="{{ old('position', 0) }}" class="mt-1 w-28 rounded border p-2" min="0" />
            </div>
            <label class="inline-flex items-center gap-2 mt-6">
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
        <h3 class="text-lg font-semibold">Existing Services</h3>
        <div class="mt-4 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="text-left p-2">Title</th>
                <th class="text-left p-2">Slug</th>
                <th class="text-left p-2">Position</th>
                <th class="text-left p-2">Active</th>
                <th class="p-2"></th>
              </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($services as $s)
              <tr>
                <td class="p-2">
                  <form action="{{ route('admin.services.update', $s) }}" method="POST" class="grid gap-2">
                    @csrf
                    @method('PUT')
                    <input type="text" name="title" value="{{ $s->title }}" class="w-full rounded border p-2" />
                    <textarea name="summary" rows="2" class="w-full rounded border p-2">{{ $s->summary }}</textarea>
                </td>
                <td class="p-2 font-mono text-xs">{{ $s->slug }}</td>
                <td class="p-2"><input type="number" name="position" value="{{ $s->position }}" class="w-24 rounded border p-1" min="0" /></td>
                <td class="p-2">
                  <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="active" value="1" @checked($s->active) />
                    <span>Active</span>
                  </label>
                </td>
                <td class="p-2 text-right whitespace-nowrap">
                    <button class="rounded bg-emerald-600 px-3 py-1.5 text-white text-xs font-semibold">Save</button>
                  </form>
                  <form action="{{ route('admin.services.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Delete this service?');">
                    @csrf
                    @method('DELETE')
                    <button class="rounded bg-red-600 px-3 py-1.5 text-white text-xs font-semibold">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="p-2 text-gray-500">No services yet.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

