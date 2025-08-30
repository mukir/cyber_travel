<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full px-4 py-2 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input 
                    id="password" 
                    class="block mt-1 w-full px-4 py-2 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" />

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

                <x-primary-button class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Register Link -->
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                {{ __("Don't have an account?") }}
                <a 
                    class="underline text-indigo-600 hover:text-indigo-700 transition-colors duration-200" 
                    href="{{ route('register') }}">
                    {{ __('Register') }}
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
