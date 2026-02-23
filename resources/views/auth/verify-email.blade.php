<x-guest-layout>
    @section('title', 'Verify Email')

    <!-- Info Message -->
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl border border-blue-100 dark:border-blue-800">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fas fa-envelope-open-text text-blue-500 text-lg"></i>
            </div>
            <p class="text-sm text-blue-700 dark:text-blue-300">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 rounded-xl border border-green-100 dark:border-green-800">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                </div>
                <p class="text-sm text-green-700 dark:text-green-300 font-medium">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </p>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        <!-- Resend Verification Button -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-gradient w-full py-3 px-4 text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-redo"></i>
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
