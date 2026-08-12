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
            <div class="text-center py-20 glass-card rounded-[2.5rem] p-16 animate-fade-in relative overflow-hidden group">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-colors duration-750"></div>
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-purple-500/5 rounded-full blur-3xl group-hover:bg-purple-500/10 transition-colors duration-750"></div>
                
                <div class="relative z-10">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-50 to-purple-50 border border-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6 animate-float shadow-inner">
                        <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Belum Ada Template</h3>
                    <p class="text-slate-400 text-sm mt-2 max-w-md mx-auto leading-relaxed">Simpan perjalanan Anda sebagai template untuk membuat perencanaan perjalanan berikutnya dengan rute yang sama secara instan.</p>
                    <a href="{{ route('trips.index') }}" class="mt-8 inline-flex items-center gap-2 px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl text-sm transition-all shadow-[0_10px_40px_-10px_rgba(15,23,42,0.4)] hover:-translate-y-1">Lihat Perjalanan Anda</a>
                </div>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($templates as $index => $template)
                <div class="trip-card bg-white/90 backdrop-blur-md rounded-[2rem] border border-white/60 shadow-sm transition-all duration-300 hover:shadow-[0_15px_40px_-15px_rgba(99,102,241,0.25)] hover:-translate-y-1.5 overflow-hidden animate-fade-in stagger-{{ min(5, $index + 1) }}">
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-12 h-12 bg-indigo-50/80 border border-indigo-100/50 rounded-2xl flex items-center justify-center text-indigo-500 shadow-inner group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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