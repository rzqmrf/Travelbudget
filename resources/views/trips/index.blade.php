<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-xl text-slate-900 dark:text-white tracking-tight">Perjalanan</h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Semua rencana & riwayat perjalanan Anda</p>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6 animate-fade-in">

        {{-- ─── HEADER ROW: Filter + CTA ─────────────────── --}}
        <div class="flex items-center gap-3">
            {{-- Filter bar --}}
            <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-white/[0.05] rounded-2xl overflow-x-auto no-scrollbar flex-1">
                @php
                $filters = [
                    ''          => ['label'=>'Semua',   'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                    'planning'  => ['label'=>'Rencana',  'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    'active'    => ['label'=>'Aktif',    'icon'=>'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'completed' => ['label'=>'Selesai',  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'cancelled' => ['label'=>'Batal',    'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                @endphp
                @foreach($filters as $val => $f)
                @php $isF = ($status === $val) || ($status === null && $val === ''); @endphp
                <a href="{{ $val ? route('trips.index', ['status'=>$val]) : route('trips.index') }}"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold rounded-xl transition-all duration-200 whitespace-nowrap shrink-0
                           {{ $isF
                              ? 'bg-green-600 text-white shadow-sm'
                              : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}" />
                    </svg>
                    {{ $f['label'] }}
                </a>
                @endforeach
            </div>

            <a href="{{ route('trips.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-[11px] font-black rounded-xl text-white shrink-0 transition-all active:scale-[0.97]"
                style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 12px rgba(22,163,74,0.3);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Buat Trip
            </a>
        </div>

        {{-- ─── STAT SUMMARY 2×2 ──────────────────────── --}}
        @php
        $allTrips = auth()->user()->trips;
        $totalCount     = $allTrips->count();
        $planningCount  = $allTrips->where('status', \App\Enums\TripStatus::Planning)->count();
        $activeCount    = $allTrips->where('status', \App\Enums\TripStatus::Active)->count();
        $completedCount = $allTrips->where('status', \App\Enums\TripStatus::Completed)->count();
        @endphp
        <div class="grid grid-cols-2 gap-3">

            {{-- Total --}}
            <div class="card p-4 flex flex-col gap-2 relative overflow-hidden">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-green-100 dark:bg-green-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Total Perjalanan</span>
                </div>
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $totalCount }}</p>
                <p class="text-[10px] font-semibold text-green-600 dark:text-green-400">Semua waktu</p>
                {{-- Mini sparkline decoration --}}
                <svg class="absolute right-3 bottom-3 w-14 h-8 opacity-20" viewBox="0 0 56 32" fill="none">
                    <polyline points="0,28 8,22 16,24 24,14 32,18 40,8 48,12 56,4" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            {{-- Rencana --}}
            <div class="card p-4 flex flex-col gap-2 relative overflow-hidden">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-blue-100 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Rencana</span>
                </div>
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $planningCount }}</p>
                <p class="text-[10px] font-semibold text-blue-500 dark:text-blue-400">Akan datang</p>
                <div class="absolute right-2 bottom-2 text-3xl opacity-10">📅</div>
            </div>

            {{-- Aktif --}}
            <div class="card p-4 flex flex-col gap-2 relative overflow-hidden">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-orange-100 dark:bg-orange-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Aktif</span>
                </div>
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $activeCount }}</p>
                <p class="text-[10px] font-semibold text-orange-500 dark:text-orange-400">Sedang berjalan</p>
                <div class="absolute right-2 bottom-2 text-3xl opacity-10">🧳</div>
            </div>

            {{-- Selesai --}}
            <div class="card p-4 flex flex-col gap-2 relative overflow-hidden">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-purple-100 dark:bg-purple-500/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Selesai</span>
                </div>
                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $completedCount }}</p>
                <p class="text-[10px] font-semibold text-purple-600 dark:text-purple-400">Perjalanan selesai</p>
                <div class="absolute right-2 bottom-2 text-3xl opacity-10">🏁</div>
            </div>
        </div>

        {{-- ─── TIPS BANNER (dismissible) ──────────────── --}}
        <div x-data="{ show: !localStorage.getItem('tips_dismissed') }" x-show="show"
            class="relative flex items-start gap-3 p-4 rounded-2xl overflow-hidden border border-amber-200/60 dark:border-amber-500/20"
            style="background:linear-gradient(135deg,#fefce8,#fef9c3);"
            x-cloak>
            <div class="dark:hidden absolute inset-0 pointer-events-none" style="background:linear-gradient(135deg,#fefce8,#fef9c3);"></div>
            <div class="dark:block hidden absolute inset-0 pointer-events-none" style="background:rgba(251,191,36,0.07);"></div>
            <div class="w-9 h-9 bg-amber-100 dark:bg-amber-500/15 rounded-xl flex items-center justify-center shrink-0 relative z-10">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m1.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <div class="flex-1 relative z-10">
                <p class="text-sm font-black text-amber-800 dark:text-amber-300">Tips hemat!</p>
                <p class="text-xs text-amber-700/80 dark:text-amber-400/80 mt-0.5 leading-relaxed">
                    Rencanakan perjalananmu dengan baik dan kelola budget dengan cerdas.
                </p>
            </div>
            <div class="text-2xl shrink-0 relative z-10 opacity-60">💰</div>
            <button @click="show=false; localStorage.setItem('tips_dismissed','1')"
                class="absolute top-3 right-3 w-6 h-6 flex items-center justify-center rounded-full bg-amber-100 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 text-xs cursor-pointer hover:bg-amber-200 transition-colors z-10">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ─── TRIP LIST / EMPTY ─────────────────────── --}}
        @if($trips->isEmpty())
        {{-- Empty state --}}
        <div class="card p-8 text-center">
            <img src="{{ asset('img/trips-illustration.jpg') }}"
                alt="Belum ada perjalanan"
                class="w-44 h-44 object-contain mx-auto mb-2"
                onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
            <div style="display:none;font-size:5rem;margin-bottom:0.5rem;">🗺️</div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Belum Ada Perjalanan</h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-2 mb-6 max-w-[260px] mx-auto leading-relaxed">
                Mulai rencanakan budget perjalanan pertammu dan ciptakan momen tak terlupakan!
            </p>
            <a href="{{ route('trips.create') }}"
                class="inline-flex items-center gap-2 px-7 py-3.5 text-sm font-bold rounded-2xl text-white transition-all active:scale-[0.97]"
                style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 16px rgba(22,163,74,0.28);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Buat Trip Pertamamu
            </a>
        </div>

        @else
        {{-- Trip list grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($trips as $trip)
            @php
                $bPct = $trip->budget_amount > 0 ? min(100, ($trip->total_expenses / $trip->budget_amount) * 100) : 0;
                $over = $trip->total_expenses > $trip->budget_amount;
                $bCol = $over ? '#ef4444' : ($bPct >= 80 ? '#f59e0b' : '#22c55e');
                $accentClass = match($trip->status->value) {
                    'active'    => 'trip-active-accent',
                    'planning'  => 'trip-planning-accent',
                    'cancelled' => 'trip-cancelled-accent',
                    default     => 'trip-completed-accent',
                };
                $statusBadge = match($trip->status->value) {
                    'active'    => 'badge-green',
                    'completed' => 'badge-blue',
                    'cancelled' => 'badge-rose',
                    default     => 'badge-slate',
                };
            @endphp
            <a href="{{ route('trips.show', $trip) }}"
                class="card {{ $accentClass }} flex items-center gap-3.5 p-4 hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-all duration-200 group block">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0 bg-slate-50 dark:bg-white/[0.04] border border-slate-100 dark:border-white/[0.05] group-hover:scale-105 transition-transform">
                    {{ $trip->vehicle?->type?->icon() ?? '🚗' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">{{ $trip->name }}</h4>
                        <span class="{{ $statusBadge }}">{{ $trip->status->label() }}</span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ $trip->origin_name }} → {{ $trip->destination_name }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex-1 max-w-[80px] h-1 rounded-full bg-slate-100 dark:bg-white/[0.06] overflow-hidden">
                            <div class="h-full rounded-full" style="width:{{ $bPct }}%;background:{{ $bCol }};"></div>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400">{{ round($bPct) }}%</span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="block text-sm font-black {{ $over ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }}">
                        Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $trip->distance_km ?? 0 }} km</span>
                    <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-green-500 transition-colors mt-1 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
            @endforeach
        </div>

        @if($trips->hasPages())
        <div>{{ $trips->links() }}</div>
        @endif
        @endif

    </div>
</x-app-layout>