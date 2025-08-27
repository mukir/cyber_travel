<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bio Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('client.biodata.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name', $profile->name) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob', $profile->dob) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $profile->email) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Gender</label>
                        <input type="text" name="gender" value="{{ old('gender', $profile->gender) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>ID No.</label>
                        <input type="text" name="id_no" value="{{ old('id_no', $profile->id_no) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>County</label>
                        <input type="text" name="county" value="{{ old('county', $profile->county) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Next of Kin</label>
                        <input type="text" name="next_of_kin" value="{{ old('next_of_kin', $profile->next_of_kin) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Service Package</label>
                        <input type="text" name="service_package" value="{{ old('service_package', $profile->service_package) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Status</label>
                        <select name="status" class="border rounded w-full">
                            @foreach(['Enquiry','Payment','Processing','Complete'] as $status)
                                <option value="{{ $status }}" @selected($profile->status === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Application Date</label>
                        <input type="date" name="application_date" value="{{ old('application_date', $profile->application_date) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Interview Date</label>
                        <input type="date" name="interview_date" value="{{ old('interview_date', $profile->interview_date) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <label>Travel Date</label>
                        <input type="date" name="travel_date" value="{{ old('travel_date', $profile->travel_date) }}" class="border rounded w-full"/>
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
