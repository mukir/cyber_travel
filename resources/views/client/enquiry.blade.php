<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Service Enquiry</h2>
      <a href="{{ route('client.dashboard') }}" class="text-sm text-emerald-700 hover:underline">&larr; Back to Dashboard</a>
    </div>
  </x-slot>

  <div class="py-8" x-data="enquiryForm()">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      @if(session('success'))
        <div class="mb-4 rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800">Please fix the errors below.</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <form method="POST" action="{{ route('client.enquiry.store') }}" class="space-y-8">
          @csrf
          <div>
            <h3 class="text-lg font-semibold">Service Type</h3>
            <div class="mt-3 grid md:grid-cols-2 gap-4">
              <label class="flex items-center gap-2">
                <input type="radio" name="service_type" value="job" x-model="type" class="rounded" required />
                <span>Job</span>
              </label>
              <label class="flex items-center gap-2">
                <input type="radio" name="service_type" value="tour" x-model="type" class="rounded" />
                <span>Tour</span>
              </label>
            </div>
          </div>

          <div x-show="type==='job'" x-cloak>
            <h3 class="text-lg font-semibold">Job Details</h3>
            <div class="mt-4 grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700">Select Job</label>
                <select name="job_id" x-model="jobId" @change="onJobChange" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500">
                  <option value="">-- Choose --</option>
                  @foreach($jobs as $job)
                    <option value="{{ $job->id }}">{{ $job->name }}</option>
                  @endforeach
                </select>
                @error('job_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Package (optional)</label>
                <select name="package_id" x-model="packageId" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500">
                  <template x-for="p in packagesForJob" :key="p.id">
                    <option :value="p.id" x-text="p.name + (p.price ? ' - ' + p.price.toFixed(2) : '')"></option>
                  </template>
                </select>
                @error('package_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Years of Experience</label>
                <input type="number" name="experience_years" min="0" max="60" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('experience_years')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Available From</label>
                <input type="date" name="available_from" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('available_from')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div class="flex items-center gap-2">
                <input type="checkbox" name="has_passport" value="1" class="rounded" />
                <label class="text-sm text-gray-700">I have a valid passport</label>
              </div>
              <div>
                <label class="block text-sm text-gray-700">Highest Education</label>
                <input type="text" name="education" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Diploma / Degree" />
                @error('education')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <div x-show="type==='tour'" x-cloak>
            <h3 class="text-lg font-semibold">Tour Details</h3>
            <div class="mt-4 grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700">Destination</label>
                <input type="text" name="destination" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Maasai Mara" />
                @error('destination')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Start Date</label>
                <input type="date" name="start_date" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">End Date</label>
                <input type="date" name="end_date" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Adults</label>
                <input type="number" name="adults" min="1" max="20" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" value="1" />
                @error('adults')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Children</label>
                <input type="number" name="children" min="0" max="20" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" value="0" />
                @error('children')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Budget ({{ config('app.currency', env('APP_CURRENCY','KES')) }})</label>
                <input type="number" step="0.01" min="0" name="budget" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('budget')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm text-gray-700">Accommodation</label>
                <input type="text" name="accommodation" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Lodge / Camp, 3-5 nights" />
                @error('accommodation')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold">Additional Information</h3>
            <textarea name="message" rows="4" class="mt-2 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Add any details or questions..."></textarea>
            @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
          </div>

          <div class="flex items-center justify-end gap-3">
            <a href="{{ route('client.dashboard') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Send Enquiry</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function enquiryForm() {
      return {
        type: 'job',
        jobId: '',
        packageId: '',
        packages: @json($packagesByJob ?? []),
        get packagesForJob() {
          return this.packages[this.jobId] || [];
        },
        onJobChange() {
          // Reset package when job changes
          this.packageId = '';
        }
      };
    }
  </script>
</x-app-layout>

