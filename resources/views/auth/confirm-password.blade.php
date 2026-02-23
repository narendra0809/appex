<x-guest-layout>
    @section('title', 'Confirm Password')

    <!-- Info Message -->
    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/30 rounded-xl border border-amber-100 dark:border-amber-800">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fas fa-shield-alt text-amber-500 text-lg"></i>
            </div>
            <p class="text-sm text-amber-700 dark:text-amber-300">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-6">
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

        <!-- Submit Button -->
        <button type="submit" class="btn-gradient w-full py-3 px-4 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ __('Confirm') }}
        </button>
    </form>

    <!-- Back to Login -->
    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 font-medium transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
