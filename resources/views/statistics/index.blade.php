<x-app-layout>
    @push('styles')
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
    @endpush

    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl text-slate-800">Analisis & Statistik</h2>
            <p class="text-xs text-slate-400 mt-0.5">Insight pengeluaran dan perjalanan Anda</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($trips->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-16 text-center animate-fade-in">
                <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-extrabold text-slate-800">Belum Ada Data</h3>
                <p class="text-slate-400 text-sm mt-1 max-w-sm mx-auto">Selesaikan setidaknya satu perjalanan untuk melihat analisis pengeluaran dan perbandingan budget.</p>
                <a href="{{ route('trips.create') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-indigo-600/10">Buat Trip Pertama</a>
            </div>
            @else
            <!-- Summary Stats -->
            @php
            $totalAllSpent = $trips->sum(fn($t) => $t->total_expenses);
            $totalAllBudget = $trips->sum('budget_amount');
            $avgSpent = $trips->count() > 0 ? $totalAllSpent / $trips->count() : 0;
            $saved = $totalAllBudget - $totalAllSpent;
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="stat-card animate-fade-in stagger-1">
                    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center mb-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-800">{{ $trips->count() }}</span>
                    <p class="text-[10px] text-slate-400 mt-0.5">Trip Selesai</p>
                </div>
                <div class="stat-card animate-fade-in stagger-2">
                    <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center mb-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold text-slate-800">Rp {{ number_format($totalAllSpent, 0, ',', '.') }}</span>
                    <p class="text-[10px] text-slate-400 mt-0.5">Total Pengeluaran</p>
                </div>
                <div class="stat-card animate-fade-in stagger-3">
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center mb-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold text-slate-800">Rp {{ number_format($avgSpent, 0, ',', '.') }}</span>
                    <p class="text-[10px] text-slate-400 mt-0.5">Rata-rata / Trip</p>
                </div>
                <div class="stat-card animate-fade-in stagger-4">
                    <div class="w-8 h-8 {{ $saved >= 0 ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-lg flex items-center justify-center mb-2">
                        @if($saved >= 0)
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        @else
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        @endif
                    </div>
                    <span class="text-xl font-extrabold {{ $saved >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format(abs($saved), 0, ',', '.') }}</span>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $saved >= 0 ? 'Total Hemat' : 'Total Defisit' }}</p>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm animate-slide-up">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Pengeluaran per Kategori</h3>
                    <div class="chart-container flex items-center justify-center">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm animate-slide-up">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Tren Pengeluaran Bulanan</h3>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm md:col-span-2 animate-slide-up">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Budget vs Pengeluaran (10 Trip Terakhir)</h3>
                    <div class="chart-container" style="height: 350px;">
                        <canvas id="tripsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden animate-slide-up">
                <div class="p-6 border-b border-slate-50">
                    <h3 class="text-sm font-bold text-slate-800">Rekap Histori Perjalanan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] text-slate-400 uppercase tracking-wider font-bold">
                                <th class="py-3 px-6">Perjalanan</th>
                                <th class="py-3 px-6">Jarak</th>
                                <th class="py-3 px-6">Kendaraan</th>
                                <th class="py-3 px-6">Budget</th>
                                <th class="py-3 px-6">Pengeluaran</th>
                                <th class="py-3 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($trips as $trip)
                            @php $diff = $trip->budget_amount - $trip->total_expenses; @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6">
                                    <a href="{{ route('trips.show', $trip) }}" class="font-semibold text-slate-800 hover:text-indigo-600 transition">{{ $trip->name }}</a>
                                    <p class="text-[10px] text-slate-400">{{ $trip->completed_at?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs">{{ $trip->distance_km ?? '-' }} km</td>
                                <td class="py-4 px-6 text-xs"><span class="flex items-center gap-1.5">{{ $trip->vehicle?->type?->icon() ?? '' }} {{ $trip->vehicle?->name ?? '-' }}</span></td>
                                <td class="py-4 px-6 text-xs text-slate-500">Rp {{ number_format($trip->budget_amount, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-xs font-bold text-slate-800">Rp {{ number_format($trip->total_expenses, 0, ',', '.') }}</td>
                                <td class="py-4 px-6">
                                    @if($diff >= 0)
                                    <span class="px-2 py-1 text-[10px] font-bold bg-emerald-50 text-emerald-600 rounded-full">Hemat Rp {{ number_format($diff, 0, ',', '.') }}</span>
                                    @else
                                    <span class="px-2 py-1 text-[10px] font-bold bg-rose-50 text-rose-600 rounded-full">Over Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
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
            Chart.defaults.font.family = 'Plus Jakarta Sans, system-ui, sans-serif';
            Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
            Chart.defaults.plugins.tooltip.cornerRadius = 12;
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.titleFont = {
                size: 12,
                weight: 'bold'
            };
            Chart.defaults.plugins.tooltip.bodyFont = {
                size: 11
            };

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
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 10,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });

            // 2. Monthly Line
            const monthlyData = {!! json_encode($formattedMonthlyData) !!};
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const gradient = monthlyCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.15)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => d.label),
                    datasets: [{
                        label: 'Pengeluaran',
                        data: monthlyData.map(d => d.value),
                        borderColor: '#4F46E5',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4F46E5',
                        pointRadius: 4,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.03)'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    }
                }
            });

            // 3. Trips Bar
            const tripData = {!! json_encode($tripData) !!};
            new Chart(document.getElementById('tripsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: tripData.map(d => d.label),
                    datasets: [{
                            label: 'Budget',
                            data: tripData.map(d => d.budget),
                            backgroundColor: '#E2E8F0',
                            borderRadius: 8
                        },
                        {
                            label: 'Pengeluaran',
                            data: tripData.map(d => d.spent),
                            backgroundColor: '#4F46E5',
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 12
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.03)'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
    @endif
    @endpush
</x-app-layout>