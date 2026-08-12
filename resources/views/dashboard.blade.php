<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-xl text-slate-900 dark:text-white tracking-tight">Dashboard</h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6 animate-fade-in">

        {{-- ═══════════════════════════════════════════════
             GREETING
        ═══════════════════════════════════════════════ --}}
        <div class="animate-fade-in">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">
                Selamat datang kembali, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">
                Kelola perjalanan dan budgetmu dengan mudah.
            </p>
        </div>

        {{-- ═══════════════════════════════════════════════
             HERO CARD — Green gradient + illustration
        ═══════════════════════════════════════════════ --}}
        <div class="hero-card p-5 min-h-[172px] animate-fade-in delay-100">
            {{-- Decorative circles --}}
            <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute -right-2 bottom-0 w-20 h-20 rounded-full bg-white/[0.07] pointer-events-none"></div>
            <div class="absolute right-16 top-3 w-3 h-3 rounded-full bg-white/30 pointer-events-none"></div>
            <div class="absolute right-10 top-8 w-1.5 h-1.5 rounded-full bg-white/20 pointer-events-none"></div>

            {{-- Content + Illustration split --}}
            <div class="flex items-stretch gap-2 relative z-10">
                {{-- Left: Text content --}}
                <div class="flex-1">
                    <span class="inline-flex items-center px-3 py-1 text-[10px] font-bold text-white/90 bg-white/20 rounded-full mb-3 tracking-wide">
                        Mulai Perjalananmu
                    </span>
                    <h2 class="text-[22px] font-black text-white leading-tight mb-2">
                        Rencanakan Trip<br>Impianmu ✈️
                    </h2>
                    <p class="text-[13px] text-white/75 leading-relaxed mb-5 max-w-[200px]">
                        Buat perjalanan baru, atur rute, hitung budget otomatis.
                    </p>
                    <a href="{{ route('trips.create') }}"
                        class="inline-flex items-center gap-2 bg-white text-green-700 font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm hover:bg-green-50 transition-colors active:scale-[0.97]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Trip Baru
                    </a>
                </div>

                {{-- Right: Illustration --}}
                <div class="w-[130px] relative flex-shrink-0 flex items-end justify-center pb-1">
                    <img src="{{ asset('img/hero-travel.jpg') }}"
                        alt="Travel illustration"
                        class="w-full h-auto object-contain max-h-[150px] drop-shadow-lg animate-float"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    {{-- Fallback emoji illustration --}}
                    <div style="display:none;" class="w-full h-[140px] flex flex-col items-center justify-end relative">
                        <div class="text-[60px] leading-none animate-float">🧳</div>
                        <div class="absolute top-0 right-2 text-2xl rotate-12">✈️</div>
                        <div class="absolute top-6 left-1 text-xl">🗺️</div>
                        <div class="absolute bottom-10 right-0 text-lg">🎩</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             RINGKASAN — 2×2 stat grid
        ═══════════════════════════════════════════════ --}}
        <div class="animate-fade-in delay-200">
            <div class="section-header">
                <h2 class="section-title">Ringkasan</h2>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-500/10 px-3 py-1.5 rounded-full border border-green-200/50 dark:border-green-500/20">
                    Bulan Ini
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3">

                {{-- Trip Selesai --}}
                <div class="stat-card">
                    <div class="flex items-start justify-between mb-4">
                        <div class="icon-badge-blue">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-extrabold px-2.5 py-1 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-full tracking-widest border border-blue-100 dark:border-blue-500/20">TRIP</span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Trip Selesai</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-white mt-1 tracking-tight">{{ $totalTrips }}</p>
                    <a href="{{ route('trips.index', ['status'=>'completed']) }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400 mt-3">
                        Lihat detail
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Pengeluaran Bulan Ini --}}
                <div class="stat-card">
                    <div class="flex items-start justify-between mb-4">
                        <div class="icon-badge-teal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-extrabold px-2.5 py-1 bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 rounded-full tracking-widest border border-teal-100 dark:border-teal-500/20">BULAN INI</span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Pengeluaran Bulan Ini</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-1 tracking-tight leading-tight">
                        Rp {{ number_format($monthlySpent, 0, ',', '.') }}
                    </p>
                    <a href="{{ route('statistics') }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 dark:text-teal-400 mt-3">
                        Lihat detail
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Total Keseluruhan --}}
                <div class="stat-card">
                    <div class="flex items-start justify-between mb-4">
                        <div class="icon-badge-amber">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-extrabold px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-full tracking-widest border border-amber-100 dark:border-amber-500/20">TOTAL</span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Total Keseluruhan</p>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-1 tracking-tight leading-tight">
                        Rp {{ number_format($totalSpent, 0, ',', '.') }}
                    </p>
                    <a href="{{ route('statistics') }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-400 mt-3">
                        Lihat detail
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Trip Dibagikan --}}
                <div class="stat-card">
                    <div class="flex items-start justify-between mb-4">
                        <div class="icon-badge-purple">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-extrabold px-2.5 py-1 bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-full tracking-widest border border-purple-100 dark:border-purple-500/20">GRUP</span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Trip Dibagikan</p>
                    <p class="text-3xl font-black text-slate-900 dark:text-white mt-1 tracking-tight">{{ $sharedTripsCount ?? 0 }}</p>
                    <a href="{{ route('trips.index') }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 dark:text-purple-400 mt-3">
                        Lihat detail
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
             ACTIVE TRIP (if any)
        ═══════════════════════════════════════════════ --}}
        @if($activeTrip)
        @php
            $bPct = $activeTrip->budget_amount > 0 ? min(100, ($activeTrip->total_expenses / $activeTrip->budget_amount) * 100) : 0;
            $overBudget = $activeTrip->total_expenses > $activeTrip->budget_amount;
            $bColor = $overBudget ? '#ef4444' : ($bPct >= 80 ? '#f59e0b' : '#22c55e');
            $circum = 2 * M_PI * 28; // r=28
            $offset = $circum * (1 - $bPct / 100);
        @endphp
        <div class="card p-5 animate-fade-in delay-200">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 rounded-full border border-green-200/60 dark:border-green-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Perjalanan Aktif
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white truncate">{{ $activeTrip->name }}</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ $activeTrip->origin_name }} → {{ $activeTrip->destination_name }}
                    </p>
                </div>

                {{-- SVG Progress Ring --}}
                <div class="relative w-16 h-16 shrink-0">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="28" fill="none" stroke="#f1f5f9" stroke-width="5"/>
                        <circle cx="32" cy="32" r="28" fill="none"
                            stroke="{{ $bColor }}"
                            stroke-width="5"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circum }}"
                            stroke-dashoffset="{{ $offset }}"
                            style="transition:stroke-dashoffset 1s ease;"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xs font-black text-slate-900 dark:text-white">{{ round($bPct) }}%</span>
                        <span class="text-[8px] text-slate-400 font-medium">budget</span>
                    </div>
                </div>
            </div>

            {{-- Budget progress bar --}}
            <div class="mb-3">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Budget terpakai</span>
                    <span class="text-[10px] font-black" style="color:{{ $bColor }}">{{ round($bPct) }}%</span>
                </div>
                <div class="h-2 w-full bg-slate-100 dark:bg-white/[0.06] rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700" style="width:{{ $bPct }}%;background:{{ $bColor }};"></div>
                </div>
                <div class="flex justify-between mt-1.5 text-[10px] text-slate-400 dark:text-slate-500">
                    <span>Rp {{ number_format($activeTrip->total_expenses, 0, ',', '.') }} terpakai</span>
                    <span>Rp {{ number_format($activeTrip->budget_amount, 0, ',', '.') }} budget</span>
                </div>
            </div>

            <a href="{{ route('trips.show', $activeTrip) }}"
                class="flex items-center justify-center gap-2 w-full py-3 text-sm font-bold rounded-xl text-white transition-all active:scale-[0.98]"
                style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 12px rgba(22,163,74,0.25);">
                Lihat Detail Trip
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════
             PERJALANAN TERAKHIR
        ═══════════════════════════════════════════════ --}}
        <div class="animate-fade-in delay-300">
            <div class="section-header">
                <h2 class="section-title">Perjalanan Terakhir</h2>
                @if($recentTrips->isNotEmpty())
                <a href="{{ route('trips.index') }}" class="section-link">
                    Lihat Semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @endif
            </div>

            @if($recentTrips->isEmpty())
            {{-- Empty state --}}
            <div class="card p-8 text-center">
                <div class="text-5xl mb-3">🎒</div>
                <div class="text-3xl -mt-6 ml-8">🪧</div>
                <h3 class="text-base font-black text-slate-900 dark:text-white mt-3">Belum ada perjalanan</h3>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5 mb-5 max-w-[260px] mx-auto leading-relaxed">
                    Yuk mulai perjalanan pertama mu dan ciptakan momen tak terlupakan!
                </p>
                <a href="{{ route('trips.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold rounded-full text-white transition-all active:scale-[0.97]"
                    style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 14px rgba(22,163,74,0.28);">
                    Mulai perjalanan pertama
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @else
            {{-- Recent trips list --}}
            <div class="card overflow-hidden divide-y divide-slate-50 dark:divide-white/[0.04]">
                @foreach($recentTrips as $trip)
                @php
                    $bPct = $trip->budget_amount > 0 ? min(100, ($trip->total_expenses / $trip->budget_amount) * 100) : 0;
                    $over = $trip->total_expenses > $trip->budget_amount;
                    $bCol = $over ? '#ef4444' : ($bPct >= 80 ? '#f59e0b' : '#22c55e');
                @endphp
                <a href="{{ route('trips.show', $trip) }}"
                    class="flex items-center gap-3.5 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors group">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0 bg-slate-50 dark:bg-white/[0.04] border border-slate-100 dark:border-white/[0.05] group-hover:scale-105 transition-transform">
                        {{ $trip->vehicle?->type?->icon() ?? '🚗' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">{{ $trip->name }}</h4>
                        <p class="text-xs text-slate-400 dark:text-slate-500 truncate mt-0.5">{{ $trip->origin_name }} → {{ $trip->destination_name }}</p>
                        <div class="mt-1.5 h-1 w-20 bg-slate-100 dark:bg-white/[0.06] rounded-full overflow-hidden">
                            <div class="h-full rounded-full" style="width:{{ $bPct }}%;background:{{ $bCol }};"></div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="block text-sm font-black text-slate-900 dark:text-white">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</span>
                        <span class="badge-green mt-1">Selesai</span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

    </div>{{-- /max-w-lg --}}
</x-app-layout>