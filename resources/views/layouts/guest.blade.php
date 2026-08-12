<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#10b981">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TravelBudget — Minimalist Budget & Trip Planner</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F8FAF8] dark:bg-[#0c0e0c] text-slate-900 dark:text-slate-100">
    <div class="min-h-screen flex">
        <!-- Left: Illustration Panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 relative overflow-hidden items-center justify-center p-12">
            <!-- Background decorations -->
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-400/5 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10 text-center max-w-lg">
                <!-- Logo -->
                <div class="flex items-center gap-3 justify-center mb-12">
                    <div class="w-12 h-12 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/10 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tight">TravelBudget</span>
                </div>

                <!-- Hero Illustration (SVG) -->
                <div class="mb-10">
                    <svg class="w-80 h-60 mx-auto text-white/20" viewBox="0 0 400 300" fill="none">
                        <rect x="50" y="120" width="300" height="140" rx="20" stroke="currentColor" stroke-width="2" fill="rgba(255,255,255,0.05)" />
                        <circle cx="120" cy="100" r="40" stroke="currentColor" stroke-width="2" fill="rgba(255,255,255,0.05)" />
                        <circle cx="280" cy="80" r="30" stroke="currentColor" stroke-width="2" fill="rgba(255,255,255,0.05)" />
                        <path d="M50 200 Q200 140 350 200" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none" stroke-dasharray="8 4" />
                        <path d="M80 180 L120 160 L200 170 L280 150 L320 170" stroke="rgba(255,255,255,0.5)" stroke-width="3" fill="none" />
                        <circle cx="120" cy="160" r="5" fill="rgba(255,255,255,0.6)" />
                        <circle cx="200" cy="170" r="5" fill="rgba(255,255,255,0.6)" />
                        <circle cx="280" cy="150" r="5" fill="rgba(255,255,255,0.6)" />
                    </svg>
                </div>

                <h2 class="text-3xl font-extrabold text-white mb-4 leading-tight">Rencanakan Perjalanan<br>Impianmu dengan Bijak</h2>
                <p class="text-emerald-100/80 text-sm leading-relaxed">Hitung estimasi bensin, pantau budget real-time, temukan rute terbaik, dan kelola pengeluaran perjalananmu dalam satu platform.</p>

                <!-- Features -->
                <div class="mt-10 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <span class="text-[10px] text-emerald-100/70 font-medium">Multi-Rute</span>
                    </div>
                    <div>
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] text-emerald-100/70 font-medium">Budget Tracker</span>
                    </div>
                    <div>
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <span class="text-[10px] text-emerald-100/70 font-medium">Info Cuaca</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Form Panel -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center gap-2.5 justify-center mb-8">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">Travel<span class="text-emerald-600">Budget</span></span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>