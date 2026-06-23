<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #trip-map {
            height: 480px;
            border-radius: 1.5rem;
            border: 1px solid rgb(241 245 249);
        }

        .tab-btn.active {
            @apply text-indigo-700 border-indigo-600;
        }

        .tab-btn.active .tab-indicator {
            @apply scale-x-100;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
            animation: fadeSlideUp 0.3s ease-out;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .receipt-thumb {
            @apply w-16 h-16 object-cover rounded-xl border border-slate-100 cursor-pointer hover:scale-105 transition-transform shadow-sm;
        }
    </style>
    @endpush

    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('trips.index') }}" class="text-slate-400 hover:text-slate-600 transition text-sm">&larr; Kembali</a>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="font-extrabold text-xl text-slate-800 leading-tight">{{ $trip->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border
                            @if($trip->status->value === 'planning') bg-slate-50 border-slate-200 text-slate-500
                            @elseif($trip->status->value === 'active') bg-blue-50 border-blue-200 text-blue-600
                            @elseif($trip->status->value === 'completed') bg-emerald-50 border-emerald-200 text-emerald-600
                            @else bg-rose-50 border-rose-200 text-rose-600
                            @endif">
                            {{ $trip->status->label() }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $trip->origin_name }} &rarr; {{ $trip->destination_name }} &middot; Dibuat {{ $trip->created_at->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($trip->status->value === 'planning')
                <form method="POST" action="{{ route('trips.start', $trip) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-indigo-600/10">
                        Mulai Trip
                    </button>
                </form>
                @elseif($trip->status->value === 'active')
                <form method="POST" action="{{ route('trips.complete', $trip) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-emerald-600/10">
                        Selesaikan Trip
                    </button>
                </form>
                @endif

                @if(in_array($trip->status->value, ['planning', 'active']))
                <a href="{{ route('trips.edit', $trip) }}" class="px-3 py-2 bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-xl text-xs font-semibold transition">Edit</a>
                <form method="POST" action="{{ route('trips.cancel', $trip) }}" onsubmit="return confirm('Batalkan perjalanan?')">
                    @csrf
                    <button type="submit" class="px-3 py-2 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-semibold transition">Batalkan</button>
                </form>
                @endif

                <a href="{{ route('trips.export-pdf', $trip) }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition flex items-center gap-1.5" title="Export PDF">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </a>

                <form method="POST" action="{{ route('trips.save-template', $trip) }}">
                    @csrf
                    <button type="submit" class="px-3 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition" title="Simpan Template">Template</button>
                </form>

                <form method="POST" action="{{ route('trips.destroy', $trip) }}" onsubmit="return confirm('Hapus permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 hover:bg-rose-50 text-rose-400 rounded-xl border border-transparent hover:border-rose-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="tripShow()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Quick Stats Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="stat-card animate-fade-in stagger-1">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <span class="text-[10px] text-slate-400">Jarak & Waktu</span>
                    </div>
                    <span class="text-lg font-extrabold text-slate-800">{{ $trip->distance_km }} <span class="text-xs font-medium text-slate-400">km</span></span>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $trip->formatted_duration }}</p>
                </div>

                <div class="stat-card animate-fade-in stagger-2">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] text-slate-400">Budget</span>
                    </div>
                    @php
                    $budgetPercent = $trip->budget_amount > 0 ? min(100, ($trip->total_expenses / $trip->budget_amount) * 100) : 0;
                    $overBudget = $trip->total_expenses > $trip->budget_amount;
                    @endphp
                    <span class="text-lg font-extrabold {{ $overBudget ? 'text-rose-600' : 'text-slate-800' }}">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</span>
                    <div class="mt-1">
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $overBudget ? 'bg-rose-500' : ($budgetPercent >= 80 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $budgetPercent) }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-0.5 block">dari Rp {{ number_format($trip->budget_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="stat-card animate-fade-in stagger-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-[10px] text-slate-400">Estimasi Bensin</span>
                    </div>
                    <span class="text-lg font-extrabold text-slate-800">Rp {{ number_format($trip->estimated_fuel_cost, 0, ',', '.') }}</span>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $trip->vehicle?->name ?? '-' }}</p>
                </div>

                <div class="stat-card animate-fade-in stagger-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 {{ $prediction['is_sufficient'] ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-lg flex items-center justify-center">
                            @if($prediction['is_sufficient'])
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            @else
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400">Kecukupan</span>
                    </div>
                    <span class="text-lg font-extrabold {{ $prediction['is_sufficient'] ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $prediction['is_sufficient'] ? 'Cukup' : 'Kurang' }}
                    </span>
                    <p class="text-[10px] text-slate-400 mt-0.5">Sisa: Rp {{ number_format($prediction['remaining_budget'], 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Traffic & Weather Row -->
            @if($trafficStatus || $weather)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @if($trafficStatus && $trafficStatus['eta'])
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4 animate-slide-up">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">ETA & Lalu Lintas</span>
                        <span class="text-xl font-extrabold text-slate-800">{{ $trafficStatus['eta'] }}</span>
                        <div class="flex items-center gap-2 mt-1">
                            @php
                            $tl = $trafficStatus['traffic_level'];
                            $tc = ['light'=>'bg-emerald-500','moderate'=>'bg-amber-500','heavy'=>'bg-rose-500'][$tl] ?? 'bg-slate-400';
                            $tlabel = ['light'=>'Lancar','moderate'=>'Sedang','heavy'=>'Macet'][$tl] ?? 'N/A';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold {{ $tc }}/10 text-slate-700 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full {{ $tc }}"></span> {{ $tlabel }}
                            </span>
                            <span class="text-[10px] text-slate-400">{{ $trafficStatus['remaining_minutes'] }} mnt tersisa</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($weather)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4 animate-slide-up">
                    <div class="w-12 h-12 bg-sky-50 rounded-xl flex items-center justify-center shrink-0 text-2xl">{{ $weather['current']['icon'] }}</div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Cuaca di {{ $weather['location'] ?? 'Tujuan' }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold text-slate-800">{{ $weather['current']['temp'] }}&deg;C</span>
                            <span class="text-xs text-slate-500 capitalize">{{ $weather['current']['description'] }}</span>
                        </div>
                        @if(!empty($weather['tips']))
                        <p class="text-[10px] text-amber-600 mt-1">{{ is_array($weather['tips']) ? implode('. ', $weather['tips']) : $weather['tips'] }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Daily Budget Limit -->
            @if($trip->daily_budget_limit)
            @php
            $todaySpent = $trip->expenses->filter(fn($e) => $e->spent_at->isToday())->sum('amount');
            $dailyPercent = $trip->daily_budget_limit > 0 ? min(100, ($todaySpent / $trip->daily_budget_limit) * 100) : 0;
            $dailyOver = $todaySpent > $trip->daily_budget_limit;
            @endphp
            <div class="bg-white rounded-2xl border {{ $dailyOver ? 'border-rose-200' : 'border-slate-100' }} shadow-sm p-5 mb-6 animate-slide-up">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 {{ $dailyOver ? 'text-rose-500' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-sm font-bold text-slate-800">Budget Harian</span>
                        @if($dailyOver)
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-50 text-rose-600 rounded-full">Melebihi!</span>
                        @endif
                    </div>
                    <span class="text-xs text-slate-500">Rp {{ number_format($todaySpent, 0, ',', '.') }} / Rp {{ number_format($trip->daily_budget_limit, 0, ',', '.') }}</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all {{ $dailyOver ? 'bg-rose-500' : 'bg-indigo-500' }}" style="width: {{ $dailyPercent }}%"></div>
                </div>
            </div>
            @endif

            <!-- Tabs Navigation -->
            <div class="flex gap-1 border-b border-slate-100 mb-6 overflow-x-auto no-scrollbar">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-indigo-700 border-indigo-600' : 'text-slate-400 border-transparent hover:text-slate-600'" class="tab-btn relative px-5 py-3 text-sm font-semibold whitespace-nowrap transition border-b-2">
                    Ringkasan
                </button>
                <button @click="activeTab = 'expenses'" :class="activeTab === 'expenses' ? 'text-indigo-700 border-indigo-600' : 'text-slate-400 border-transparent hover:text-slate-600'" class="tab-btn relative px-5 py-3 text-sm font-semibold whitespace-nowrap transition border-b-2">
                    Pengeluaran <span class="ml-1 px-1.5 py-0.5 text-[10px] bg-slate-100 text-slate-500 rounded-full">{{ $trip->expenses->count() }}</span>
                </button>
                <button @click="activeTab = 'route'" :class="activeTab === 'route' ? 'text-indigo-700 border-indigo-600' : 'text-slate-400 border-transparent hover:text-slate-600'" class="tab-btn relative px-5 py-3 text-sm font-semibold whitespace-nowrap transition border-b-2">
                    Rute & Peta
                </button>
                <button @click="activeTab = 'sharing'" :class="activeTab === 'sharing' ? 'text-indigo-700 border-indigo-600' : 'text-slate-400 border-transparent hover:text-slate-600'" class="tab-btn relative px-5 py-3 text-sm font-semibold whitespace-nowrap transition border-b-2">
                    Berbagi <span class="ml-1 px-1.5 py-0.5 text-[10px] bg-slate-100 text-slate-500 rounded-full">{{ $trip->shares->count() }}</span>
                </button>
            </div>

            <!-- TAB: Overview -->
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left: Budget Predictor -->
                    <div class="lg:col-span-5">
                        <div class="rounded-3xl p-6 shadow-lg border relative overflow-hidden text-white
                            @if($prediction['is_sufficient']) bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 border-emerald-500
                            @else bg-gradient-to-br from-rose-700 via-rose-800 to-red-950 border-rose-600 @endif">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-white/70">Proyeksi Budget</h4>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="text-4xl">{{ $prediction['is_sufficient'] ? '&#10003;' : '&#9888;' }}</span>
                                <div>
                                    <h3 class="text-lg font-black">{{ $prediction['is_sufficient'] ? 'BUDGET CUKUP' : 'BUDGET TIDAK CUKUP' }}</h3>
                                    <p class="text-xs text-white/60">Estimasi sampai tujuan</p>
                                </div>
                            </div>
                            <div class="mt-5 space-y-2.5 bg-black/10 border border-white/10 rounded-2xl p-4 text-sm">
                                <div class="flex justify-between"><span class="text-white/60">Sisa Anggaran:</span><span class="font-bold">Rp {{ number_format($prediction['remaining_budget'], 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-white/60">Est. Sisa Bensin:</span><span class="font-bold">Rp {{ number_format($prediction['estimated_fuel_remaining'], 0, ',', '.') }}</span></div>
                                <div class="flex justify-between border-t border-white/10 pt-2"><span class="text-white/60">Proyeksi Realistis:</span><span class="font-extrabold">Rp {{ number_format($prediction['prediction']['realistic'], 0, ',', '.') }}</span></div>
                            </div>
                            <div class="mt-4 p-3 bg-white/10 border border-white/10 rounded-xl text-xs leading-relaxed">
                                <strong class="block mb-1">Rekomendasi:</strong>{{ $prediction['suggestion'] }}
                            </div>
                        </div>
                    </div>

                    <!-- Right: Waypoints + Category Breakdown -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Category Breakdown -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <h3 class="text-sm font-bold text-slate-800 mb-4">Breakdown Kategori</h3>
                            @if($expensesByCategory->isEmpty())
                            <p class="text-xs text-slate-400 text-center py-6">Belum ada pengeluaran.</p>
                            @else
                            <div class="space-y-3">
                                @foreach($expensesByCategory as $catValue => $catAmount)
                                @php
                                $cat = \App\Enums\ExpenseCategory::from($catValue);
                                $catPercent = $trip->total_expenses > 0 ? ($catAmount / $trip->total_expenses) * 100 : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-medium text-slate-700">{{ $cat->icon() }} {{ $cat->label() }}</span>
                                        <span class="font-bold text-slate-800">Rp {{ number_format($catAmount, 0, ',', '.') }} <span class="text-slate-400 font-normal">({{ round($catPercent) }}%)</span></span>
                                    </div>
                                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full bg-indigo-500 transition-all" style="width: {{ $catPercent }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <!-- Waypoints -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-800">Waypoints</h3>
                                @if(in_array($trip->status->value, ['planning', 'active']))
                                <button @click="showWaypointForm = !showWaypointForm" class="text-xs text-indigo-600 font-bold hover:underline">+ Tambah</button>
                                @endif
                            </div>

                            @if($trip->waypoints->isEmpty())
                            <p class="text-xs text-slate-400 text-center py-4">Belum ada waypoint.</p>
                            @else
                            <div class="space-y-2">
                                @foreach($trip->waypoints->sortBy('order_index') as $wp)
                                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                    <span class="w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-[10px] font-bold">{{ $wp->order_index + 1 }}</span>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-semibold text-slate-800 truncate block">{{ $wp->name }}</span>
                                        @if($wp->stay_duration_minutes)<span class="text-[10px] text-slate-400">{{ $wp->stay_duration_minutes }} menit</span>@endif
                                    </div>
                                    @if(in_array($trip->status->value, ['planning', 'active']))
                                    <form method="POST" action="{{ route('trips.waypoints.destroy', [$trip, $wp->id]) }}">
                                        @csrf @method('DELETE')
                                        <button class="p-1 text-rose-400 hover:text-rose-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg></button>
                                    </form>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Waypoint Add Form -->
                            <div x-show="showWaypointForm" x-cloak class="mt-4 p-4 bg-slate-50 rounded-xl space-y-3">
                                <form method="POST" action="{{ route('trips.waypoints.store', $trip) }}" class="space-y-3">
                                    @csrf
                                    <input type="text" name="name" placeholder="Nama waypoint" required class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" step="any" name="latitude" placeholder="Latitude" required class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <input type="number" step="any" name="longitude" placeholder="Longitude" required class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <input type="number" name="stay_duration_minutes" placeholder="Durasi (menit)" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="submit" class="w-full py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: Expenses -->
            <div x-show="activeTab === 'expenses'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Expense List -->
                    <div class="lg:col-span-8">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-sm font-bold text-slate-800">Semua Pengeluaran</h3>
                                <!-- Tag Filter -->
                                <div class="flex gap-1.5 flex-wrap">
                                    <button @click="filterTag = ''" :class="filterTag === '' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-50 text-slate-500'" class="px-2.5 py-1 text-[10px] font-bold rounded-full transition">Semua</button>
                                    @foreach($userTags as $tag)
                                    <button @click="filterTag = '{{ $tag->id }}'" :class="filterTag === '{{ $tag->id }}' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-50 text-slate-500'" class="px-2.5 py-1 text-[10px] font-bold rounded-full transition" style="border-left: 3px solid {{ $tag->color }}">{{ $tag->name }}</button>
                                    @endforeach
                                </div>
                            </div>

                            @if($trip->expenses->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-sm text-slate-400">Belum ada pengeluaran.</p>
                            </div>
                            @else
                            <div class="space-y-3">
                                @foreach($trip->expenses->sortByDesc('spent_at') as $expense)
                                <div class="flex items-center justify-between p-4 rounded-2xl border border-slate-50 hover:border-slate-100 transition gap-4 expense-item" data-tags="{{ $expense->tags->pluck('id')->implode(',') }}">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl p-2 bg-slate-50 rounded-xl">{{ $expense->category->icon() }}</span>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-sm text-slate-800">{{ $expense->category->label() }}</span>
                                                @if($expense->location_name)
                                                <span class="text-[10px] px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full truncate max-w-[120px]">{{ $expense->location_name }}</span>
                                                @endif
                                                @foreach($expense->tags as $tag)
                                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold" style="background: {{ $tag->color }}20; color: {{ $tag->color }}">{{ $tag->name }}</span>
                                                @endforeach
                                                @if($expense->is_recurring)
                                                <span class="text-[10px] px-2 py-0.5 bg-violet-50 text-violet-600 rounded-full font-bold">Ulangan</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $expense->note ?: '-' }} &middot; {{ $expense->spent_at->translatedFormat('d M, H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($expense->receipt_path)
                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="w-10 h-10 rounded-lg overflow-hidden border border-slate-100 hover:ring-2 hover:ring-indigo-300 transition">
                                            <img src="{{ asset('storage/' . $expense->receipt_path) }}" class="w-full h-full object-cover" alt="Receipt">
                                        </a>
                                        @endif
                                        <span class="font-extrabold text-sm text-slate-800 whitespace-nowrap">Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                                        <form method="POST" action="{{ route('expenses.destroy', [$trip, $expense]) }}" onsubmit="return confirm('Hapus?')">
                                            @csrf @method('DELETE')
                                            <button class="p-1.5 hover:bg-rose-50 text-rose-400 rounded-lg transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg></button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Add Expense Form -->
                    <div class="lg:col-span-4">
                        @if(in_array($trip->status->value, ['planning', 'active']))
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4 sticky top-20">
                            <h3 class="text-sm font-bold text-slate-800">Catat Pengeluaran</h3>
                            <form method="POST" action="{{ route('expenses.store', $trip) }}" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Jumlah (Rp)</label>
                                    <input type="number" name="amount" required placeholder="50000" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Kategori</label>
                                    <select name="category" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach(App\Enums\ExpenseCategory::cases() as $cat)
                                        <option value="{{ $cat->value }}">{{ $cat->icon() }} {{ $cat->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Catatan</label>
                                    <input type="text" name="note" placeholder="Keterangan..." class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Nama Lokasi</label>
                                    <input type="text" name="location_name" placeholder="SPBU, Restoran, dll" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <!-- Tags Multi-select -->
                                @if($userTags->isNotEmpty())
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Tags</label>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($userTags as $tag)
                                        <label class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 rounded-full cursor-pointer hover:bg-slate-100 transition text-[10px]">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3 h-3">
                                            <span class="w-2 h-2 rounded-full" style="background:{{ $tag->color }}"></span>
                                            {{ $tag->name }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Receipt Upload -->
                                <div>
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Foto Struk</label>
                                    <input type="file" name="receipt" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>

                                <!-- Recurring Toggle -->
                                <div x-data="{ isRecurring: false }">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" x-model="isRecurring" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                        <span class="text-xs text-slate-600 font-medium">Pengeluaran Berulang</span>
                                    </label>
                                    <div x-show="isRecurring" x-cloak class="mt-2">
                                        <select name="recurring_interval" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Pilih Interval</option>
                                            <option value="daily">Harian</option>
                                            <option value="weekly">Mingguan</option>
                                            <option value="monthly">Bulanan</option>
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="latitude" x-model="expenseLat">
                                <input type="hidden" name="longitude" x-model="expenseLng">

                                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/10">Simpan</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB: Route & Map -->
            <div x-show="activeTab === 'route'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
                    <div id="trip-map"></div>
                    <div class="mt-3 flex flex-wrap gap-2 text-[10px]">
                        <span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded-full font-semibold">Asal: {{ $trip->origin_name }}</span>
                        <span class="px-2 py-1 bg-rose-50 text-rose-700 rounded-full font-semibold">Tujuan: {{ $trip->destination_name }}</span>
                        <span class="px-2 py-1 bg-slate-50 text-slate-600 rounded-full">{{ $trip->distance_km }} km &middot; {{ $trip->formatted_duration }}</span>
                        @if($trip->is_round_trip)
                        <span class="px-2 py-1 bg-violet-50 text-violet-700 rounded-full font-semibold">Pulang-Pergi</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB: Sharing -->
            <div x-show="activeTab === 'sharing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Share Form -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-4">Bagikan Perjalanan</h3>
                        <form method="POST" action="{{ route('trips.share', $trip) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="text-[10px] font-semibold text-slate-500 block mb-1">Email Pengguna</label>
                                <input type="email" name="email" required placeholder="email@contoh.com" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="text-[10px] font-semibold text-slate-500 block mb-1">Izin Akses</label>
                                <select name="permission" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="view">Lihat Saja</option>
                                    <option value="edit">Lihat & Edit</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">Bagikan</button>
                        </form>
                    </div>

                    <!-- Shared Users List -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-4">Dibagikan Kepada</h3>
                        @if($trip->shares->isEmpty())
                        <p class="text-xs text-slate-400 text-center py-8">Belum dibagikan kepada siapapun.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($trip->shares as $share)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-violet-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold">
                                        {{ strtoupper(substr($share->sharedWithUser->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-slate-800 block">{{ $share->sharedWithUser->name ?? 'Unknown' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $share->permission->value === 'edit' ? 'Lihat & Edit' : 'Lihat Saja' }}</span>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('trips.share.revoke', [$trip, $share]) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] text-rose-500 font-bold hover:underline">Cabut</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        function tripShow() {
            return {
                activeTab: 'overview',
                showWaypointForm: false,
                filterTag: '',
                expenseLat: '',
                expenseLng: '',
                map: null,

                init() {
                    this.$watch('filterTag', (val) => this.filterExpenses(val));
                    this.$watch('activeTab', (val) => {
                        if (val === 'route' && !this.map) {
                            this.$nextTick(() => this.initMap());
                        }
                    });
                },

                filterExpenses(tagId) {
                    document.querySelectorAll('.expense-item').forEach(el => {
                        if (!tagId) {
                            el.style.display = '';
                            return;
                        }
                        const tags = el.dataset.tags.split(',').map(Number);
                        el.style.display = tags.includes(Number(tagId)) ? '' : 'none';
                    });
                },

                initMap() {
                    const origin = [{
                        {
                            $trip - > origin_lat
                        }
                    }, {
                        {
                            $trip - > origin_lng
                        }
                    }];
                    const dest = [{
                        {
                            $trip - > destination_lat
                        }
                    }, {
                        {
                            $trip - > destination_lng
                        }
                    }];
                    this.map = L.map('trip-map').setView(origin, 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);

                    // Origin
                    L.marker(origin, {
                            icon: L.icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                                shadowSize: [41, 41]
                            })
                        })
                        .addTo(this.map).bindPopup('<b>Asal</b><br>{{ $trip->origin_name }}');

                    // Destination
                    L.marker(dest, {
                            icon: L.icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                                shadowSize: [41, 41]
                            })
                        })
                        .addTo(this.map).bindPopup('<b>Tujuan</b><br>{{ $trip->destination_name }}');

                    // Waypoints
                    @foreach($trip - > waypoints - > sortBy('order_index') as $wp)
                    L.marker([{
                            {
                                $wp - > latitude
                            }
                        }, {
                            {
                                $wp - > longitude
                            }
                        }], {
                            icon: L.icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                                shadowSize: [41, 41]
                            })
                        })
                        .addTo(this.map).bindPopup('<b>{{ $wp->name }}</b>');
                    @endforeach

                    // Route
                    @if($trip - > route_geometry)
                    const geom = {
                        !!$trip - > route_geometry!!
                    };
                    const latLngs = geom.coordinates.map(c => [c[1], c[0]]);
                    const route = L.polyline(latLngs, {
                        color: '#4F46E5',
                        weight: 6,
                        opacity: 0.8
                    }).addTo(this.map);
                    this.map.fitBounds(route.getBounds(), {
                        padding: [50, 50]
                    });
                    @endif

                    // Expense markers
                    @foreach($trip - > expenses as $expense)
                    @if($expense - > latitude && $expense - > longitude)
                    L.marker([{
                            {
                                $expense - > latitude
                            }
                        }, {
                            {
                                $expense - > longitude
                            }
                        }], {
                            icon: L.icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
                                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                                shadowSize: [41, 41]
                            })
                        })
                        .addTo(this.map).bindPopup('<b>{{ $expense->category->icon() }} {{ $expense->category->label() }}</b><br>Rp {{ number_format($expense->amount, 0, ",", ".") }}');
                    @endif
                    @endforeach
                }
            };
        }
    </script>
    @endpush
</x-app-layout>