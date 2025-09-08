<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Document Types</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">Add Document Type</h3>
        <form action="{{ route('admin.document_types.store') }}" method="POST" class="mt-4 grid md:grid-cols-2 gap-4">
          @csrf
          <div>
            <label class="block text-sm text-gray-700">Key (unique)</label>
            <input type="text" name="key" value="{{ old('key') }}" class="mt-1 w-full rounded border p-2 font-mono" placeholder="passport, good_conduct" required />
            @error('key')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded border p-2" placeholder="Passport" required />
            @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm text-gray-700">Description (optional)</label>
            <textarea name="description" class="mt-1 w-full rounded border p-2" rows="2">{{ old('description') }}</textarea>
            @error('description')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="md:col-span-2 flex items-center gap-6">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="required" value="1" class="rounded border" checked />
              <span class="text-sm text-gray-700">Required</span>
            </label>
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
        <h3 class="text-lg font-semibold">Existing Types</h3>
        <div class="mt-4 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="text-left p-2">Key</th>
                <th class="text-left p-2">Name</th>
                <th class="text-left p-2">Required</th>
                <th class="text-left p-2">Active</th>
                <th class="p-2"></th>
              </tr>
            </thead>
            <tbody class="divide-y">
            @forelse($types as $t)
              <tr>
                <td class="p-2 font-mono">{{ $t->key }}</td>
                <td class="p-2">{{ $t->name }}</td>
                <td class="p-2">{{ $t->required ? 'Yes' : 'No' }}</td>
                <td class="p-2">{{ $t->active ? 'Yes' : 'No' }}</td>
                <td class="p-2 text-right whitespace-nowrap">
                  <form action="{{ route('admin.document_types.update', $t) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $t->name }}"/>
                    <input type="hidden" name="description" value="{{ $t->description }}"/>
                    <input type="hidden" name="required" value="{{ $t->required ? 0 : 1 }}"/>
                    <input type="hidden" name="active" value="{{ $t->active ? 0 : 1 }}"/>
                    <button class="text-emerald-700 underline mr-3">Toggle</button>
                  </form>
                  <form action="{{ route('admin.document_types.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Delete this type?');">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-700 underline">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="p-2 text-gray-500">No document types yet.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

