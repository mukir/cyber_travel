<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md text-center">
        <!-- Logo -->
        <div class="mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="Cyber Travel" class="mx-auto" style="width: 120px;">
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900 mb-4">
            Verify Your Email Address
        </h1>

        <!-- Status Message -->
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('status') }}
            </div>
        @endif

        <!-- Description -->
        <p class="text-gray-600 mb-6">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>

        <!-- Verification Status -->
        @if (session('verification_link_sent'))
            <div class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-md">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <!-- Resend Form -->
        <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
            @csrf
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                Resend Verification Email
            </button>
        </form>

        <!-- Logout Option -->
        <form method="POST" action="{{ route('logout') }}" class="mb-6">
            @csrf
            <button type="submit" class="w-full bg-gray-200 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                Logout
            </button>
        </form>

        <!-- Help Text -->
        <p class="text-sm text-gray-500">
            Didn't receive the email? Check your spam folder or contact support if the problem persists.
        </p>
    </div>
</x-guest-layout>
