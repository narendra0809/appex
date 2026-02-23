<x-guest-layout>
    @section('title', 'Sign In')

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                    autofocus 
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
                    autocomplete="current-password" 
                    placeholder="Enter your password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600" name="remember">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-gradient w-full py-3 px-4 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-sign-in-alt"></i>
            {{ __('Sign In') }}
        </button>
    </form>

    <!-- Register Link -->
    @if (Route::has('register'))
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __("Don't have an account?") }}
                <a class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold transition-colors" href="{{ route('register') }}">
                    {{ __('Create Account') }}
                </a>
            </p>
        </div>
    @endif
</x-guest-layout>
