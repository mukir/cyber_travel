<?php

namespace App\Http\Controllers;

use App\Helpers\Settings as SettingsHelper;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $defaultCurrency = SettingsHelper::get('default_currency', config('app.currency', env('APP_CURRENCY', 'KES')));
        return view('admin.settings', compact('defaultCurrency'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'default_currency' => ['required', 'string', 'max:10'],
        ]);

        SettingsHelper::set('default_currency', strtoupper(trim($data['default_currency'])));

        return back()->with('success', 'Settings updated.');
    }
}

