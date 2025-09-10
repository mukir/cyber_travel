<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings</h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if(session('success'))
        <div class="rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
      @endif

      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold">Company</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-4 grid gap-4">
          @csrf
          <input type="hidden" name="default_currency" value="{{ old('default_currency', $defaultCurrency) }}" />
          <div>
            <label class="block text-sm text-gray-700">Company WhatsApp Number (E.164)</label>
            <input type="text" name="company.whatsapp_number" value="{{ old('company.whatsapp_number', $company['whatsapp_number'] ?? '') }}" class="mt-1 w-full rounded border p-2" placeholder="2547XXXXXXXX" />
            @error('company.whatsapp_number')
              <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Used for client WhatsApp support links.</p>
          </div>
          <div>
            <label class="block text-sm text-gray-700">Company Admin Email</label>
            <input type="email" name="company.admin_email" value="{{ old('company.admin_email', $company['admin_email'] ?? '') }}" class="mt-1 w-full rounded border p-2" placeholder="admin@example.com" />
            @error('company.admin_email')
              <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Used for internal notifications (e.g., retainer summaries).</p>
          </div>
          <div>
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Save Settings</button>
          </div>
        </form>

        <h3 class="text-lg font-semibold">Commissions</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-4 grid gap-4">
          @csrf
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-700">Europe Fixed (KES)</label>
              <input type="number" step="1" min="0" name="commission.region.europe" value="{{ old('commission.region.europe', $commissions['europe'] ?? 10000) }}" class="mt-1 w-full rounded border p-2" />
              @error('commission.region.europe')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Gulf Fixed (KES)</label>
              <input type="number" step="1" min="0" name="commission.region.gulf" value="{{ old('commission.region.gulf', $commissions['gulf'] ?? 5000) }}" class="mt-1 w-full rounded border p-2" />
              @error('commission.region.gulf')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Americas Fixed (KES)</label>
              <input type="number" step="1" min="0" name="commission.region.americas" value="{{ old('commission.region.americas', $commissions['americas'] ?? 10000) }}" class="mt-1 w-full rounded border p-2" />
              @error('commission.region.americas')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Weekly Passport Bonus (KES per 3)</label>
              <input type="number" step="1" min="0" name="commission.bonus.passport_weekly" value="{{ old('commission.bonus.passport_weekly', $commissions['passport_weekly_bonus'] ?? 5000) }}" class="mt-1 w-full rounded border p-2" />
              @error('commission.bonus.passport_weekly')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Retainer (KES when target achieved)</label>
              <input type="number" step="1" min="0" name="commission.retainer.amount" value="{{ old('commission.retainer.amount', $commissions['retainer_amount'] ?? 30000) }}" class="mt-1 w-full rounded border p-2" />
              @error('commission.retainer.amount')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Payout Day (1–28)</label>
              <input type="number" min="1" max="28" step="1" name="commission.payout.day" value="{{ old('commission.payout.day', $commissions['payout_day'] ?? 15) }}" class="mt-1 w-full rounded border p-2" />
              @error('commission.payout.day')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
              <p class="mt-1 text-xs text-gray-500">Day of month to run payouts. Use a server cron: php artisan commissions:payout</p>
            </div>
          </div>
          <div>
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Save Commission Settings</button>
          </div>
        </form>

        <h3 class="text-lg font-semibold">General</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-4 grid gap-4">
          @csrf
          <div>
            <label class="block text-sm text-gray-700">Default Currency</label>
            <select name="default_currency" class="mt-1 w-40 rounded border p-2 uppercase">
              @foreach($currencies as $c)
                <option value="{{ $c }}" @selected(old('default_currency', $defaultCurrency) === $c)>{{ $c }}</option>
              @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Used when creating new applications. Default is KES.</p>
            @error('default_currency')
              <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div>
            <label class="block text-sm text-gray-700">Payment Plan (must total 100%)</label>
            <div class="mt-2 grid sm:grid-cols-3 gap-3">
              <div>
                <span class="text-xs text-gray-600">Deposit %</span>
                <input type="number" name="payment.plan.deposit_percent" min="0" max="100" step="0.01" value="{{ old('payment.plan.deposit_percent', $paymentPlan['deposit_percent'] ?? 50) }}" class="mt-1 w-full rounded border p-2" />
                @error('payment.plan.deposit_percent')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
              </div>
              <div>
                <span class="text-xs text-gray-600">Second %</span>
                <input type="number" name="payment.plan.second_percent" min="0" max="100" step="0.01" value="{{ old('payment.plan.second_percent', $paymentPlan['second_percent'] ?? 25) }}" class="mt-1 w-full rounded border p-2" />
                @error('payment.plan.second_percent')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
              </div>
              <div>
                <span class="text-xs text-gray-600">Final %</span>
                <input type="number" name="payment.plan.final_percent" min="0" max="100" step="0.01" value="{{ old('payment.plan.final_percent', $paymentPlan['final_percent'] ?? 25) }}" class="mt-1 w-full rounded border p-2" />
                @error('payment.plan.final_percent')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
              </div>
            </div>
            @if(session('error'))
              <div class="text-xs text-red-600 mt-1">{{ session('error') }}</div>
            @endif
          </div>

          <div>
            <label class="block text-sm text-gray-700">USD → KES Rate</label>
            <input type="number" step="0.01" min="1" name="currency.usd_to_kes" value="{{ old('currency.usd_to_kes', $usdToKes ?? 135) }}" class="mt-1 w-40 rounded border p-2" />
            @error('currency.usd_to_kes')
              <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Used to convert USD amounts when charging via M-PESA.</p>
          </div>

          <h3 class="text-lg font-semibold mt-4">Safaricom Daraja</h3>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-700">Base URL</label>
              <input type="url" name="safaricom.base_url" value="{{ old('safaricom.base_url', $safaricom['base_url']) }}" class="mt-1 w-full rounded border p-2" placeholder="https://sandbox.safaricom.co.ke" />
              @error('safaricom.base_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Shortcode</label>
              <input type="text" name="safaricom.shortcode" value="{{ old('safaricom.shortcode', $safaricom['shortcode']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.shortcode')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-700">Consumer Key</label>
              <input type="text" name="safaricom.consumer_key" value="{{ old('safaricom.consumer_key', $safaricom['consumer_key']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.consumer_key')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Consumer Secret</label>
              <input type="text" name="safaricom.consumer_secret" value="{{ old('safaricom.consumer_secret', $safaricom['consumer_secret']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.consumer_secret')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-700">Passkey</label>
              <input type="text" name="safaricom.passkey" value="{{ old('safaricom.passkey', $safaricom['passkey']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.passkey')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Initiator Name</label>
              <input type="text" name="safaricom.initiator_name" value="{{ old('safaricom.initiator_name', $safaricom['initiator_name']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.initiator_name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm text-gray-700">Security Credential</label>
              <input type="text" name="safaricom.security_credential" value="{{ old('safaricom.security_credential', $safaricom['security_credential']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.security_credential')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm text-gray-700">STK Callback URL</label>
              <input type="url" name="safaricom.stk_callback_url" value="{{ old('safaricom.stk_callback_url', $safaricom['stk_callback_url']) }}" class="mt-1 w-full rounded border p-2" placeholder="{{ url('/payments/mpesa/stk/callback') }}" />
              @error('safaricom.stk_callback_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-700">B2C Result URL</label>
              <input type="url" name="safaricom.b2c_result_url" value="{{ old('safaricom.b2c_result_url', $safaricom['b2c_result_url']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.b2c_result_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">B2C Timeout URL</label>
              <input type="url" name="safaricom.b2c_timeout_url" value="{{ old('safaricom.b2c_timeout_url', $safaricom['b2c_timeout_url']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.b2c_timeout_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-700">B2B Result URL</label>
              <input type="url" name="safaricom.b2b_result_url" value="{{ old('safaricom.b2b_result_url', $safaricom['b2b_result_url']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.b2b_result_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">B2B Timeout URL</label>
              <input type="url" name="safaricom.b2b_timeout_url" value="{{ old('safaricom.b2b_timeout_url', $safaricom['b2b_timeout_url']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.b2b_timeout_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-700">Balance Result URL</label>
              <input type="url" name="safaricom.balance_result_url" value="{{ old('safaricom.balance_result_url', $safaricom['balance_result_url']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.balance_result_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="block text-sm text-gray-700">Balance Timeout URL</label>
              <input type="url" name="safaricom.balance_timeout_url" value="{{ old('safaricom.balance_timeout_url', $safaricom['balance_timeout_url']) }}" class="mt-1 w-full rounded border p-2" />
              @error('safaricom.balance_timeout_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
          </div>

          <h3 class="text-lg font-semibold mt-6">PayPal</h3>
          <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2 flex items-center gap-3">
              <input type="hidden" name="paypal.enabled" value="0">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="paypal.enabled" value="1" class="rounded border" @checked(old('paypal.enabled', $paypal['enabled']) ? true : false) />
                <span class="text-sm text-gray-700">Enable PayPal</span>
              </label>
              @error('paypal.enabled')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            </div>

            <div>
              <label class="block text-sm text-gray-700">Mode</label>
              <select name="paypal.mode" class="mt-1 w-full rounded border p-2">
                @php($m = old('paypal.mode', $paypal['mode']))
                <option value="sandbox" @selected($m==='sandbox')>Sandbox</option>
                <option value="live" @selected($m==='live')>Live</option>
              </select>
              @error('paypal.mode')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm text-gray-700">Client ID</label>
              <input type="text" name="paypal.client_id" value="{{ old('paypal.client_id', $paypal['client_id']) }}" class="mt-1 w-full rounded border p-2" />
              @error('paypal.client_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm text-gray-700">Client Secret</label>
              <input type="password" name="paypal.client_secret" value="{{ old('paypal.client_secret', $paypal['client_secret']) }}" class="mt-1 w-full rounded border p-2" />
              @error('paypal.client_secret')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm text-gray-700">Base API URL</label>
              <input type="url" name="paypal.base_url" value="{{ old('paypal.base_url', $paypal['base_url']) }}" class="mt-1 w-full rounded border p-2" placeholder="https://api-m.sandbox.paypal.com" />
              @error('paypal.base_url')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
              <p class="mt-1 text-xs text-gray-500">Leave blank to auto-set from Mode.</p>
            </div>
          </div>

          <div class="mt-4">
            <button class="rounded bg-emerald-600 px-4 py-2 text-white font-semibold hover:bg-emerald-700">Save Settings</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
