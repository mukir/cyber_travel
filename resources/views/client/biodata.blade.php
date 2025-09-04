<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
      <div>
          <h2 class="font-bold text-2xl text-gray-900 leading-tight">Personal Details</h2>
          <p class="mt-1 text-sm text-gray-600">Complete your profile information for better service</p>
        </div>
      </div>
      <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
        Back to Dashboard
      </a>
    </div>
  </x-slot>

  <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
      <!-- Support quick access -->
      <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="flex items-center justify-center w-10 h-10 bg-emerald-200 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-800" viewBox="0 0 20 20" fill="currentColor"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-9 4a1 1 0 102 0 1 1 0 00-2 0zm.25-7a.75.75 0 000 1.5h.5a1.25 1.25 0 011.25 1.25v.25a1 1 0 01-2 0 .75.75 0 00-1.5 0 2.5 2.5 0 005 0v-.25A2.75 2.75 0 0010.75 7h-.5A.75.75 0 009.25 6z"/></svg>
          </div>
          <div>
            <div class="font-semibold text-emerald-900">Need help?</div>
            <div class="text-sm text-emerald-800">Send a support request and our team will assist you.</div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('client.support.tickets') }}" class="text-sm text-emerald-800 hover:underline">View My Tickets</a>
          <a href="{{ route('client.support') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-white font-medium hover:bg-emerald-700">
            Open Support Form
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h8.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L12.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>
          </a>
        </div>
      </div>
      <!-- Profile Completeness Card -->
      <div class="mb-8 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-lg backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-white">Profile Completeness</h3>
                <p class="text-emerald-100 text-sm">Keep your information up to date</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-3xl font-bold text-white">{{ $completeness ?? 0 }}%</div>
              <div class="text-emerald-100 text-sm">Complete</div>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="mb-4">
            <div class="h-3 w-full rounded-full bg-gray-200 overflow-hidden shadow-inner" role="progressbar" aria-valuenow="{{ $completeness ?? 0 }}" aria-valuemin="0" aria-valuemax="100" aria-label="Profile completion">
              <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-600 transition-all duration-700 ease-out rounded-full shadow-sm" style="width: {{ (int)($completeness ?? 0) }}%"></div>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
              <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-900">Fields Completed</div>
                <div class="text-lg font-bold text-gray-700">{{ $fieldsFilled ?? 0 }}/{{ $fieldsTotal ?? 0 }}</div>
              </div>
            </div>
            
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
              <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-900">Documents Uploaded</div>
                <div class="text-lg font-bold text-gray-700">{{ $docsUploaded ?? 0 }}/{{ $docsTotal ?? 0 }}</div>
        </div>
        </div>
          </div>
          
          @if(isset($missingDocs) && $missingDocs->count())
            <div class="mt-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
              <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-lg flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm text-amber-800">
                  <span class="font-medium">Missing documents:</span> {{ $missingDocs->implode(', ') }}. 
                  <a class="inline-flex items-center gap-1 font-medium text-amber-700 hover:text-amber-900 underline underline-offset-2 transition-colors" href="{{ route('client.documents') }}">
                    Upload now
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </a>
                </p>
              </div>
          </div>
        @endif
        </div>
      </div>

      @if ($errors->any())
        <div class="mb-6 bg-white rounded-2xl border border-red-200 shadow-lg overflow-hidden">
          <div class="bg-red-50 px-6 py-4 border-b border-red-200">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <h3 class="text-sm font-semibold text-red-800">Please fix the following errors:</h3>
            </div>
          </div>
          <div class="p-6">
            <ul class="space-y-2 text-sm text-red-700">
              @foreach ($errors->all() as $error)
                <li class="flex items-start gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  {{ $error }}
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <!-- Main Form Card -->
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
          <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 bg-emerald-100 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </div>
              <div>
              <h3 class="text-lg font-semibold text-gray-900">Personal Information Form</h3>
              <p class="text-sm text-gray-600">Fill in your details to complete your profile</p>
            </div>
          </div>
        </div>
        
        <form method="POST" action="{{ route('client.biodata.store') }}" class="p-6 space-y-8">
          @csrf

          <!-- Personal Details Section -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
            <div class="flex items-center gap-3 mb-6">
              <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Personal Details</h3>
                <p class="text-sm text-gray-600">Your basic personal information</p>
              </div>
              <div class="ml-auto">
                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                  <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                  Required fields marked with <span class="text-red-500 font-semibold">*</span>
                  </span>
              </div>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $profile->name) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('name') border-red-300 ring-4 ring-red-100 @enderror" placeholder="Enter your full name" required @error('name') aria-invalid="true" @enderror autofocus />
                @error('name')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Date of Birth
                </label>
                <input type="date" name="dob" value="{{ old('dob', $profile->dob) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('dob') border-red-300 ring-4 ring-red-100 @enderror" />
                @error('dob')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                  Gender
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      Select or type your gender
                    </div>
                  </div>
                </label>
                <input list="genderOptions" type="text" name="gender" value="{{ old('gender', $profile->gender) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('gender') border-red-300 ring-4 ring-red-100 @enderror" placeholder="e.g. Male / Female" />
                <datalist id="genderOptions">
                  <option value="Male" />
                  <option value="Female" />
                  <option value="Other" />
                  <option value="Prefer not to say" />
                </datalist>
                @error('gender')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                  </svg>
                  National ID / Passport
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      Enter as it appears on the document
                    </div>
                  </div>
                </label>
                <input type="text" name="id_no" value="{{ old('id_no', $profile->id_no) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('id_no') border-red-300 ring-4 ring-red-100 @enderror" placeholder="e.g. 12345678 or AB123456" />
                @error('id_no')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  County
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      Your county of residence
                    </div>
                  </div>
                </label>
                <input type="text" name="county" value="{{ old('county', $profile->county) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('county') border-red-300 ring-4 ring-red-100 @enderror" placeholder="e.g. Nairobi" />
                @error('county')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <!-- Contact Section -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
            <div class="flex items-center gap-3 mb-6">
              <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                <p class="text-sm text-gray-600">How we can reach you for updates</p>
              </div>
              <div class="ml-auto">
                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 5h6V3H4v2zM4 11h6V9H4v2z" />
                  </svg>
                  Used for notifications
                  </span>
              </div>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  Phone Number
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      Use Kenyan format: 07XXXXXXXX
                    </div>
                  </div>
                </label>
                <input type="tel" name="phone" value="{{ old('phone', $profile->phone) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-green-500 focus:ring-4 focus:ring-green-100 @error('phone') border-red-300 ring-4 ring-red-100 @enderror" placeholder="07XXXXXXXX" pattern="^(?:0|\+?254)?7\d{8}$" />
                @error('phone')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  Email Address
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      We'll use this to contact you
                    </div>
                  </div>
                </label>
                <input type="email" name="email" value="{{ old('email', $profile->email) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-green-500 focus:ring-4 focus:ring-green-100 @error('email') border-red-300 ring-4 ring-red-100 @enderror" placeholder="e.g. jane@example.com" />
                @error('email')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="md:col-span-2 space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                  Next of Kin
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      Emergency contact: full name and phone
                    </div>
                  </div>
                </label>
                <input type="text" name="next_of_kin" value="{{ old('next_of_kin', $profile->next_of_kin) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-green-500 focus:ring-4 focus:ring-green-100 @error('next_of_kin') border-red-300 ring-4 ring-red-100 @enderror" placeholder="Full name and phone (optional)" />
                @error('next_of_kin')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <!-- Application Section -->
          <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-100">
            <div class="flex items-center gap-3 mb-6">
              <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Application Details</h3>
                <p class="text-sm text-gray-600">Service and tracking information</p>
              </div>
              <div class="ml-auto">
                <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  For internal tracking
                </span>
              </div>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
              <div class="md:col-span-2 space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                  Service Package
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      E.g. Standard / Premium. See Jobs for details
                    </div>
                  </div>
                </label>
                <input type="text" name="service_package" value="{{ old('service_package', $profile->service_package) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-500 shadow-sm transition-all duration-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 @error('service_package') border-red-300 ring-4 ring-red-100 @enderror" placeholder="e.g. Standard / Premium" />
                @error('service_package')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Application Status
                  <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 cursor-help" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      For internal tracking. Leave default unless advised
                    </div>
                  </div>
                </label>
                @php($currentStatus = old('status', $profile->status))
                <select name="status" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition-all duration-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 @error('status') border-red-300 ring-4 ring-red-100 @enderror">
                  @foreach(['Enquiry','Payment','Processing','Complete'] as $st)
                    <option value="{{ $st }}" @selected($currentStatus === $st)>{{ $st }}</option>
                  @endforeach
                </select>
                @error('status')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Application Date
                </label>
                <input type="date" name="application_date" value="{{ old('application_date', $profile->application_date) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition-all duration-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 @error('application_date') border-red-300 ring-4 ring-red-100 @enderror" />
                @error('application_date')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Interview Date
                </label>
                <input type="date" name="interview_date" value="{{ old('interview_date', $profile->interview_date) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition-all duration-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 @error('interview_date') border-red-300 ring-4 ring-red-100 @enderror" />
                @error('interview_date')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
              
              <div class="space-y-2">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  Travel Date
                </label>
                <input type="date" name="travel_date" value="{{ old('travel_date', $profile->travel_date) }}" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-sm transition-all duration-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 @error('travel_date') border-red-300 ring-4 ring-red-100 @enderror" />
                @error('travel_date')<p class="flex items-center gap-1 text-sm text-red-600 mt-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ $message }}</p>@enderror
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="bg-gray-50 -mx-6 -mb-6 px-6 py-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 hover:shadow-md transition-all duration-200 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
              </a>
              <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 text-sm font-bold text-white bg-emerald-600 border-2 border-emerald-600 rounded-xl hover:bg-emerald-700 hover:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Changes
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
