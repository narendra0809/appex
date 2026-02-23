<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Invoice System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex h-screen overflow-hidden">
        
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 z-40 lg:hidden"
             style="display: none;">
        </div>

        <!-- Sidebar -->
        <aside class="fixed lg:relative inset-y-0 left-0 z-50 w-64 bg-slate-800 text-white flex flex-col transition-all duration-300 transform"
               :class="[
                   sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                   sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'
               ]">
            
            <!-- Logo -->
            <div class="flex items-center h-20 px-4 border-b border-slate-700" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                <div x-show="!sidebarCollapsed" class="flex items-center gap-3">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo" class="h-12 w-12 object-contain rounded-lg bg-white p-1">
                    <div>
                        <h1 class="text-lg font-bold">Apex Capital</h1>
                        {{-- <p class="text-xs text-slate-400">Invoice System</p> --}}
                    </div>
                </div>
                <div x-show="sidebarCollapsed" class="flex flex-col items-center">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo" class="h-10 w-10 object-contain rounded-lg bg-white p-1">
                    <button @click="sidebarOpen = false" class="lg:hidden mt-2 text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <button x-show="!sidebarCollapsed" @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Dashboard</span>
                </a>
                <a href="{{ route('clients.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('clients.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Clients</span>
                </a>
                <a href="{{ route('kyc.records') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('kyc.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">KYC</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="border-t border-slate-700 p-3">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   class="flex items-center px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors"
                   :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span x-show="!sidebarCollapsed" class="ml-3 whitespace-nowrap">Logout</span>
                </a>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition" title="Toggle Sidebar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                        </button>
                        <div class="flex items-center gap-2 hidden sm:flex">
                            {{-- <img src="{{ asset('logo.jpg') }}" alt="Logo" class="h-8 w-8 object-contain rounded bg-white p-0.5"> --}}
                            <span class="font-semibold text-gray-800 text-lg">Apex Capital</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="hidden sm:block text-sm text-gray-600">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
