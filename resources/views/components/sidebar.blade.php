<aside id="sidebar" class="sidebar w-64 bg-slate-800 text-white flex-col fixed md:relative h-screen z-40 hidden md:flex transition-transform duration-200 ease-in-out">
    <div class="md:hidden flex items-center justify-between px-4 py-3 border-b border-slate-700">
        <div class="flex items-center space-x-3">
            <div>
                <h2 class="text-lg font-bold">Apex Capital</h2>
                {{-- <div class="text-xs text-slate-400">Invoice System</div> --}}
            </div>
        </div>
        <button onclick="toggleSidebar()" aria-label="Close menu" class="p-2 rounded-md hover:bg-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="hidden md:block p-6 border-b border-slate-700">
        <h2 class="text-xl font-bold">Apex Capital</h2>
        {{-- <p class="text-sm text-slate-400 mt-1">Invoice System</p> --}}
    </div>
    <nav class="flex-1 mt-6">
        <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 text-slate-300 hover:bg-slate-700 {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('clients.index') }}" class="flex items-center px-6 py-3 text-slate-300 hover:bg-slate-700 {{ request()->routeIs('clients.*') ? 'bg-slate-700 text-white' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Clients
        </a>
        <a href="{{ route('kyc.records') }}" class="flex items-center px-6 py-3 text-slate-300 hover:bg-slate-700 {{ request()->routeIs('kyc.*') ? 'bg-slate-700 text-white' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            KYC
        </a>
    </nav>
    <div class="border-t border-slate-700">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center px-6 py-3 text-slate-300 hover:bg-slate-700">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Logout
        </a>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
    </div>

    <!-- Mobile overlay for when sidebar is open -->
    <div id="mobileOverlay" class="mobile-overlay" onclick="toggleSidebar()"></div>
</aside>
