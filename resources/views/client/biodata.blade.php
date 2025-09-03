<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bio Data</h2>
      <a href="{{ route('client.dashboard') }}" class="text-sm text-emerald-700 hover:underline">&larr; Back to Dashboard</a>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="mb-6 bg-white shadow sm:rounded-lg p-6">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold">Profile Completeness</h3>
          <span class="text-sm text-gray-600">{{ $completeness ?? 0 }}%</span>
        </div>
        <div class="mt-2 h-2 w-full rounded bg-gray-200 overflow-hidden" role="progressbar" aria-valuenow="{{ $completeness ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
          <div class="h-full bg-emerald-600" style="width: {{ (int)($completeness ?? 0) }}%"></div>
        </div>
        <p class="mt-2 text-xs text-gray-600">Fields: {{ $fieldsFilled ?? 0 }}/{{ $fieldsTotal ?? 0 }} • Documents: {{ $docsUploaded ?? 0 }}/{{ $docsTotal ?? 0 }}</p>
        @if(isset($missingDocs) && $missingDocs->count())
          <div class="mt-3 rounded border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
            Missing documents: {{ $missingDocs->implode(', ') }}. <a class="underline" href="{{ route('client.documents') }}">Upload now</a>.
          </div>
        @endif
      </div>

      @if ($errors->any())
        <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800">
          Please fix the errors below and try again.
        </div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <form method="POST" action="{{ route('client.biodata.store') }}" class="space-y-8">
          @csrf

          <div>
            <h3 class="text-lg font-semibold">Personal Details</h3>
            <div class="mt-4 grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700">Full Name <span class="text-red-600">*</span></label>
                <input type="text" name="name" value="{{ old('name', $profile->name) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" required />
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob', $profile->dob) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('dob')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Gender
                  <span class="align-middle text-gray-400" title="Select or type your gender.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                <input list="genderOptions" type="text" name="gender" value="{{ old('gender', $profile->gender) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Male / Female" />
                <datalist id="genderOptions">
                  <option value="Male" />
                  <option value="Female" />
                  <option value="Other" />
                  <option value="Prefer not to say" />
                </datalist>
                @error('gender')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">National ID / Passport
                  <span class="align-middle text-gray-400" title="Enter your National ID or Passport as it appears on the document.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                <input type="text" name="id_no" value="{{ old('id_no', $profile->id_no) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('id_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">County
                  <span class="align-middle text-gray-400" title="Your county of residence.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                <input type="text" name="county" value="{{ old('county', $profile->county) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('county')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold">Contact</h3>
            <div class="mt-4 grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700">Phone
                  <span class="align-middle text-gray-400" title="Use Kenyan format 07XXXXXXXX.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                <input type="tel" name="phone" value="{{ old('phone', $profile->phone) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="07XXXXXXXX" pattern="^(?:0|\+?254)?7\d{8}$" />
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Email
                  <span class="align-middle text-gray-400" title="We will use this to contact you.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                <input type="email" name="email" value="{{ old('email', $profile->email) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm text-gray-700">Next of Kin
                  <span class="align-middle text-gray-400" title="Emergency contact: full name and phone.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                <input type="text" name="next_of_kin" value="{{ old('next_of_kin', $profile->next_of_kin) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Full name and contact (optional)" />
                @error('next_of_kin')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold">Application</h3>
            <div class="mt-4 grid md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="block text-sm text-gray-700">Service Package
                  <span class="align-middle text-gray-400" title="E.g. Standard / Premium. See Jobs for details.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                <input type="text" name="service_package" value="{{ old('service_package', $profile->service_package) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Standard / Premium" />
                @error('service_package')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Status
                  <span class="align-middle text-gray-400" title="For internal tracking. Leave default unless advised.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0z"/><path d="M9 7h2v5H9V7zm1 8a1 1 0 100-2 1 1 0 000 2z"/></svg>
                  </span>
                </label>
                @php($currentStatus = old('status', $profile->status))
                <select name="status" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500">
                  @foreach(['Enquiry','Payment','Processing','Complete'] as $st)
                    <option value="{{ $st }}" @selected($currentStatus === $st)>{{ $st }}</option>
                  @endforeach
                </select>
                @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Application Date</label>
                <input type="date" name="application_date" value="{{ old('application_date', $profile->application_date) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('application_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Interview Date</label>
                <input type="date" name="interview_date" value="{{ old('interview_date', $profile->interview_date) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('interview_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700">Travel Date</label>
                <input type="date" name="travel_date" value="{{ old('travel_date', $profile->travel_date) }}" class="mt-1 w-full rounded border p-2 focus:border-emerald-500 focus:ring-emerald-500" />
                @error('travel_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3">
            <a href="{{ route('client.dashboard') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button type="submit" class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
