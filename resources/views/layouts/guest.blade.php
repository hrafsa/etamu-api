<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-Tamu') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body{font-family:'Figtree',ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,'Apple Color Emoji','Segoe UI Emoji'}
            .glass{background:linear-gradient(180deg,rgba(255,255,255,0.08),rgba(255,255,255,0.05));border:1px solid rgba(255,255,255,0.12)}
        </style>
    </head>
    <body class="min-h-screen text-gray-200" style="background: radial-gradient(1200px 600px at 10% -20%, rgba(99,102,241,0.25), transparent 50%), radial-gradient(1000px 500px at 90% 0%, rgba(20,184,166,0.22), transparent 50%), radial-gradient(800px 400px at 50% 100%, rgba(236,72,153,0.18), transparent 50%), #0b0f1a;">
        <header class="w-full">
            <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
                <a href="/" class="inline-flex items-center gap-2">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500/20 ring-1 ring-indigo-400/30">
                        <svg class="w-5 h-5 text-indigo-300" viewBox="0 0 24 24" fill="none"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.5"/></svg>
                    </span>
                    <span class="text-sm font-semibold tracking-wide">E‑Tamu</span>
                </a>
                <a href="/" class="text-xs text-gray-400 hover:text-gray-200">Kembali ke Beranda</a>
            </div>
        </header>

        <div class="min-h-[calc(100vh-112px)] flex items-center">
            <div class="w-full max-w-md mx-auto px-6">
                <div class="glass rounded-2xl p-6 md:p-8 shadow-2xl bg-gray-900/60">
                    <div class="mb-4">
                        <h1 class="text-lg font-semibold text-white">Masuk Admin</h1>
                        <p class="mt-1 text-xs text-gray-400">Gunakan kredensial admin Anda untuk mengakses panel.</p>
                    </div>
                    {{ $slot }}
                </div>
                <div class="text-center mt-6 text-[11px] text-gray-500">© {{ date('Y') }} E‑Tamu</div>
            </div>
        </div>
    </body>
</html>
