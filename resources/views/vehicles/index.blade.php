<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-xl text-slate-800 leading-tight">Kendaraan Saya</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kelola daftar kendaraan untuk perhitungan BBM</p>
            </div>
            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-indigo-600/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kendaraan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($vehicles->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-16 text-center animate-fade-in">
                <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-float">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 17h.01M12 17h.01M16 17h.01M3 11l1.5-5.25A2 2 0 016.4 4h11.2a2 2 0 011.9 1.75L21 11M3 11h18M3 11v6a1 1 0 001 1h1a2 2 0 002-2v0a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 002 2h1a1 1 0 001-1v-6" />
                    </svg>
                </div>
                <h3 class="text-lg font-extrabold text-slate-800">Belum Ada Kendaraan</h3>
                <p class="text-slate-400 text-sm mt-1 max-w-md mx-auto">Tambahkan kendaraan Anda untuk memulai perhitungan biaya bensin otomatis.</p>
                <a href="{{ route('vehicles.create') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-indigo-600/10">Tambah Kendaraan Pertama</a>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($vehicles as $vehicle)
                <div class="trip-card group animate-fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl flex items-center justify-center text-3xl border border-slate-100 group-hover:scale-105 transition-transform">
                                    {{ $vehicle->type->icon() }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-base text-slate-800 group-hover:text-indigo-600 transition">{{ $vehicle->name }}</h3>
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">{{ $vehicle->type->label() }}</span>
                                </div>
                            </div>
                            @if($vehicle->is_default)
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-full">Default</span>
                            @endif
                        </div>

                        <!-- Fuel Efficiency Badge -->
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-3 mb-4 border border-emerald-100/50">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-emerald-600 font-semibold">Efisiensi BBM</span>
                                <span class="text-lg font-extrabold text-emerald-700">{{ $vehicle->fuel_consumption }} <span class="text-[10px] font-medium">km/L</span></span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-400">Jenis BBM</span>
                                <span class="text-xs font-semibold text-slate-700">{{ $vehicle->fuel_type }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-400">Harga BBM</span>
                                <span class="text-xs font-semibold text-slate-700">Rp {{ number_format($vehicle->fuel_price, 0, ',', '.') }}/L</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-400">Digunakan</span>
                                <span class="text-xs font-semibold text-slate-700">{{ $vehicle->trips_count }} Trip</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 mt-5 pt-4 border-t border-slate-50">
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="flex-1 text-center py-2 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl text-xs font-semibold text-slate-600 transition">Edit</a>

                            @if(!$vehicle->is_default)
                            <form method="POST" action="{{ route('vehicles.set-default', $vehicle) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full text-center py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 text-indigo-700 rounded-xl text-xs font-semibold transition">Set Default</button>
                            </form>
                            @endif

                            @if($vehicle->trips_count === 0)
                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Hapus kendaraan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-rose-50 text-rose-400 rounded-xl border border-transparent hover:border-rose-100 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>