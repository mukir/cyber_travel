<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Country</h2>
      <a href="{{ route('admin.countries.index') }}" class="text-sm text-emerald-700 hover:underline">&larr; Back</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white p-6 shadow sm:rounded-lg">
        <form method="POST" action="{{ route('admin.countries.update', $country) }}" class="grid gap-4">
          @csrf
          @method('PUT')
          <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" class="mt-1 w-full rounded border p-2" value="{{ old('name', $country->name) }}" required />
            @error('name')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm font-medium">ISO Code (2-3 letters)</label>
            <input name="code" class="mt-1 w-full rounded border p-2 uppercase" value="{{ old('code', $country->code) }}" />
            @error('code')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block text-sm font-medium">Region</label>
            @php($r = old('region', $country->region))
            <select name="region" class="mt-1 w-full rounded border p-2">
              <option value="" @selected(!$r)>Other / Unset</option>
              <option value="europe" @selected($r==='europe')>Europe</option>
              <option value="gulf" @selected($r==='gulf')>Gulf</option>
              <option value="americas" @selected($r==='americas')>Americas</option>
              <option value="other" @selected($r==='other')>Other</option>
            </select>
            @error('region')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
          </div>
          <div class="flex items-center gap-2">
            <input id="active" type="checkbox" name="active" value="1" class="rounded border-gray-300" @checked($country->active) />
            <label for="active">Active</label>
          </div>
          <div>
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>

