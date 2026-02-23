<header>
    <!-- Mobile Header -->
    <div class="md:hidden bg-gradient-to-r from-slate-800 to-slate-700 text-white px-4 h-16 flex justify-between items-center fixed top-0 w-full z-50 shadow-md">
        <div class="flex items-center space-x-3">
            <button onclick="toggleSidebar()" aria-label="Open menu" class="p-2 rounded-md hover:bg-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <div class="font-bold">Apex Capital</div>
                {{-- <div class="text-xs text-slate-300">Invoice System</div> --}}
            </div>
        </div>
        <div class="flex items-center">
            <button class="p-2 rounded-full hover:bg-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </button>
        </div>
    </div>

    <!-- Desktop Header -->
    <div class="hidden md:flex items-center justify-between bg-white border-b border-gray-200 px-6 py-4 sticky top-0 z-20">
        <div class="flex items-center space-x-4">
            <h1 class="text-lg font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
            <nav class="hidden lg:flex space-x-3 text-sm text-slate-600">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-800">Overview</a>
                <a href="{{ route('clients.index') }}" class="hover:text-slate-800">Clients</a>
                <a href="{{ route('kyc.records') }}" class="hover:text-slate-800">KYC</a>
            </nav>
        </div>

        <div class="flex items-center space-x-4">
            <form class="hidden sm:block">
                <input aria-label="Search" class="border rounded px-3 py-2 text-sm" placeholder="Search invoices, clients..." />
            </form>
            <div class="flex items-center space-x-3">
                <button class="p-2 rounded-md hover:bg-gray-100">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>
                <div class="text-sm text-slate-700">Hi, {{ auth()->user()->name ?? 'User' }}</div>
            </div>
        </div>
    </div>
</header>
