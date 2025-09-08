                   @if(Auth::user()->is_admin())

                   <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                       {{ __('Dashboard') }}
                   </x-nav-link>
                    <x-nav-link :href="route('admin.clients')" :active="request()->routeIs('admin.clients')">
                        {{ __('Clients') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                        {{ __('Applications') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.sales')" :active="request()->routeIs('admin.sales')">
                        {{ __('Sales') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.payments')" :active="request()->routeIs('admin.payments')">
                        {{ __('Payments') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.leads.index')" :active="request()->routeIs('admin.leads.*')">
                        {{ __('Leads') }}
                    </x-nav-link>
                    {{-- Other admin links are available in the profile dropdown --}}
                    @endif
