                   @if(Auth::user()->is_admin())

                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Admin Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.clients')" :active="request()->routeIs('admin.clients')">
                        {{ __('Clients') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.sales')" :active="request()->routeIs('admin.sales')">
                        {{ __('Sales') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.payments')" :active="request()->routeIs('admin.payments')">
                        {{ __('Payments') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                        {{ __('Reports') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.communications')" :active="request()->routeIs('admin.communications')">
                        {{ __('Communication') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                        {{ __('Logs') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.notes')" :active="request()->routeIs('admin.notes')">
                        {{ __('Notes') }}
                    </x-nav-link>
                    @endif

