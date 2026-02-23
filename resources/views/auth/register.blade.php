<x-guest-layout>
    @section('title', 'Create Account')

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-5">
            <x-input-label for="name" :value="__('Full Name')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
            <div class="relative">
                <span class="input-icon">
                    <i class="fas fa-user"></i>
                </span>
                <x-text-input id="name" 
                    class="input-with-icon block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 transition-all duration-200" 
                    type="text" 
                    name="name" 
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name" 
                    placeholder="Enter your full name" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

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
                    :value="old('email')" 
                    required 
                    autocomplete="username" 
                    placeholder="Enter your email address" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <x-input-label for="password" :value="__('Password')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
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
                    placeholder="Create a password" />
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
                    placeholder="Confirm your password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-gradient w-full py-3 px-4 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-user-plus"></i>
            {{ __('Create Account') }}
        </button>
    </form>

    <!-- Login Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Already have an account?') }}
            <a class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold transition-colors" href="{{ route('login') }}">
                {{ __('Sign In') }}
            </a>
        </p>
    </div>
</x-guest-layout>
