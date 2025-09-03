<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="Cyber Travel" class="mx-auto" style="width: 120px;">
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900 text-center mb-6">
            Welcome Back
        </h1>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Email Verification Notice -->
        @if (session('verification_notice'))
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-yellow-700">
                        <strong>Email Verification Required:</strong> Please verify your email address to access your account.
                    </p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username" 
                    placeholder="Enter your email address" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input 
                    id="password" 
                    class="block mt-1 w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" 
                    placeholder="Enter your password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center mt-4">
                <label for="remember_me" class="inline-flex items-center text-gray-600">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                        name="remember">
                    <span class="ml-2 text-sm">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between mt-6">
                <div class="text-sm">
                    @if (Route::has('password.request'))
                        <a 
                            class="underline text-gray-600 hover:text-indigo-600 transition-colors duration-200" 
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <x-primary-button class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                    {{ __('Sign In') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                {{ __("Don't have an account?") }}
                <a 
                    class="underline text-indigo-600 hover:text-indigo-700 transition-colors duration-200" 
                    href="{{ route('register') }}">
                    {{ __('Create Account') }}
                </a>
            </p>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 p-3 bg-gray-50 rounded-md">
            <p class="text-xs text-gray-500 text-center">
                🔒 Secure login with email verification required for new accounts.
            </p>
        </div>
    </div>
</x-guest-layout>
