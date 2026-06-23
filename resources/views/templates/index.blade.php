<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl text-slate-800">Template Perjalanan</h2>
            <p class="text-xs text-slate-400 mt-0.5">Simpan dan gunakan kembali rute favorit Anda</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($templates->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm animate-fade-in">
                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
                <h3 class="text-lg font-bold text-slate-800">Belum Ada Template</h3>
                <p class="text-slate-400 text-sm mt-1 max-w-md mx-auto">Simpan trip sebagai template untuk memudahkan pembuatan trip berikutnya dengan rute yang sama.</p>
                <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition">
                    Lihat Perjalanan
                </a>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($templates as $index => $template)
                <div class="trip-card animate-fade-in stagger-{{ min(5, $index + 1) }}">
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ $template->name }}</h4>
                                    <p class="text-[10px] text-slate-400">{{ $template->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs text-slate-500 mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $template->origin_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $template->destination_name }}</span>
                            </div>
                            @if($template->default_budget)
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Rp {{ number_format($template->default_budget, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('trips.create', ['template' => $template->id]) }}" class="flex-1 text-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition">
                                Gunakan Template
                            </a>
                            <form method="POST" action="{{ route('templates.destroy', $template) }}" onsubmit="return confirm('Hapus template ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 border border-rose-200 text-rose-500 hover:bg-rose-50 rounded-xl text-xs transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>