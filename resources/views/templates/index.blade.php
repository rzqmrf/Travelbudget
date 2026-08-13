<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-xl text-slate-900 dark:text-white tracking-tight">Template Perjalanan</h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Gunakan kembali rute & budget favorit Anda secara instan</p>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6 animate-fade-in">
        @if($templates->isEmpty())
        <div class="card p-10 text-center">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Belum Ada Template</h3>
            <p class="text-slate-400 dark:text-slate-500 text-sm mt-2 max-w-md mx-auto leading-relaxed">Simpan perjalanan Anda sebagai template untuk membuat perencanaan perjalanan berikutnya dengan rute yang sama secara instan.</p>
            <a href="{{ route('trips.index') }}" class="btn-primary mt-6 inline-flex items-center gap-2">
                Lihat Perjalanan Anda
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $index => $template)
            <div class="trip-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white leading-tight">{{ $template->name }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $template->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 dark:text-slate-400 mb-5">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span class="truncate font-medium">{{ $template->origin_name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span class="truncate font-medium">{{ $template->destination_name }}</span>
                        </div>
                        @if($template->default_budget)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($template->default_budget, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-slate-100 dark:border-white/[0.05]">
                    <a href="{{ route('trips.create', ['template' => $template->id]) }}" class="flex-1 text-center btn-primary justify-center text-xs py-2">
                        Gunakan Template
                    </a>
                    <form method="POST" action="{{ route('templates.destroy', $template) }}" onsubmit="return confirm('Hapus template ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 border border-rose-200/80 dark:border-rose-500/20 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</x-app-layout>