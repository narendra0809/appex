<x-guest-layout>
    @section('title', 'Set New Password')

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-5">
            <x-input-label for="email" :value="__('Email Address')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <span class="input-icon">
                    <i class="fas fa-envelope"></i>
                </span>
                <x-text-input id="email" 
                    class="input-with-icon block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200" 
                    type="email" 
                    name="email" 
                    :value="old('email', $request->email)" 
                    required 
                    autofocus 
                    autocomplete="username" 
                    placeholder="Enter your email address" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <x-input-label for="password" :value="__('New Password')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <span class="input-icon">
                    <i class="fas fa-lock"></i>
                </span>
                <x-text-input id="password" 
                    class="input-with-icon block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password" 
                    placeholder="Enter new password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <span class="input-icon">
                    <i class="fas fa-lock"></i>
                </span>
                <x-text-input id="password_confirmation" 
                    class="input-with-icon block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                    placeholder="Confirm new password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-gradient w-full py-3 px-4 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-key"></i>
            {{ __('Reset Password') }}
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
