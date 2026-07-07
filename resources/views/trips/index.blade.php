<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl text-slate-800 leading-tight">Perjalanan Saya</h2>
            <p class="text-xs text-slate-400 mt-0.5">Kelola semua rencana dan riwayat perjalanan Anda</p>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ viewMode: 'grid', statusFilter: '{{ $status ?? '' }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Bar -->
            <div class="bg-white rounded-2xl border border-slate-100/80 shadow-sm p-4 mb-6 flex flex-wrap items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-1.5 flex-wrap">
                    @php
                        $statusFilters = [
                            '' => 'Semua',
                            'planning' => 'Perencanaan',
                            'active' => 'Aktif',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan'
                        ];
                    @endphp
                    @foreach($statusFilters as $value => $label)
                        @php
                            $isFilterActive = ($status === $value) || ($status === null && $value === '');
                        @endphp
                        <a href="{{ $value ? route('trips.index', ['status' => $value]) : route('trips.index') }}"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition duration-200
                            {{ $isFilterActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                <div class="flex items-center gap-2">
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-400 hover:text-slate-600'" class="p-2 rounded-xl transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-400 hover:text-slate-600'" class="p-2 rounded-xl transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            @if($trips->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-slate-100 shadow-sm p-16 animate-fade-in">
                <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-float">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-extrabold text-slate-800">Belum Ada Perjalanan</h3>
                <p class="text-slate-400 text-sm mt-1 max-w-sm mx-auto">Mulai rencanakan budget perjalanan pertama Anda dengan mudah sekarang.</p>
                <a href="{{ route('trips.create') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-indigo-600/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Trip Baru
                </a>
            </div>
            @else
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($trips as $index => $trip)
                @php
                $budgetPercent = $trip->budget_amount > 0 ? min(100, ($trip->total_expenses / $trip->budget_amount) * 100) : 0;
                $overBudget = $trip->total_expenses > $trip->budget_amount;
                $statusColors = [
                    'planning' => 'bg-slate-50 text-slate-600 border-slate-200',
                    'active' => 'bg-blue-50 text-blue-600 border-blue-200',
                    'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    'cancelled' => 'bg-rose-50 text-rose-600 border-rose-200'
                ];
                $sc = $statusColors[$trip->status->value] ?? $statusColors['planning'];
                $statusBarColor = match($trip->status->value) {
                    'planning' => 'from-slate-400 to-slate-500',
                    'active' => 'from-blue-500 to-indigo-600',
                    'completed' => 'from-emerald-500 to-teal-600',
                    'cancelled' => 'from-rose-500 to-red-600',
                    default => 'from-slate-400 to-slate-500'
                };
                @endphp
                <a href="{{ route('trips.show', $trip) }}" class="trip-card group block overflow-hidden bg-white rounded-3xl border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1.5 relative animate-fade-in stagger-{{ min(5, ($index % 5) + 1) }}">
                    <div class="h-2 bg-gradient-to-r {{ $statusBarColor }}"></div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100/50 text-xl shrink-0 group-hover:scale-105 transition-transform">
                                    {{ $trip->vehicle?->type?->icon() ?? '🚗' }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-extrabold text-base text-slate-800 group-hover:text-indigo-600 transition truncate leading-snug">{{ $trip->name }}</h4>
                                    <span class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase block truncate">{{ $trip->vehicle?->name ?? 'Tanpa Kendaraan' }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-[9px] font-extrabold rounded-full border {{ $sc }} shrink-0 uppercase tracking-wider">{{ $trip->status->label() }}</span>
                        </div>

                        <!-- Route Info -->
                        <div class="bg-slate-50/60 border border-slate-100/50 rounded-2xl p-4 mb-4">
                            <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="truncate">{{ $trip->origin_name }}</span>
                            </div>
                            <div class="h-4 border-l border-dashed border-slate-300 ml-1 my-0.5"></div>
                            <div class="flex items-center gap-2.5 text-xs font-semibold text-slate-700">
                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                <span class="truncate">{{ $trip->destination_name }}</span>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-slate-100/80 flex justify-between text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                <span>{{ $trip->distance_km ?? 0 }} km</span>
                                <span>{{ $trip->formatted_duration }}</span>
                            </div>
                        </div>

                        <!-- Budget Usage -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center text-[10px] font-bold mb-1.5 uppercase tracking-wider">
                                <span class="text-slate-400">Penggunaan Budget</span>
                                <span class="{{ $overBudget ? 'text-rose-600 font-extrabold' : 'text-slate-500' }}">{{ round($budgetPercent) }}%</span>
                            </div>
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $overBudget ? 'bg-rose-500' : ($budgetPercent >= 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                    style="width: {{ min(100, $budgetPercent) }}%"></div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100/80 text-xs mt-2">
                            <span class="text-[10px] font-semibold text-slate-400">{{ $trip->created_at->diffForHumans() }}</span>
                            <div class="text-right">
                                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">Total Biaya</span>
                                <span class="font-black text-sm {{ $overBudget ? 'text-rose-600' : 'text-slate-800' }}">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'" x-cloak class="bg-white rounded-3xl border border-slate-100/80 shadow-sm overflow-hidden animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold bg-slate-50/50 uppercase tracking-wider">
                                <th class="py-3.5 px-6">Nama Perjalanan</th>
                                <th class="py-3.5 px-6">Rute</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6">Anggaran</th>
                                <th class="py-3.5 px-6">Pengeluaran</th>
                                <th class="py-3.5 px-6">Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($trips as $trip)
                            @php
                            $statusColors = [
                                'planning' => 'bg-slate-50 text-slate-600 border-slate-200',
                                'active' => 'bg-blue-50 text-blue-600 border-blue-200',
                                'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                'cancelled' => 'bg-rose-50 text-rose-600 border-rose-200'
                            ];
                            $sc = $statusColors[$trip->status->value] ?? $statusColors['planning'];
                            @endphp
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="py-4 px-6"><a href="{{ route('trips.show', $trip) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition">{{ $trip->name }}</a></td>
                                <td class="py-4 px-6 text-slate-500 font-medium truncate max-w-[220px]">{{ $trip->origin_name }} &rarr; {{ $trip->destination_name }}</td>
                                <td class="py-4 px-6"><span class="px-2.5 py-0.5 text-[9px] font-extrabold rounded-full border {{ $sc }} uppercase tracking-wider">{{ $trip->status->label() }}</span></td>
                                <td class="py-4 px-6 text-slate-600 font-semibold">Rp {{ number_format($trip->budget_amount, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 font-extrabold {{ $trip->total_expenses > $trip->budget_amount ? 'text-rose-600' : 'text-slate-800' }}">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-slate-400 text-xs font-semibold">{{ $trip->created_at->translatedFormat('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($trips->hasPages())
            <div class="mt-6">
                {{ $trips->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>