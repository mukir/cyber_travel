<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium">Service Package: {{ $profile->service_package ?? 'N/A' }}</h3>
                <p>Status: {{ $profile->status }}</p>
                <p>Application Date: {{ $profile->application_date }}</p>
                <p>Interview Date: {{ $profile->interview_date }}</p>
                <p>Travel Date: {{ $profile->travel_date }}</p>
            </div>

            @if($missingDocs->isNotEmpty())
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                    <p class="font-bold">Missing Documents:</p>
                    <ul>
                        @foreach($missingDocs as $doc)
                            <li>{{ ucfirst(str_replace('_', ' ', $doc)) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
