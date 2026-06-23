<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h2 class="font-extrabold text-xl text-slate-800">Perjalanan Saya</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kelola semua rencana dan riwayat perjalanan</p>
            </div>
            <a href="{{ route('trips.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Trip Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ viewMode: 'grid', statusFilter: '{{ $status ?? '' }}' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter Bar -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6 flex flex-wrap items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-2">
                    <template x-for="s in [{v:'',l:'Semua'},{v:'planning',l:'Perencanaan'},{v:'active',l:'Aktif'},{v:'completed',l:'Selesai'},{v:'cancelled',l:'Dibatalkan'}]">
                        <button @click="statusFilter = s.v; window.location.href = s.v ? '?status=' + s.v : '{{ route('trips.index') }}'"
                            :class="statusFilter === s.v ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition" x-text="s.l"></button>
                    </template>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-400 hover:text-slate-600'" class="p-1.5 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-indigo-50 text-indigo-600' : 'text-slate-400 hover:text-slate-600'" class="p-1.5 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            @if($trips->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm animate-fade-in">
                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <h3 class="text-lg font-bold text-slate-800">Belum Ada Perjalanan</h3>
                <p class="text-slate-400 text-sm mt-1">Mulai buat rencana perjalanan pertama Anda.</p>
                <a href="{{ route('trips.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Trip Baru
                </a>
            </div>
            @else
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($trips as $index => $trip)
                @php
                $budgetPercent = $trip->budget_amount > 0 ? min(100, ($trip->total_expenses / $trip->budget_amount) * 100) : 0;
                $overBudget = $trip->total_expenses > $trip->budget_amount;
                $statusColors = ['planning' => 'bg-slate-50 text-slate-600 border-slate-200', 'active' => 'bg-blue-50 text-blue-600 border-blue-200', 'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-200', 'cancelled' => 'bg-rose-50 text-rose-600 border-rose-200'];
                $sc = $statusColors[$trip->status->value] ?? $statusColors['planning'];
                @endphp
                <a href="{{ route('trips.show', $trip) }}" class="trip-card group block animate-fade-in stagger-{{ min(5, ($index % 5) + 1) }}">
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xl shrink-0">{{ $trip->vehicle?->type?->icon() ?? '🚗' }}</span>
                                <h4 class="font-bold text-slate-800 group-hover:text-indigo-600 transition truncate">{{ $trip->name }}</h4>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full border {{ $sc }} shrink-0">{{ $trip->status->label() }}</span>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-400 mb-4">
                            <p class="truncate">{{ $trip->origin_name }} &rarr; {{ $trip->destination_name }}</p>
                            <div class="flex items-center gap-3">
                                @if($trip->distance_km)
                                <span>{{ $trip->distance_km }} km</span>
                                @endif
                                @if($trip->duration_minutes)
                                <span>{{ $trip->formatted_duration }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Budget Bar -->
                        <div class="mb-3">
                            <div class="flex justify-between text-[10px] mb-1">
                                <span class="text-slate-400">Budget</span>
                                <span class="{{ $overBudget ? 'text-rose-500 font-bold' : 'text-slate-500' }}">{{ round($budgetPercent) }}%</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $overBudget ? 'bg-rose-500' : ($budgetPercent >= 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                    style="width: {{ min(100, $budgetPercent) }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">{{ $trip->created_at->diffForHumans() }}</span>
                            <span class="font-bold {{ $overBudget ? 'text-rose-600' : 'text-slate-700' }}">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'" x-cloak class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-xs font-semibold">
                                <th class="py-3 px-5">Nama</th>
                                <th class="py-3 px-5">Rute</th>
                                <th class="py-3 px-5">Status</th>
                                <th class="py-3 px-5">Anggaran</th>
                                <th class="py-3 px-5">Pengeluaran</th>
                                <th class="py-3 px-5">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($trips as $trip)
                            @php $sc = $statusColors[$trip->status->value] ?? $statusColors['planning']; @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-5"><a href="{{ route('trips.show', $trip) }}" class="font-semibold text-slate-800 hover:text-indigo-600 transition">{{ $trip->name }}</a></td>
                                <td class="py-4 px-5 text-slate-500 truncate max-w-[200px]">{{ $trip->origin_name }} &rarr; {{ $trip->destination_name }}</td>
                                <td class="py-4 px-5"><span class="px-2 py-0.5 text-[10px] font-semibold rounded-full border {{ $sc }}">{{ $trip->status->label() }}</span></td>
                                <td class="py-4 px-5 text-slate-600">Rp {{ number_format($trip->budget_amount, 0, ',', '.') }}</td>
                                <td class="py-4 px-5 font-semibold {{ $trip->total_expenses > $trip->budget_amount ? 'text-rose-600' : 'text-slate-800' }}">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</td>
                                <td class="py-4 px-5 text-slate-400 text-xs">{{ $trip->created_at->translatedFormat('d M Y') }}</td>
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