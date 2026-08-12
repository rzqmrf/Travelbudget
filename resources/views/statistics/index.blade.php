<x-app-layout>
    @push('styles')
    <style>
        .chart-container { position: relative; height: 280px; width: 100%; }
        @media (min-width: 768px) { .chart-container { height: 300px; } }
    </style>
    @endpush

    <x-slot name="header">
        <div>
            <h2 class="font-black text-xl text-slate-900 dark:text-white tracking-tight">Statistik</h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Analisis pengeluaran & insight perjalanan</p>
        </div>
    </x-slot>

    <div class="py-6 lg:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if($trips->isEmpty())
            {{-- Empty State --}}
            <div class="ios-card text-center py-20 px-8">
                <div class="icon-badge-blue w-16 h-16 rounded-2xl mx-auto mb-5">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Belum Ada Data</h3>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-2 max-w-xs mx-auto">Selesaikan minimal satu perjalanan untuk melihat statistik pengeluaran.</p>
                <a href="{{ route('trips.create') }}"
                    class="btn-primary inline-flex items-center gap-2 mt-6 px-6 py-3 text-sm font-bold">
                    Buat Trip Pertama
                </a>
            </div>

            @else
            @php
                $totalAllSpent = $trips->sum(fn($t) => $t->total_expenses);
                $totalAllBudget = $trips->sum('budget_amount');
                $avgSpent = $trips->count() > 0 ? $totalAllSpent / $trips->count() : 0;
                $saved = $totalAllBudget - $totalAllSpent;
            @endphp

            {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                 SUMMARY STATS
            ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="stat-card">
                    <div class="icon-badge-blue mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Trip Selesai</p>
                    <span class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $trips->count() }}</span>
                </div>

                <div class="stat-card">
                    <div class="icon-badge-amber mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Total Pengeluaran</p>
                    <span class="text-xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Rp {{ number_format($totalAllSpent, 0, ',', '.') }}</span>
                </div>

                <div class="stat-card">
                    <div class="icon-badge-purple mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Rata-rata Trip</p>
                    <span class="text-xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Rp {{ number_format($avgSpent, 0, ',', '.') }}</span>
                </div>

                <div class="stat-card">
                    @php $savedPositive = $saved >= 0; @endphp
                    <div class="{{ $savedPositive ? 'icon-badge-green' : 'icon-badge-rose' }} mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($savedPositive)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @endif
                        </svg>
                    </div>
                    <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">{{ $savedPositive ? 'Sisa Budget' : 'Over Budget' }}</p>
                    <span class="text-xl font-black tracking-tight leading-tight {{ $savedPositive ? 'text-[#00c853] dark:text-[#00e676]' : 'text-[#c62828] dark:text-[#ff5252]' }}">
                        Rp {{ number_format(abs($saved), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                 CHARTS
            ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Category Doughnut --}}
                <div class="ios-card p-6">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white mb-5 tracking-tight">Pengeluaran per Kategori</h3>
                    <div class="chart-container flex items-center justify-center">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                {{-- Monthly Line --}}
                <div class="ios-card p-6">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white mb-5 tracking-tight">Tren Bulanan</h3>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                {{-- Budget vs Spent Bar --}}
                <div class="ios-card p-6 md:col-span-2">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white mb-5 tracking-tight">Budget vs Pengeluaran — 10 Trip Terakhir</h3>
                    <div class="chart-container" style="height:320px;">
                        <canvas id="tripsChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                 HISTORY TABLE
            ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
            <div class="ios-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-white/[0.05]">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Rekap Histori Perjalanan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[9px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-bold bg-slate-50/50 dark:bg-white/[0.02]">
                                <th class="py-3 px-5">Perjalanan</th>
                                <th class="py-3 px-5">Jarak</th>
                                <th class="py-3 px-5">Kendaraan</th>
                                <th class="py-3 px-5">Budget</th>
                                <th class="py-3 px-5">Pengeluaran</th>
                                <th class="py-3 px-5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-white/[0.03]">
                            @foreach($trips as $trip)
                            @php $diff = $trip->budget_amount - $trip->total_expenses; @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="py-4 px-5">
                                    <a href="{{ route('trips.show', $trip) }}"
                                        class="font-bold text-slate-900 dark:text-white hover:text-[#00c853] dark:hover:text-[#00e676] transition-colors">{{ $trip->name }}</a>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $trip->completed_at?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="py-4 px-5 text-xs text-slate-500 dark:text-slate-400">{{ $trip->distance_km ?? '-' }} km</td>
                                <td class="py-4 px-5 text-xs text-slate-700 dark:text-slate-300">
                                    <span class="flex items-center gap-1.5">{{ $trip->vehicle?->type?->icon() ?? '' }} {{ $trip->vehicle?->name ?? '-' }}</span>
                                </td>
                                <td class="py-4 px-5 text-xs text-slate-500 dark:text-slate-400">Rp {{ number_format($trip->budget_amount, 0, ',', '.') }}</td>
                                <td class="py-4 px-5 text-xs font-bold text-slate-900 dark:text-white">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</td>
                                <td class="py-4 px-5">
                                    @if($diff >= 0)
                                    <span class="badge-green">Hemat Rp {{ number_format($diff, 0, ',', '.') }}</span>
                                    @else
                                    <span class="badge-rose">Over Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    @if(!$trips->isEmpty())
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';
            const textColor = isDark ? 'rgba(240,240,248,0.4)' : 'rgba(15,17,23,0.45)';
            const tooltipBg = isDark ? '#0f0f14' : '#ffffff';
            const tooltipBorder = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';

            Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
            Chart.defaults.plugins.tooltip.backgroundColor = tooltipBg;
            Chart.defaults.plugins.tooltip.borderColor = tooltipBorder;
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.cornerRadius = 12;
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.titleColor = isDark ? '#f0f0f8' : '#0f1117';
            Chart.defaults.plugins.tooltip.bodyColor = isDark ? 'rgba(240,240,248,0.6)' : 'rgba(15,17,23,0.6)';
            Chart.defaults.plugins.legend.labels.color = textColor;
            Chart.defaults.scale.ticks.color = textColor;

            // 1. Category Doughnut
            const categoryData = {!! json_encode($categoryData) !!};
            new Chart(document.getElementById('categoryChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(d => d.label),
                    datasets: [{
                        data: categoryData.map(d => d.value),
                        backgroundColor: categoryData.map(d => d.color),
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 10, padding: 14, font: { size: 11 } } }
                    },
                    animation: { animateRotate: true, duration: 900 }
                }
            });

            // 2. Monthly Line
            const monthlyData = {!! json_encode($formattedMonthlyData) !!};
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const gradGreen = monthlyCtx.createLinearGradient(0, 0, 0, 280);
            gradGreen.addColorStop(0, 'rgba(0,230,118,0.18)');
            gradGreen.addColorStop(1, 'rgba(0,230,118,0)');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => d.label),
                    datasets: [{
                        label: 'Pengeluaran',
                        data: monthlyData.map(d => d.value),
                        borderColor: '#00e676',
                        backgroundColor: gradGreen,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#00e676',
                        pointRadius: 4,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    },
                    animation: { duration: 1200, easing: 'easeOutQuart' }
                }
            });

            // 3. Trips Bar
            const tripData = {!! json_encode($tripData) !!};
            new Chart(document.getElementById('tripsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: tripData.map(d => d.label),
                    datasets: [
                        {
                            label: 'Budget',
                            data: tripData.map(d => d.budget),
                            backgroundColor: isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)',
                            borderRadius: 8
                        },
                        {
                            label: 'Pengeluaran',
                            data: tripData.map(d => d.spent),
                            backgroundColor: '#00e676',
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { font: { size: 10 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    },
                    animation: { duration: 1000, easing: 'easeOutQuart' }
                }
            });
        });
    </script>
    @endif
    @endpush
</x-app-layout>