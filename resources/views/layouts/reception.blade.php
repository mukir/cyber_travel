                   @if(method_exists(Auth::user(), 'is_reception') && Auth::user()->is_reception())

                    <x-nav-link :href="route('reception.dashboard')" :active="request()->routeIs('reception.dashboard')">
                        {{ __('Reception Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reception.clients')" :active="request()->routeIs('reception.clients')">
                        {{ __('Client Status') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reception.visitors')" :active="request()->routeIs('reception.visitors')">
                        {{ __('Visitors Book') }}
                    </x-nav-link>
                    @endif

