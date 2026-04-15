<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E‑Tamu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Background accents are applied inline on body to maximize compatibility */
        body { font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji','Segoe UI Emoji'; }
        .glass { background: linear-gradient(180deg, rgba(255,255,255,0.08), rgba(255,255,255,0.05)); border: 1px solid rgba(255,255,255,0.12); }
        .fade-up { opacity: 0; transform: translateY(10px); animation: fade-up .6s ease forwards; }
        .fade-up:nth-child(2){ animation-delay:.1s } .fade-up:nth-child(3){ animation-delay:.2s } .fade-up:nth-child(4){ animation-delay:.3s }
        @keyframes fade-up { to { opacity: 1; transform: translateY(0) } }
    </style>
</head>
<body class="min-h-screen bg-gray-950 text-gray-200" style="background: radial-gradient(1200px 600px at 10% -20%, rgba(99,102,241,0.25), transparent 50%), radial-gradient(1000px 500px at 90% 0%, rgba(20,184,166,0.22), transparent 50%), radial-gradient(800px 400px at 50% 100%, rgba(236,72,153,0.18), transparent 50%), #0b0f1a;">
    <header class="w-full">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500/20 ring-1 ring-indigo-400/30">
                    <svg class="w-5 h-5 text-indigo-300" viewBox="0 0 24 24" fill="none"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke="currentColor" stroke-width="1.5"/></svg>
                </span>
                <span class="text-sm font-semibold tracking-wide">E‑Tamu</span>
            </div>
            <nav class="hidden sm:flex items-center gap-6 text-sm">
                <a href="#fitur" class="text-gray-300 hover:text-white">Fitur</a>
                <a href="#tentang" class="text-gray-300 hover:text-white">Tentang</a>
                <a href="#faq" class="text-gray-300 hover:text-white">FAQ</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm bg-indigo-600 hover:bg-indigo-500 text-white">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm glass hover:bg-white/10">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm bg-indigo-600 hover:bg-indigo-500 text-white">Daftar</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main>
        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 pt-14 pb-10">
                <div class="grid lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <h1 class="fade-up text-4xl md:text-5xl font-bold tracking-tight text-white">
                            Buku Tamu Digital Modern untuk Instansi Anda
                        </h1>
                        <p class="fade-up mt-4 text-gray-300 text-base md:text-lg leading-relaxed">
                            E‑Tamu memudahkan pencatatan, verifikasi, dan pelaporan kunjungan.
                            Ramping, aman, dan siap dipakai oleh tim Anda.
                        </p>
                        <div class="fade-up mt-6 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium">Buka Dashboard</a>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md glass text-white text-sm font-medium hover:bg-white/10">Daftar</a>
                                @endif
                            @endauth
                        </div>
                        <div class="fade-up mt-8 flex items-center gap-6 text-xs text-gray-400">
                            <div class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Real‑time</div>
                            <div class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-400"></span> Aman</div>
                            <div class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-pink-400"></span> Mudah Dipakai</div>
                        </div>
                    </div>
                    <div class="fade-up">
                        <div class="relative">
                            <div class="absolute -inset-6 bg-gradient-to-tr from-indigo-600/20 via-emerald-500/10 to-pink-500/10 blur-2xl rounded-3xl"></div>
                            <div class="relative glass rounded-2xl p-4 md:p-6 shadow-2xl">
                                <div class="bg-gray-900/60 border border-white/10 rounded-xl p-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="rounded-lg bg-gray-800 border border-white/10 p-4">
                                            <div class="text-xs uppercase tracking-wide text-gray-400">Stat Hari Ini</div>
                                            <div class="mt-2 text-2xl font-semibold text-white">128</div>
                                            <div class="text-xs text-emerald-400 mt-1">+12% dari kemarin</div>
                                        </div>
                                        <div class="rounded-lg bg-gray-800 border border-white/10 p-4">
                                            <div class="text-xs uppercase tracking-wide text-gray-400">Pending</div>
                                            <div class="mt-2 text-2xl font-semibold text-white">9</div>
                                            <div class="text-xs text-amber-300 mt-1">butuh verifikasi</div>
                                        </div>
                                        <div class="rounded-lg bg-gray-800 border border-white/10 p-4 sm:col-span-2">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-xs uppercase tracking-wide text-gray-400">Kunjungan Minggu Ini</div>
                                                    <div class="mt-2 text-2xl font-semibold text-white">542</div>
                                                </div>
                                                <span class="text-indigo-300 text-xs">contoh pratinjau</span>
                                            </div>
                                            <div class="mt-3 h-16 bg-gradient-to-r from-indigo-500/30 via-emerald-500/30 to-pink-500/30 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-[11px] text-gray-400 text-center">Tampilan ilustratif — fokus pada desain antarmuka.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fitur -->
        <section id="fitur" class="py-12">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="fade-up glass rounded-xl p-5 border border-white/10">
                        <div class="text-sm text-gray-300 font-medium">Kelola Pengajuan</div>
                        <p class="mt-2 text-sm text-gray-400">Tinjau dan ubah status pengajuan dengan cepat.</p>
                    </div>
                    <div class="fade-up glass rounded-xl p-5 border border-white/10">
                        <div class="text-sm text-gray-300 font-medium">Kategori & Subkategori</div>
                        <p class="mt-2 text-sm text-gray-400">Struktur data rapi untuk pelaporan akurat.</p>
                    </div>
                    <div class="fade-up glass rounded-xl p-5 border border-white/10">
                        <div class="text-sm text-gray-300 font-medium">User Management</div>
                        <p class="mt-2 text-sm text-gray-400">Kelola peran dan hak akses tim Anda.</p>
                    </div>
                    <div class="fade-up glass rounded-xl p-5 border border-white/10">
                        <div class="text-sm text-gray-300 font-medium">Log Aktivitas</div>
                        <p class="mt-2 text-sm text-gray-400">Jejak audit transparan untuk setiap aksi.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tentang -->
        <section id="tentang" class="py-6">
            <div class="max-w-3xl mx-auto px-6 text-center">
                <h2 class="fade-up text-xl font-semibold text-white">Tentang E‑Tamu</h2>
                <p class="fade-up mt-3 text-sm text-gray-400">Platform buku tamu digital yang dibangun untuk kemudahan operasional instansi dengan fokus pada kecepatan, keamanan, dan kesederhanaan UI.</p>
            </div>
        </section>

        <!-- FAQ singkat -->
        <section id="faq" class="py-10">
            <div class="max-w-4xl mx-auto px-6 grid gap-4 md:grid-cols-2">
                <div class="fade-up glass rounded-lg p-4 border border-white/10">
                    <div class="text-sm text-white font-medium">Apakah E‑Tamu bisa multi‑user?</div>
                    <p class="mt-1 text-sm text-gray-400">Ya, kelola banyak pengguna dengan peran berbeda.</p>
                </div>
                <div class="fade-up glass rounded-lg p-4 border border-white/10">
                    <div class="text-sm text-white font-medium">Apakah ada log aktivitas?</div>
                    <p class="mt-1 text-sm text-gray-400">Semua aksi penting tercatat untuk audit.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-8">
        <div class="max-w-7xl mx-auto px-6 text-xs text-gray-500 flex items-center justify-between">
            <span>© {{ date('Y') }} E‑Tamu</span>
            <div class="flex items-center gap-4">
                <a href="#" class="hover:text-gray-300">Kebijakan Privasi</a>
                <a href="#" class="hover:text-gray-300">Ketentuan</a>
            </div>
        </div>
    </footer>
</body>
</html>

