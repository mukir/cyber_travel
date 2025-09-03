<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="Cyber Travel" class="mx-auto" style="width: 120px;">
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900 text-center mb-6">
            Create Your Account
        </h1>

        <!-- Email Verification Notice -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-sm text-blue-700">
                    <strong>Secure Registration:</strong> We'll send you an email verification link to activate your account.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input id="email" class="block mt-1 w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email address" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none"
                                type="password"
                                name="password"
                                required autocomplete="new-password" 
                                placeholder="Create a strong password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full px-4 py-3 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:outline-none"
                                type="password" 
                                name="password_confirmation" 
                                required 
                                autocomplete="new-password" 
                                placeholder="Confirm your password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Terms and Conditions -->
            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" required>
                    <span class="ml-2 text-sm text-gray-600">
                        I agree to the 
                        <a href="#" class="text-indigo-600 hover:text-indigo-500 underline">Terms of Service</a> 
                        and 
                        <a href="#" class="text-indigo-600 hover:text-indigo-500 underline">Privacy Policy</a>
                    </span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already have an account?') }}
                </a>

                <x-primary-button class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                    {{ __('Create Account') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Security Notice -->
        <div class="mt-6 p-3 bg-gray-50 rounded-md">
            <p class="text-xs text-gray-500 text-center">
                🔒 Your information is secure and encrypted. We'll never share your personal details.
            </p>
        </div>
    </div>
</x-guest-layout>
