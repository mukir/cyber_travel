                   @if(Auth::user()->is_client())

                    <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                        {{ __('Client Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('client.biodata')" :active="request()->routeIs('client.biodata')">
                        {{ __('Bio Data') }}
                    </x-nav-link>
                    <x-nav-link :href="route('client.documents')" :active="request()->routeIs('client.documents')">
                        {{ __('Documents') }}
                    </x-nav-link>
                    <x-nav-link :href="route('client.applications')" :active="request()->routeIs('client.applications')">
                        {{ __('My Applications') }}
                    </x-nav-link>
                    <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                        {{ __('Explore Services') }}
                    </x-nav-link>
                    <x-nav-link :href="route('client.enquiry')" :active="request()->routeIs('client.enquiry')">
                        {{ __('Service Enquiry') }}
                    </x-nav-link>
                    @endif
