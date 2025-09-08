<?php

namespace App\Http\Controllers;

use App\Helpers\Settings as SettingsHelper;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $defaultCurrency = SettingsHelper::get('default_currency', config('app.currency', env('APP_CURRENCY', 'KES')));
        $usdToKes = (float) SettingsHelper::get('currency.usd_to_kes', 135);
        $currencies = [
            'KES','USD','EUR','GBP','UGX','TZS','NGN','ZAR','INR','AUD','CAD','CHF','CNY','JPY','AED','SAR'
        ];

        $safaricom = [
            'base_url'            => SettingsHelper::get('safaricom.base_url', env('SAFARICOM_DARAJA_BASE_URL')),
            'consumer_key'        => SettingsHelper::get('safaricom.consumer_key', env('SAFARICOM_DARAJA_CONSUMER_KEY')),
            'consumer_secret'     => SettingsHelper::get('safaricom.consumer_secret', env('SAFARICOM_DARAJA_CONSUMER_SECRET')),
            'passkey'             => SettingsHelper::get('safaricom.passkey', env('SAFARICOM_PASSKEY')),
            'shortcode'           => SettingsHelper::get('safaricom.shortcode', env('SAFARICOM_SHORTCODE')),
            'initiator_name'      => SettingsHelper::get('safaricom.initiator_name', env('SAFARICOM_INITIATOR_NAME')),
            'security_credential' => SettingsHelper::get('safaricom.security_credential', env('SecurityCredential')),
            'stk_callback_url'    => SettingsHelper::get('safaricom.stk_callback_url', env('SAFARICOM_STK_CALLBACK_URL', url('/payments/mpesa/stk/callback'))),
            'b2c_timeout_url'     => SettingsHelper::get('safaricom.b2c_timeout_url', env('SAFARICOM_B2C_TIMEOUT_URL')),
            'b2c_result_url'      => SettingsHelper::get('safaricom.b2c_result_url', env('SAFARICOM_B2C_RESULT_URL')),
            'b2b_timeout_url'     => SettingsHelper::get('safaricom.b2b_timeout_url', env('SAFARICOM_B2B_TIMEOUT_URL')),
            'b2b_result_url'      => SettingsHelper::get('safaricom.b2b_result_url', env('SAFARICOM_B2B_RESULT_URL')),
            'balance_timeout_url' => SettingsHelper::get('safaricom.balance_timeout_url', env('SAFARICOM_BALANCE_TIMEOUT_URL')),
            'balance_result_url'  => SettingsHelper::get('safaricom.balance_result_url', env('SAFARICOM_BALANCE_RESULT_URL')),
        ];

        $paypal = [
            'enabled'       => (bool) SettingsHelper::get('paypal.enabled', true),
            'mode'          => SettingsHelper::get('paypal.mode', env('PAYPAL_MODE', 'sandbox')),
            'client_id'     => SettingsHelper::get('paypal.client_id', env('PAYPAL_CLIENT_ID')),
            'client_secret' => SettingsHelper::get('paypal.client_secret', env('PAYPAL_CLIENT_SECRET')),
            'base_url'      => SettingsHelper::get('paypal.base_url', env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com')),
        ];
        $paymentPlan = [
            'deposit_percent' => (float) SettingsHelper::get('payment.plan.deposit_percent', 50),
            'second_percent'  => (float) SettingsHelper::get('payment.plan.second_percent', 25),
            'final_percent'   => (float) SettingsHelper::get('payment.plan.final_percent', 25),
        ];
        $company = [
            'whatsapp_number' => SettingsHelper::get('company.whatsapp_number', ''),
        ];

        return view('admin.settings', compact('defaultCurrency', 'usdToKes', 'currencies', 'safaricom', 'paypal', 'paymentPlan', 'company'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'default_currency'      => ['required', 'in:KES,USD,EUR,GBP,UGX,TZS,NGN,ZAR,INR,AUD,CAD,CHF,CNY,JPY,AED,SAR'],
            // Currency
            'currency.usd_to_kes'   => ['nullable', 'numeric', 'min:1'],
            // Payment plan
            'payment.plan.deposit_percent' => ['nullable','numeric','min:0','max:100'],
            'payment.plan.second_percent'  => ['nullable','numeric','min:0','max:100'],
            'payment.plan.final_percent'   => ['nullable','numeric','min:0','max:100'],
            'safaricom.base_url'     => ['nullable', 'url'],
            'safaricom.consumer_key' => ['nullable', 'string'],
            'safaricom.consumer_secret' => ['nullable', 'string'],
            'safaricom.passkey'      => ['nullable', 'string'],
            'safaricom.shortcode'    => ['nullable', 'string'],
            'safaricom.initiator_name' => ['nullable', 'string'],
            'safaricom.security_credential' => ['nullable', 'string'],
            'safaricom.stk_callback_url' => ['nullable', 'url'],
            'safaricom.b2c_timeout_url' => ['nullable', 'url'],
            'safaricom.b2c_result_url' => ['nullable', 'url'],
            'safaricom.b2b_timeout_url' => ['nullable', 'url'],
            'safaricom.b2b_result_url' => ['nullable', 'url'],
            'safaricom.balance_timeout_url' => ['nullable', 'url'],
            'safaricom.balance_result_url' => ['nullable', 'url'],
            // PayPal
            'paypal.enabled'      => ['nullable', 'boolean'],
            'paypal.mode'         => ['nullable', 'in:sandbox,live'],
            'paypal.client_id'     => ['nullable', 'string'],
            'paypal.client_secret' => ['nullable', 'string'],
            'paypal.base_url'      => ['nullable', 'url'],
            // Company
            'company.whatsapp_number' => ['nullable', 'string', 'max:30'],
        ]);

        SettingsHelper::set('default_currency', strtoupper(trim($data['default_currency'])));

        // Validate payment plan sum equals 100 if provided
        $dp = (float)($data['payment.plan.deposit_percent'] ?? SettingsHelper::get('payment.plan.deposit_percent', 50));
        $sp = (float)($data['payment.plan.second_percent'] ?? SettingsHelper::get('payment.plan.second_percent', 25));
        $fp = (float)($data['payment.plan.final_percent'] ?? SettingsHelper::get('payment.plan.final_percent', 25));
        if (round($dp + $sp + $fp, 2) !== 100.00) {
            return back()->with('error', 'Payment plan percentages must add up to 100.');
        }

        // If mode provided and base_url empty, derive automatically based on mode
        if (!filled($data['paypal.base_url'] ?? null) && isset($data['paypal.mode'])) {
            $derived = $data['paypal.mode'] === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
            SettingsHelper::set('paypal.base_url', $derived);
        }

        foreach ($data as $key => $value) {
            if ($key === 'default_currency') continue;
            // Skip setting empty base_url to avoid wiping derived or existing value
            if ($key === 'paypal.base_url' && !filled($value)) continue;
            SettingsHelper::set($key, $value);
        }

        return back()->with('success', 'Settings updated.');
    }
}
