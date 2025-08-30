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
                    <x-nav-link :href="route('client.bookings')" :active="request()->routeIs('client.bookings')">
                        {{ __('My Bookings') }}
                    </x-nav-link>
                    <x-nav-link :href="route('jobs.index')" :active="request()->routeIs('jobs.*')">
                        {{ __('Apply for Job') }}
                    </x-nav-link>
                    @endif
