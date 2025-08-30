<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jobs</h2>
      <a href="{{ route('admin.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Add Job</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      @if(session('success'))
        <div class="mb-4 rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($jobs as $job)
              <tr>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $job->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $job->slug }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ number_format($job->base_price, 2) }} {{ env('APP_CURRENCY','KES') }}</td>
                <td class="px-6 py-4 text-sm">
                  @if($job->active)
                    <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Active</span>
                  @else
                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactive</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-sm text-right">
                  <a href="{{ route('admin.jobs.edit', $job) }}" class="text-emerald-700 hover:underline mr-3">Edit</a>
                  <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this job?')">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-6 text-sm text-gray-500">No jobs yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $jobs->links() }}</div>
    </div>
  </div>
</x-app-layout>

