                   @if(Auth::user()->is_staff())

                    <x-nav-link :href="route('staff.dashboard')" :active="request()->routeIs('staff.dashboard')">
                        {{ __('Sales Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('staff.leads')" :active="request()->routeIs('staff.leads')">
                        {{ __('Leads') }}
                    </x-nav-link>

                    <x-nav-link :href="route('staff.clients')" :active="request()->routeIs('staff.clients')">
                        {{ __('My Clients') }}
                    </x-nav-link>
              
              
                    <x-nav-link :href="route('staff.commissions')" :active="request()->routeIs('staff.commissions')">
                        {{ __('Commissions') }}
                    </x-nav-link>
                    <x-nav-link :href="route('staff.reports')" :active="request()->routeIs('staff.reports')">
                        {{ __('Reports') }}
                    </x-nav-link>
                  
                    <x-nav-link :href="route('staff.payments')" :active="request()->routeIs('staff.payments')">
                        {{ __('Payment History') }}
                    </x-nav-link>
                    <x-nav-link :href="route('staff.targets')" :active="request()->routeIs('staff.targets')">
                        {{ __('Targets') }}
                    </x-nav-link>
                    <x-nav-link :href="route('staff.referrals')" :active="request()->routeIs('staff.referrals')">
                        {{ __('Referrals') }}
                    </x-nav-link>
                    @endif
