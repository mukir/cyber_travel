<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = \Illuminate\Support\Facades\Auth::user();
        $target = route('dashboard', absolute: false);
        if ($user && method_exists($user, 'is_admin') && $user->is_admin()) {
            $target = route('admin.dashboard', absolute: false);
        } elseif ($user && method_exists($user, 'is_staff') && $user->is_staff()) {
            $target = route('staff.dashboard', absolute: false);
        } elseif ($user && method_exists($user, 'is_client') && $user->is_client()) {
            $target = route('client.dashboard', absolute: false);
        }

        return redirect()->intended($target);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
