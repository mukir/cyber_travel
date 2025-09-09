<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\RoundRobin;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Normalize phone and enforce uniqueness in client_profiles
        $normalized = \App\Helpers\Phone::toE164Digits($request->input('phone'));
        if (!$normalized) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'phone' => 'Enter a valid phone number.',
            ]);
        }
        if (\App\Models\ClientProfile::where('phone', $normalized)->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'phone' => 'The phone has already been taken.',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::Client,
        ]);

        // Best-effort: create/update client profile with provided phone
        try {
            $phone = $normalized;
            if ($phone) {
                \App\Models\ClientProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $phone,
                    ]
                );
            } else {
                // Ensure profile exists with base identity
                \App\Models\ClientProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    ['name' => $user->name, 'email' => $user->email]
                );
            }
        } catch (\Throwable $e) {
            // swallow profile errors to avoid blocking registration
        }

        // Auto-assign a sales rep in round-robin for new signups
        try {
            $profile = \App\Models\ClientProfile::firstOrNew(['user_id' => $user->id]);
            if (empty($profile->sales_rep_id)) {
                $nextStaffId = RoundRobin::nextStaffId('sales_rep');
                if ($nextStaffId) {
                    $profile->sales_rep_id = $nextStaffId;
                    if (!$profile->name) { $profile->name = $user->name; }
                    if (!$profile->email) { $profile->email = $user->email; }
                    $profile->save();
                }
            }
        } catch (\Throwable $e) {
            // ignore assignment failures
        }

        event(new Registered($user));

        Auth::login($user);

        $target = route('client.dashboard', absolute: false);
        if (method_exists($user, 'is_admin') && $user->is_admin()) {
            $target = route('admin.dashboard', absolute: false);
        } elseif (method_exists($user, 'is_staff') && $user->is_staff()) {
            $target = route('staff.dashboard', absolute: false);
        }

        return redirect($target);
    }
}
