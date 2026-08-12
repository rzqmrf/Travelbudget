<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-xl text-slate-900 dark:text-white tracking-tight">Kendaraan</h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Armada kendaraan untuk kalkulasi BBM</p>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6 animate-fade-in">

        {{-- ─── INFO BANNER "Kelola Kendaraanmu" ─────── --}}
        <a href="{{ route('vehicles.create') }}"
            class="relative overflow-hidden flex items-center gap-4 p-4 rounded-2xl border border-emerald-200/60 dark:border-emerald-500/20 bg-emerald-50/70 dark:bg-emerald-500/10 hover:bg-emerald-100/60 dark:hover:bg-emerald-500/15 transition-all group">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform border border-emerald-200/50 dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 17h.01M12 17h.01M16 17h.01M3 11l1.5-5.25A2 2 0 016.4 4h11.2a2 2 0 011.9 1.75L21 11M3 11h18M3 11v6a1 1 0 001 1h1a2 2 0 002-2v0a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 002 2h1a1 1 0 001-1v-6" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-900 dark:text-white">Kelola Kendaraanmu</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                    Tambah kendaraan untuk menghitung kebutuhan BBM secara otomatis.
                </p>
            </div>
            <svg class="w-5 h-5 text-slate-400 dark:text-slate-500 shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        @if($vehicles->isEmpty())
        {{-- ─── EMPTY STATE ─────────────────────────── --}}
        <div class="card p-8 text-center">
            {{-- Illustration --}}
            <img src="{{ asset('img/vehicle-illustration.jpg') }}"
                alt="Belum ada kendaraan"
                class="w-48 h-48 object-contain mx-auto"
                onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
            <div style="display:none;" class="w-36 h-36 mx-auto bg-orange-50 rounded-full flex items-center justify-center mb-2">
                <span style="font-size:5rem;">🚗</span>
            </div>

            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight mt-2">Belum Ada Kendaraan</h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-2 mb-6 max-w-[240px] mx-auto leading-relaxed">
                Tambahkan kendaraan untuk kalkulasi BBM otomatis di setiap perjalanan.
            </p>

            <a href="{{ route('vehicles.create') }}"
                class="inline-flex items-center gap-2 px-7 py-3.5 text-sm font-bold rounded-2xl text-white transition-all active:scale-[0.97] w-full justify-center"
                style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 16px rgba(22,163,74,0.28);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kendaraan Pertama
            </a>

            {{-- Security badge --}}
            <div class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-full bg-slate-50 dark:bg-white/[0.04] border border-slate-100 dark:border-white/[0.06]">
                <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Data kendaraanmu aman bersama kami</span>
            </div>
        </div>

        @else
        {{-- ─── VEHICLE GRID ─────────────────────────── --}}
        <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $vehicles->count() }} kendaraan terdaftar</p>
            <a href="{{ route('vehicles.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl text-white transition-all active:scale-[0.97]"
                style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 3px 10px rgba(22,163,74,0.25);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($vehicles as $vehicle)
            <div class="card overflow-hidden">
                {{-- Top: identity --}}
                <div class="p-4 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/15 flex items-center justify-center text-4xl shrink-0">
                        {{ $vehicle->type->icon() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-black text-base text-slate-900 dark:text-white leading-tight">{{ $vehicle->name }}</h3>
                            @if($vehicle->is_default)
                            <span class="badge-green">Default</span>
                            @endif
                        </div>
                        <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-0.5">{{ $vehicle->type->label() }}</p>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span class="text-[11px] font-bold text-green-600 dark:text-green-400">{{ $vehicle->fuel_consumption }} km/L</span>
                            </div>
                            <span class="text-slate-200 dark:text-white/10">|</span>
                            <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">{{ $vehicle->trips_count }}× dipakai</span>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="h-px bg-slate-50 dark:bg-white/[0.04] mx-4"></div>

                {{-- Stats row --}}
                <div class="grid grid-cols-3 divide-x divide-slate-50 dark:divide-white/[0.04]">
                    <div class="px-3 py-3 text-center">
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Jenis BBM</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ $vehicle->fuel_type }}</p>
                    </div>
                    <div class="px-3 py-3 text-center">
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Harga/L</p>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-0.5">Rp{{ number_format($vehicle->fuel_price/1000, 0) }}rb</p>
                    </div>
                    <div class="px-3 py-3 text-center">
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Efisiensi</p>
                        <p class="text-xs font-bold text-green-600 dark:text-green-400 mt-0.5">{{ $vehicle->fuel_consumption }} km/L</p>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="h-px bg-slate-50 dark:bg-white/[0.04] mx-4"></div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 px-4 py-3">
                    <a href="{{ route('vehicles.edit', $vehicle) }}"
                        class="flex-1 text-center py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-white/[0.07] text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/[0.04] transition-all">
                        Edit
                    </a>

                    @if(!$vehicle->is_default)
                    <form method="POST" action="{{ route('vehicles.set-default', $vehicle) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full py-2 text-xs font-bold rounded-xl transition-all border border-green-200 dark:border-green-500/20 bg-green-50 dark:bg-green-500/8 text-green-700 dark:text-green-400 cursor-pointer hover:bg-green-100 dark:hover:bg-green-500/15">
                            Set Default
                        </button>
                    </form>
                    @endif

                    @if($vehicle->trips_count === 0)
                    <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Hapus kendaraan ini?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="p-2 rounded-xl border border-rose-200 dark:border-rose-500/20 bg-rose-50 dark:bg-rose-500/8 text-rose-600 dark:text-rose-400 cursor-pointer hover:bg-rose-100 transition-all"
                            title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Bottom add more --}}
        <a href="{{ route('vehicles.create') }}"
            class="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl border-2 border-dashed border-green-200 dark:border-green-500/20 text-sm font-bold text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-500/[0.06] transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kendaraan Lain
        </a>
        @endif

    </div>
</x-app-layout>