<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none !important;}body{overscroll-behavior-y:contain;}main{min-height:1px;}</style>
    </head>
    <body class="font-sans antialiased" x-data="{ mobileNav:false }" @keydown.window.escape="mobileNav=false" :class="{'overflow-hidden': mobileNav}">
    <!-- Root container: allow vertical scroll, hide horizontal overflow only -->
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex overflow-x-hidden">
        <!-- Sidebar (desktop) -->
        @include('layouts.navigation')

        <!-- Off-canvas mobile nav (refactored) -->
        <div x-cloak class="md:hidden">
            <!-- Overlay -->
            <div x-show="mobileNav"
                 x-transition.opacity
                 class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
                 @click="mobileNav=false"
                 aria-hidden="true"></div>
            <!-- Panel -->
            <nav x-show="mobileNav"
                 x-trap.noscroll="mobileNav"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transform transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="-translate-x-full opacity-0"
                 class="fixed inset-y-0 left-0 z-50 w-72 max-w-[80%] bg-gray-900 border-r border-gray-800 shadow-xl flex flex-col"
                 role="dialog" aria-modal="true" aria-label="Mobile navigation">
                <div class="h-14 flex items-center px-4 border-b border-gray-800 justify-between">
                    <span class="text-sm font-semibold text-gray-100">Menu</span>
                    <button @click="mobileNav=false" class="p-2 text-gray-400 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md" aria-label="Close menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto py-3">
                    @include('layouts.navigation-mobile')
                </div>
                <div class="p-3 border-t border-gray-800">
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <button class="w-full inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm bg-red-600/80 hover:bg-red-600 text-white justify-center" type="submit">Logout</button>
                    </form>
                </div>
            </nav>
        </div>

        <!-- Content wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile top bar -->
            <div class="md:hidden flex items-center justify-between bg-gray-900 border-b border-gray-800 h-14 px-4">
                <button @click="mobileNav=true" class="p-2 rounded-md bg-gray-800 text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Open navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="text-sm font-semibold text-gray-200 tracking-wide">{{ config('app.name','Laravel') }}</div>
                <div class="w-5 h-5"></div>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow-sm hidden md:block">
                    <div class="max-w-7xl w-full mx-auto py-4 px-4 sm:px-6 lg:px-8 h-16">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 w-full">
                <div class="w-full mx-auto max-w-7xl md:px-6 lg:px-8 px-4 py-4 md:py-0">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    </body>
</html>
