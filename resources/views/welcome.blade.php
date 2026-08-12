<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#10b981">
    <title>TravelBudget — Minimalist Budget & Trip Planner</title>
    <meta name="description" content="Kelola budget perjalanan, pantau BBM real-time, dan atur anggaran trip Anda secara presisi.">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F8FAF8] dark:bg-[#0c0e0c] text-slate-900 dark:text-slate-100 antialiased selection:bg-emerald-500 selection:text-white relative overflow-x-hidden min-h-screen flex flex-col">

    <!-- Ambient Subtle Background Grid -->
    <div class="fixed inset-0 z-0 opacity-40 dark:opacity-20 pointer-events-none" 
         style="background-image: radial-gradient(#10b981 0.75px, transparent 0.75px); background-size: 24px 24px;"></div>

    <!-- Header / Navbar -->
    <header class="relative z-10 w-full border-b border-slate-200/50 dark:border-white/[0.06] bg-white/70 dark:bg-[#0c0e0c]/70 backdrop-blur-md sticky top-0">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white"
                     style="background:linear-gradient(135deg,#059669,#10b981);box-shadow:0 4px 12px rgba(16,185,129,0.25);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">Travel<span class="text-emerald-600 dark:text-emerald-400">Budget</span></span>
                    <span class="block text-[9px] font-bold text-slate-400 -mt-1 uppercase tracking-widest">Planner</span>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">
                                Mulai Gratis
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="relative z-10 flex-grow flex flex-col justify-center max-w-7xl mx-auto px-6 py-12 md:py-20 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Side: Hero Intro -->
            <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/60 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-full uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Perencana Perjalanan Presisi &amp; Minimalis
                </span>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white tracking-tight leading-[1.1]">
                    Rencanakan Trip Tanpa Overspending <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Secara Real-Time</span>
                </h1>
                
                <p class="text-slate-600 dark:text-slate-400 text-sm md:text-base max-w-lg mx-auto lg:mx-0 leading-relaxed">
                    Estimasi biaya bensin otomatis, navigasi rest-stop pintar, breakdown pengeluaran harian, dan fitur offline PWA untuk pengalaman perjalanan yang efisien dan terkontrol.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto btn-primary px-8 py-3.5 text-center text-sm">
                                Masuk ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto btn-primary px-8 py-3.5 text-center text-sm">
                                Buat Rencana Trip
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto btn-secondary px-6 py-3.5 text-center text-sm">
                                Sudah Punya Akun?
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Feature Highlights -->
                <div class="pt-6 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                    <span class="flex items-center gap-1.5">🗺️ Interactive Route</span>
                    <span class="flex items-center gap-1.5">⛽ Live Fuel Calculator</span>
                    <span class="flex items-center gap-1.5">📱 Mobile PWA Ready</span>
                </div>
            </div>

            <!-- Right Side: Minimalist App Interactive Card Preview -->
            <div class="lg:col-span-6 relative flex justify-center">
                <div class="relative w-full max-w-md card-elevated p-6 md:p-7">
                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-100 dark:border-white/[0.06]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-xl">
                                🚘
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-slate-900 dark:text-white leading-tight">Roadtrip Bali - Jogja</h4>
                                <span class="text-[10px] text-slate-400 font-medium">SUV Diesel • 620 KM</span>
                            </div>
                        </div>
                        <span class="badge-green">Aktif</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/[0.03] border border-slate-100 dark:border-white/[0.05]">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Pengeluaran</span>
                            <span class="text-base font-black text-slate-900 dark:text-white block mt-0.5">Rp 1.250.000</span>
                            <span class="text-[9px] text-slate-400">Limit Rp 3.000.000</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20">
                            <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider block">Estimasi BBM</span>
                            <span class="text-base font-black text-emerald-700 dark:text-emerald-400 block mt-0.5">Rp 680.000</span>
                            <span class="text-[9px] text-emerald-600/80 dark:text-emerald-400/80">Kalkulasi Presisi</span>
                        </div>
                    </div>

                    <div class="hero-card p-4 mb-5">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100">Status Anggaran</span>
                            <span class="text-[10px] font-black bg-white/20 px-2 py-0.5 rounded-full">Sangat Aman</span>
                        </div>
                        <p class="text-xs text-emerald-50 leading-relaxed font-medium">
                            Proyeksi anggaran mencukupi hingga akhir rute dengan sisa cadangan Rp 1.070.000.
                        </p>
                    </div>

                    <!-- Minimal Waypoint Preview -->
                    <div class="space-y-3 pl-3.5 border-l-2 border-dashed border-slate-200 dark:border-white/10 relative text-xs">
                        <div class="relative flex items-center justify-between">
                            <span class="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-[#141714]"></span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">Surabaya (Keberangkatan)</span>
                            <span class="text-[10px] text-slate-400">0 KM</span>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span class="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full bg-blue-500 ring-4 ring-white dark:ring-[#141714]"></span>
                            <span class="font-medium text-slate-600 dark:text-slate-400">Rest Area KM 575 (Waypoint)</span>
                            <span class="text-[10px] text-slate-400">320 KM</span>
                        </div>
                        <div class="relative flex items-center justify-between">
                            <span class="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-[#141714]"></span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">Yogyakarta (Tujuan)</span>
                            <span class="text-[10px] text-slate-400">620 KM</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Features Grid -->
        <section class="mt-20 md:mt-24 grid grid-cols-1 md:grid-cols-3 gap-6 relative">
            <div class="stat-card">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-lg mb-4 text-emerald-600 dark:text-emerald-400">⛽</div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Kalkulator BBM Otomatis</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Perhitungan presisi biaya bahan bakar sesuai jenis kendaraan, tingkat konsumsi KM/L, dan harga BBM terkini.
                    </p>
                </div>
            </div>

            <div class="stat-card">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-lg mb-4 text-blue-600 dark:text-blue-400">🛋️</div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Rest Stops &amp; SPBU Search</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Cari SPBU terdekat, tempat istirahat, rumah makan, dan tempat ibadah secara interaktif langsung dari peta rute.
                    </p>
                </div>
            </div>

            <div class="stat-card">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-lg mb-4 text-purple-600 dark:text-purple-400">📲</div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">PWA &amp; Pencatatan Offline</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Akses aplikasi layaknya aplikasi native di HP Anda. Catat pengeluaran bahkan tanpa koneksi internet dengan sinkronisasi otomatis.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 bg-white dark:bg-[#0c0e0c] border-t border-slate-200/60 dark:border-white/[0.06] py-6 text-center text-xs text-slate-400 dark:text-slate-600 no-print mt-12 shrink-0">
        &copy; {{ date('Y') }} TravelBudget — Minimalist Budget &amp; Trip Planner.
    </footer>
</body>

</html>
