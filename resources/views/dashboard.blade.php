<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl text-slate-800">Dashboard</h2>
            <p class="text-xs text-slate-400 mt-0.5">Selamat datang kembali, {{ auth()->user()->name }}!</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="stat-card animate-fade-in stagger-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800">{{ $totalTrips }}</span>
                    <p class="text-[11px] text-slate-400 mt-1">Trip Selesai</p>
                </div>

                <div class="stat-card animate-fade-in stagger-2">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800">Rp {{ number_format($monthlySpent, 0, ',', '.') }}</span>
                    <p class="text-[11px] text-slate-400 mt-1">Bulan Ini</p>
                </div>

                <div class="stat-card animate-fade-in stagger-3">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800">Rp {{ number_format($totalSpent, 0, ',', '.') }}</span>
                    <p class="text-[11px] text-slate-400 mt-1">Total Keseluruhan</p>
                </div>

                <div class="stat-card animate-fade-in stagger-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800">{{ $sharedTripsCount ?? 0 }}</span>
                    <p class="text-[11px] text-slate-400 mt-1">Trip Dibagikan</p>
                </div>
            </div>

            <!-- Active Trip Banner -->
            @if($activeTrip)
            <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-800 text-white rounded-3xl p-6 md:p-8 mb-8 shadow-2xl shadow-indigo-600/20 relative overflow-hidden animate-fade-in">
                <!-- Background decoration -->
                <div class="absolute right-0 top-0 opacity-5 translate-x-20 -translate-y-20 select-none pointer-events-none">
                    <svg class="w-[500px] h-[500px]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                    </svg>
                </div>

                <div class="relative z-10">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold bg-white/15 border border-white/10 rounded-full uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Perjalanan Aktif
                            </span>
                            <h4 class="text-2xl font-extrabold mt-2">{{ $activeTrip->name }}</h4>
                            <p class="text-indigo-200/70 text-sm mt-1">{{ $activeTrip->origin_name }} &rarr; {{ $activeTrip->destination_name }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('trips.show', $activeTrip) }}" class="px-5 py-2.5 bg-white text-indigo-700 font-bold rounded-xl text-xs hover:bg-indigo-50 transition shadow-lg">
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                    <!-- Real-time Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Budget Progress Ring -->
                        <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center">
                            @php
                            $percent = $budgetStatus['usage_percent'] ?? 0;
                            $circumference = 2 * M_PI * 36;
                            $offset = $circumference - ($percent / 100) * $circumference;
                            @endphp
                            <div class="relative w-20 h-20 mx-auto mb-2">
                                <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
                                    <circle cx="40" cy="40" r="36" stroke="rgba(255,255,255,0.1)" stroke-width="6" fill="none" />
                                    <circle cx="40" cy="40" r="36" stroke="{{ $percent >= 80 ? '#F87171' : ($percent >= 50 ? '#FBBF24' : '#34D399') }}"
                                        stroke-width="6" fill="none" stroke-linecap="round"
                                        stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $offset }}"
                                        class="transition-all duration-1000" />
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-lg font-extrabold">{{ $percent }}%</span>
                            </div>
                            <span class="text-[10px] text-indigo-200/60 block">Budget Terpakai</span>
                            <span class="text-sm font-bold">Rp {{ number_format($budgetStatus['remaining_budget'], 0, ',', '.') }}</span>
                            <span class="text-[10px] text-indigo-200/50 block">sisa</span>
                        </div>

                        <!-- Traffic ETA -->
                        <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                            <span class="text-[10px] text-indigo-200/60 block mb-2">ETA & Lalu Lintas</span>
                            @if($trafficStatus && $trafficStatus['eta'])
                            <span class="text-2xl font-extrabold">{{ $trafficStatus['eta'] }}</span>
                            <div class="mt-2">
                                @php
                                $trafficColors = ['light' => 'bg-emerald-400', 'moderate' => 'bg-amber-400', 'heavy' => 'bg-rose-400'];
                                $trafficLabels = ['light' => 'Lancar', 'moderate' => 'Sedang', 'heavy' => 'Macet'];
                                $level = $trafficStatus['traffic_level'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 {{ $trafficColors[$level] ?? 'bg-slate-400' }}/20 text-xs rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $trafficColors[$level] ?? 'bg-slate-400' }}"></span>
                                    {{ $trafficLabels[$level] ?? 'N/A' }}
                                </span>
                            </div>
                            <span class="text-[10px] text-indigo-200/50 block mt-1">{{ $trafficStatus['remaining_minutes'] }} menit tersisa</span>
                            @else
                            <span class="text-sm text-indigo-200/50">N/A</span>
                            @endif
                        </div>

                        <!-- Weather -->
                        <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                            <span class="text-[10px] text-indigo-200/60 block mb-2">Cuaca Tujuan</span>
                            @if($weather)
                            <div class="flex items-center gap-2">
                                <span class="text-3xl">{{ $weather['current']['icon'] }}</span>
                                <div>
                                    <span class="text-2xl font-extrabold">{{ $weather['current']['temp'] }}&deg;C</span>
                                    <p class="text-[10px] text-indigo-200/60 capitalize">{{ $weather['current']['description'] }}</p>
                                </div>
                            </div>
                            @else
                            <span class="text-sm text-indigo-200/50">N/A</span>
                            @endif
                        </div>

                        <!-- Budget Status -->
                        <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                            <span class="text-[10px] text-indigo-200/60 block mb-2">Status Kelayakan</span>
                            @if(($budgetStatus['remaining_budget'] ?? 0) >= ($activeTrip->estimated_fuel_cost ?? 0))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-xl text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Cukup
                            </span>
                            <p class="text-[10px] text-indigo-200/50 mt-2">Sampai tujuan</p>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-500/20 text-rose-300 border border-rose-500/30 rounded-xl text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span> Tidak Cukup
                            </span>
                            <p class="text-[10px] text-indigo-200/50 mt-2">Perlu tambahan dana</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-3xl p-8 mb-8 border border-slate-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6 animate-fade-in">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center animate-float">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-extrabold text-slate-800">Mulai Petualangan Baru</h4>
                            <p class="text-slate-400 text-xs mt-0.5 max-w-lg">Buat rencana perjalanan, pilih rute terbaik, hitung bensin otomatis, dan pantau budget secara real-time.</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('trips.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-xl shadow-indigo-600/15">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Trip Baru
                </a>
            </div>
            @endif

            <!-- Recent Trips -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 animate-slide-up">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-extrabold text-slate-800">Perjalanan Terakhir</h3>
                    <a href="{{ route('trips.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 hover:underline transition">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if($recentTrips->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <p class="text-slate-400 text-sm">Belum ada perjalanan yang diselesaikan.</p>
                    <a href="{{ route('trips.create') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-indigo-600 hover:underline">Mulai perjalanan pertama</a>
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentTrips as $trip)
                    @php
                    $budgetPercent = $trip->budget_amount > 0 ? min(100, ($trip->total_expenses / $trip->budget_amount) * 100) : 0;
                    $overBudget = $trip->total_expenses > $trip->budget_amount;
                    @endphp
                    <a href="{{ route('trips.show', $trip) }}" class="trip-card group block">
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">{{ $trip->vehicle?->type?->icon() ?? '🚗' }}</span>
                                    <h4 class="font-bold text-slate-800 group-hover:text-indigo-600 transition truncate">{{ $trip->name }}</h4>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100">Selesai</span>
                            </div>
                            <p class="text-xs text-slate-400 truncate mb-4">{{ $trip->origin_name }} &rarr; {{ $trip->destination_name }}</p>

                            <!-- Mini Budget Bar -->
                            <div class="mb-3">
                                <div class="flex justify-between text-[10px] mb-1">
                                    <span class="text-slate-400">Budget</span>
                                    <span class="{{ $overBudget ? 'text-rose-500 font-bold' : 'text-slate-500' }}">
                                        {{ round($budgetPercent) }}%
                                    </span>
                                </div>
                                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $overBudget ? 'bg-rose-500' : ($budgetPercent >= 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                        style="width: {{ min(100, $budgetPercent) }}%"></div>
                                </div>
                            </div>

                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400">{{ $trip->completed_at?->diffForHumans() ?? '-' }}</span>
                                <span class="font-bold text-slate-700">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>