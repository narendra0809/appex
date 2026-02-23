<x-guest-layout>
    @section('title', 'Reset Password')

    <!-- Info Message -->
    <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-100 dark:border-indigo-800">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-indigo-500 text-lg"></i>
            </div>
            <p class="text-sm text-indigo-700 dark:text-indigo-300">
                {{ __('Forgot your password? No problem. Just enter your email address and we will send you a password reset link.') }}
            </p>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" :value="__('Email Address')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <span class="input-icon">
                    <i class="fas fa-envelope"></i>
                </span>
                <x-text-input id="email" 
                    class="input-with-icon block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    placeholder="Enter your email address" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-gradient w-full py-3 px-4 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-paper-plane"></i>
            {{ __('Send Reset Link') }}
        </button>
    </form>

    <!-- Back to Login -->
    <div class="mt-6 text-center">
        <a class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold transition-colors" href="{{ route('login') }}">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to Sign In') }}
        </a>
    </div>
</x-guest-layout>
