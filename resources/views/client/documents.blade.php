<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Documents</h2>
      <a href="{{ route('client.dashboard') }}" class="text-sm text-emerald-700 hover:underline">&larr; Back to Dashboard</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if ($errors->any())
        <div class="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800">Upload failed. Please check the file and type.</div>
      @endif

      @php(
        $required = collect(['passport','good_conduct','cv','photo'])
      )
      @php(
        $uploadedTypes = $documents->pluck('type')->map(fn($t)=> strtolower((string)$t))->unique()
      )
      @php(
        $done = $required->filter(fn($t)=> $uploadedTypes->contains($t))->count()
      )
      @php($percent = (int) round(($required->count() ? $done / $required->count() : 1) * 100))

      <div class="bg-white shadow sm:rounded-lg p-6">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold">Requirements</h3>
          <span class="text-sm text-gray-600">{{ $percent }}% complete</span>
        </div>
        <div class="mt-2 h-2 w-full rounded bg-gray-200 overflow-hidden" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
          <div class="h-full bg-emerald-600" style="width: {{ $percent }}%"></div>
        </div>
        <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
          @foreach($required as $req)
            @php($ok = $uploadedTypes->contains($req))
            <div class="flex items-center gap-2 rounded border p-2 {{ $ok ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }}">
              @if($ok)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414L8.5 11.586l6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7.707 7.707a1 1 0 10-1.414-1.414L10 5.586l3.707 3.707a1 1 0 11-1.414 1.414L10 8.414l-2.293 2.293z" clip-rule="evenodd"/></svg>
              @endif
              <span class="text-sm capitalize">{{ str_replace('_',' ', $req) }}</span>
            </div>
          @endforeach
        </div>
        <p class="mt-3 text-xs text-gray-600">Accepted formats: PDF, JPG, JPEG, PNG. Max size: 2MB per file.</p>
      </div>

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">Upload Document</h3>
        <form method="POST" action="{{ route('client.documents.upload') }}" enctype="multipart/form-data" class="mt-4 grid md:grid-cols-3 gap-4 items-end">
          @csrf
          <div>
            <label class="block text-sm text-gray-700">Document Type</label>
            <select name="type" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" required>
              <option value="passport">Passport</option>
              <option value="good_conduct">Good Conduct</option>
              <option value="cv">CV/Academic certs</option>
              <option value="photo">Passport Size Photo</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm text-gray-700">File</label>
            <input type="file" name="file" accept="application/pdf,image/*" class="mt-1 block w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" required />
          </div>
          <div>
            <button type="submit" class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Upload</button>
          </div>
        </form>
      </div>

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">Uploaded Documents</h3>
        @if($documents->isEmpty())
          <p class="mt-2 text-sm text-gray-600">No documents uploaded yet.</p>
        @else
          <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($documents as $doc)
              @php($label = ucfirst(str_replace('_',' ', $doc->type)))
              <div class="rounded border p-4">
                <div class="flex items-start justify-between">
                  <div>
                    <p class="font-medium">{{ $label }}</p>
                    <p class="mt-1 text-xs text-gray-500">Uploaded {{ optional($doc->created_at)->diffForHumans() }}</p>
                  </div>
                  <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold {{ $doc->validated ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $doc->validated ? 'Validated' : 'Pending' }}
                  </span>
                </div>
                <div class="mt-3 flex items-center gap-3 text-sm">
                  <a href="{{ Storage::url($doc->path) }}" target="_blank" class="text-emerald-700 hover:underline">View</a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
