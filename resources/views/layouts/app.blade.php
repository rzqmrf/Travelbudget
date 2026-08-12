@props([
    'hideNav' => false,
    'hideSidebar' => false,
    'hideBottomNav' => false,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10b981">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>TravelBudget — Kelola Budget Perjalanan Anda</title>
    <meta name="description" content="Aplikasi budget perjalanan terbaik — pantau pengeluaran, BBM, dan anggaran trip Anda.">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-[#F8FAF8] dark:bg-[#0d0f0d] antialiased" x-data="globalApp()" x-init="init()">

    <div class="min-h-screen flex">

        @unless($hideNav || $hideSidebar)
        {{-- ═══════════════════════════════════════════════
             SIDEBAR — Desktop only (lg+)
        ═══════════════════════════════════════════════ --}}
        <aside class="hidden lg:flex flex-col w-64 shrink-0 sticky top-0 h-screen
                       bg-white dark:bg-[#111411]
                       border-r border-slate-100 dark:border-white/[0.05]
                       no-print">

            {{-- Brand --}}
            <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-100 dark:border-white/[0.05] shrink-0">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                    style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 12px rgba(22,163,74,0.3);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <p class="font-black text-slate-900 dark:text-white text-base leading-tight">
                        Travel<span class="text-green-600">Budget</span>
                    </p>
                    <p class="text-[9px] text-slate-400 font-semibold uppercase tracking-widest">Budget Planner</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                @php
                $navItems = [
                    ['route'=>'dashboard','icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','label'=>'Dashboard','pattern'=>'dashboard'],
                    ['route'=>'trips.index','icon'=>'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7','label'=>'Perjalanan','pattern'=>'trips.*'],
                    ['route'=>'vehicles.index','icon'=>'M8 17h.01M12 17h.01M16 17h.01M3 11l1.5-5.25A2 2 0 016.4 4h11.2a2 2 0 011.9 1.75L21 11M3 11h18M3 11v6a1 1 0 001 1h1a2 2 0 002-2v0a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 002 2h1a1 1 0 001-1v-6','label'=>'Kendaraan','pattern'=>'vehicles.*'],
                    ['route'=>'statistics','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','label'=>'Statistik','pattern'=>'statistics'],
                    ['route'=>'profile.edit','icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','label'=>'Akun','pattern'=>'profile.*'],
                ];
                @endphp
                @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                    class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                           {{ $isActive ? 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 font-semibold' : 'text-slate-500 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-white/[0.03] hover:text-slate-800 dark:hover:text-slate-200' }}">
                    @if($isActive)<span class="nav-active-indicator"></span>@endif
                    <svg class="w-5 h-5 shrink-0 {{ $isActive ? 'text-green-600 dark:text-green-400' : 'text-slate-400 group-hover:text-slate-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2.2' : '1.7' }}" d="{{ $item['icon'] }}" />
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </a>
                @endforeach
            </nav>

            {{-- Quick Add Button --}}
            <div class="p-3 border-t border-slate-100 dark:border-white/[0.05] shrink-0">
                <button @click="quickAddOpen = true" type="button"
                    class="w-full flex items-center justify-center gap-2 py-3 text-sm font-bold text-white rounded-xl transition-all cursor-pointer active:scale-[0.98]"
                    style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 14px rgba(22,163,74,0.25);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Pengeluaran
                </button>
            </div>

            {{-- User --}}
            <div class="p-3 border-t border-slate-100 dark:border-white/[0.05] shrink-0" x-data="{open:false}">
                <div @click="open=!open" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-white/[0.03] transition cursor-pointer relative">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-black shrink-0"
                        style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div x-show="open" @click.away="open=false" x-transition
                        class="absolute bottom-full left-0 right-0 mb-2 rounded-xl border py-1.5 z-50
                               bg-white dark:bg-[#161a16] border-slate-100 dark:border-white/[0.06] shadow-xl">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <hr class="my-1 border-slate-100 dark:border-white/[0.04]">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/[0.08]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
        @endunless

        {{-- ═══════════════════════════════════════════════
             MAIN COLUMN
        ═══════════════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- ╔═══════════════════════════════════════════╗
                 ║ MOBILE TOP HEADER (hidden lg+)           ║
                 ╚═══════════════════════════════════════════╝ --}}
            <header class="lg:hidden sticky top-0 z-40 no-print h-16
                           bg-white dark:bg-[#111411]
                           border-b border-slate-100 dark:border-white/[0.05]"
                style="box-shadow:0 1px 0 rgba(0,0,0,0.04);">
                <div class="h-full flex items-center justify-between px-4">
                    {{-- Logo --}}
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                            style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 3px 10px rgba(22,163,74,0.28);">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-900 dark:text-white text-base leading-tight">
                                Travel<span class="text-green-600">Budget</span>
                            </p>
                            <p class="text-[9px] text-slate-400 font-semibold uppercase tracking-widest">Budget Planner</p>
                        </div>
                    </div>

                    {{-- Right actions --}}
                    <div class="flex items-center gap-2">
                        {{-- Dark mode toggle --}}
                        <button @click="toggleDarkMode()" type="button"
                            class="w-9 h-9 rounded-full flex items-center justify-center bg-slate-100 dark:bg-white/[0.08] text-slate-500 dark:text-slate-400 cursor-pointer transition">
                            <svg x-show="!darkMode" class="w-4.5 h-4.5" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="darkMode" x-cloak style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14.828 14.828a4 4 0 11-5.656-5.656 4 4 0 015.656 5.656z" />
                            </svg>
                        </button>

                        {{-- User avatar --}}
                        <div x-data="{open:false}" class="relative">
                            <button @click="open=!open" class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-black cursor-pointer"
                                style="background:linear-gradient(135deg,#16a34a,#22c55e);">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </button>
                            <div x-show="open" @click.away="open=false" x-transition
                                class="absolute top-full right-0 mt-2 w-52 rounded-2xl border py-1.5 z-50
                                       bg-white dark:bg-[#161a16] border-slate-100 dark:border-white/[0.06]
                                       shadow-xl dark:shadow-[0_16px_48px_rgba(0,0,0,0.4)]">
                                <div class="px-4 py-2.5 border-b border-slate-100 dark:border-white/[0.05]">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/[0.04]">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profil Saya
                                </a>
                                <hr class="my-1 border-slate-100 dark:border-white/[0.04]">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/[0.07]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- ╔═══════════════════════════════════════════╗
                 ║ DESKTOP TOP HEADER (hidden on mobile)    ║
                 ╚═══════════════════════════════════════════╝ --}}
            <header class="hidden lg:flex h-16 sticky top-0 z-30 no-print items-center justify-between px-8
                            bg-white/90 dark:bg-[#111411]/90 backdrop-blur-xl
                            border-b border-slate-100 dark:border-white/[0.05]">
                <div class="flex items-center gap-3 min-w-0">
                    @isset($header){{ $header }}@endisset
                </div>
                <div class="flex items-center gap-2">
                    <button @click="toggleDarkMode()" type="button"
                        class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/[0.05] transition cursor-pointer">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14.828 14.828a4 4 0 11-5.656-5.656 4 4 0 015.656 5.656z" />
                        </svg>
                    </button>
                    <a href="{{ route('trips.create') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl text-white transition-all"
                        style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 12px rgba(22,163,74,0.28);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Trip Baru
                    </a>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-grow bg-[#F8FAF8] dark:bg-[#0d0f0d] pb-24 lg:pb-8">
                {{ $slot }}
            </main>

            {{-- Footer (desktop) --}}
            <footer class="hidden lg:block py-3 text-center text-[11px] text-slate-400 dark:text-slate-600 border-t border-slate-100 dark:border-white/[0.04] no-print">
                &copy; {{ date('Y') }} TravelBudget — Semua Hak Dilindungi.
            </footer>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         TOAST NOTIFICATIONS
    ═══════════════════════════════════════════════ --}}
    <div x-data="toastManager()" class="fixed top-20 right-4 z-[9999] space-y-3 pointer-events-none no-print w-80">
        @if(session('success'))
        <div x-init="show('success', '{{ addslashes(session('success')) }}')" class="pointer-events-auto"></div>
        @endif
        @if(session('error'))
        <div x-init="show('error', '{{ addslashes(session('error')) }}')" class="pointer-events-auto"></div>
        @endif
        <template x-for="(toast, index) in toasts" :key="toast.id">
            <div x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-6 scale-95"
                x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full rounded-2xl p-4 flex items-start gap-3 pointer-events-auto
                       bg-white dark:bg-[#161a16] border border-slate-100 dark:border-white/[0.06]
                       shadow-lg dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]">
                <div class="shrink-0 mt-0.5" x-html="toast.icon"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="toast.message"></p>
                    <div class="toast-progress">
                        <div class="toast-progress-bar" :style="{ width: toast.progress + '%' }"></div>
                    </div>
                </div>
                <button @click="dismiss(toast.id)" class="shrink-0 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-white/[0.05] text-slate-400 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @unless($hideNav || $hideBottomNav)
    {{-- ═══════════════════════════════════════════════
         BOTTOM NAV — Mobile 5 tabs
    ═══════════════════════════════════════════════ --}}
    <nav class="fixed bottom-0 inset-x-0 z-40 no-print lg:hidden"
        style="padding-bottom:env(safe-area-inset-bottom,0px);">
        <div class="bg-white dark:bg-[#111411] border-t border-slate-100 dark:border-white/[0.05]"
            style="box-shadow:0 -4px 24px rgba(0,0,0,0.06);">
            <div class="flex items-stretch h-[60px] max-w-lg mx-auto">

                @php
                $tabs = [
                    ['route'=>'dashboard','pattern'=>'dashboard','icon_path'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','label'=>'Dashboard'],
                    ['route'=>'trips.index','pattern'=>'trips.*','icon_path'=>'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7','label'=>'Perjalanan'],
                    ['route'=>'vehicles.index','pattern'=>'vehicles.*','icon_path'=>'M8 17h.01M12 17h.01M16 17h.01M3 11l1.5-5.25A2 2 0 016.4 4h11.2a2 2 0 011.9 1.75L21 11M3 11h18M3 11v6a1 1 0 001 1h1a2 2 0 002-2v0a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 002 2h1a1 1 0 001-1v-6','label'=>'Kendaraan'],
                    ['route'=>'statistics','pattern'=>'statistics','icon_path'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','label'=>'Statistik'],
                    ['route'=>'profile.edit','pattern'=>'profile.*','icon_path'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','label'=>'Akun'],
                ];
                @endphp

                @foreach($tabs as $tab)
                @php $active = request()->routeIs($tab['pattern']); @endphp
                <a href="{{ route($tab['route']) }}"
                    class="flex-1 flex flex-col items-center justify-center gap-[3px] transition-all duration-200">
                    <svg class="transition-all duration-200"
                        style="width:22px;height:22px;color:{{ $active ? '#16a34a' : '#9ca3af' }};stroke-width:{{ $active ? '2.3' : '1.7' }};"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon_path'] }}" />
                    </svg>
                    <span class="text-[9.5px] font-{{ $active ? 'extrabold' : 'semibold' }} transition-all"
                        style="color:{{ $active ? '#16a34a' : '#9ca3af' }};">
                        {{ $tab['label'] }}
                    </span>
                </a>
                @endforeach

            </div>
        </div>
    </nav>
    @endunless

    {{-- ═══════════════════════════════════════════════
         QUICK ADD EXPENSE SHEET
    ═══════════════════════════════════════════════ --}}
    <div x-show="quickAddOpen" x-cloak class="fixed inset-0 z-[100] no-print">
        <div @click="quickAddOpen = false"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="absolute bottom-0 inset-x-0 max-h-[92vh] overflow-y-auto rounded-t-3xl
                    bg-white dark:bg-[#131613]
                    border-t border-slate-100 dark:border-white/[0.06]"
            style="box-shadow:0 -8px 48px rgba(0,0,0,0.12);padding-bottom:env(safe-area-inset-bottom,0px);"
            x-show="quickAddOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">

            <div class="px-5 pt-5 pb-8">
                <div class="w-10 h-1 bg-slate-200 dark:bg-white/10 rounded-full mx-auto mb-5"></div>

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Catat Pengeluaran</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Rekam biaya perjalanan dengan cepat</p>
                    </div>
                    <button @click="quickAddOpen=false" class="p-2 rounded-xl bg-slate-100 dark:bg-white/[0.06] text-slate-500 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submitQuickExpense()" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Pilih Perjalanan Aktif</label>
                        <select x-model="quickTripId" class="w-full px-4 py-3 text-sm rounded-xl">
                            <option value="">-- Pilih Trip --</option>
                            <template x-for="trip in activeTrips" :key="trip.id">
                                <option :value="trip.id" x-text="trip.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Jumlah (Rp)</label>
                            <input type="number" x-model="quickAmount" required placeholder="50000" class="w-full px-4 py-3 text-sm rounded-xl">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Kategori</label>
                            <select x-model="quickCategory" class="w-full px-4 py-3 text-sm rounded-xl">
                                <option value="fuel">⛽ Bensin</option>
                                <option value="toll">🛣️ Tol</option>
                                <option value="food">🍽️ Makan</option>
                                <option value="lodging">🏨 Penginapan</option>
                                <option value="parking">🅿️ Parkir</option>
                                <option value="maintenance">🔧 Perawatan</option>
                                <option value="other">📦 Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Lokasi</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="quickLocation" placeholder="SPBU, Warung makan..." class="flex-1 px-4 py-3 text-sm rounded-xl">
                            <button type="button" @click="autofillQuickLocation()" :disabled="quickLoadingLocation"
                                class="px-3.5 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-600 dark:text-green-400 flex items-center disabled:opacity-50 cursor-pointer">
                                <svg x-show="!quickLoadingLocation" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div x-show="quickLoadingLocation" x-cloak class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></div>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Catatan</label>
                        <input type="text" x-model="quickNote" placeholder="Isi penuh, bayar cash..." class="w-full px-4 py-3 text-sm rounded-xl">
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 text-sm font-black rounded-xl text-white cursor-pointer transition-all active:scale-[0.98]"
                        style="background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 4px 14px rgba(22,163,74,0.3);">
                        Simpan Pengeluaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Offline queue badge --}}
    <div x-show="offlineQueueCount > 0" x-cloak
        class="fixed bottom-20 left-4 z-50 px-3 py-2 text-xs font-bold rounded-2xl flex items-center gap-2 no-print
               bg-white dark:bg-[#161a16] border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-400
               shadow-lg">
        <span>📶</span>
        <span x-text="offlineQueueCount + ' antrean offline'"></span>
    </div>

    @stack('scripts')

    <script>
        function globalApp() {
            return {
                sidebarOpen: false, quickAddOpen: false, darkMode: false,
                activeTrips: [], quickTripId: '', quickAmount: '',
                quickCategory: 'fuel', quickNote: '', quickLocation: '',
                quickLoadingLocation: false, offlineQueueCount: 0,

                init() {
                    const savedTheme = localStorage.theme;
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    this.darkMode = savedTheme === 'dark' || (!savedTheme && prefersDark);
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    this.loadActiveTrips();
                    this.updateOfflineCount();
                    window.addEventListener('online', () => this.syncOfflineExpenses());
                    if (navigator.onLine) setTimeout(() => this.syncOfflineExpenses(), 2000);
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    localStorage.theme = this.darkMode ? 'dark' : 'light';
                },

                async loadActiveTrips() {
                    try {
                        const r = await fetch('/api/trips/active');
                        if (r.ok) {
                            this.activeTrips = await r.json();
                            const p = window.location.pathname.split('/');
                            const i = p.indexOf('trips');
                            if (i !== -1 && p[i+1] && !isNaN(p[i+1])) this.quickTripId = p[i+1];
                            else if (this.activeTrips.length) this.quickTripId = this.activeTrips[0].id;
                        }
                    } catch(e) {}
                },

                async autofillQuickLocation() {
                    if (!navigator.geolocation) { alert('Geolocation tidak didukung.'); return; }
                    this.quickLoadingLocation = true;
                    navigator.geolocation.getCurrentPosition(async (pos) => {
                        const { latitude: lat, longitude: lng } = pos.coords;
                        try {
                            const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18`);
                            if (r.ok) {
                                const d = await r.json();
                                this.quickLocation = d.address?.road ? d.address.road + (d.address.suburb ? ', ' + d.address.suburb : '') : d.display_name.split(',')[0];
                            }
                        } catch { this.quickLocation = `GPS (${lat.toFixed(4)}, ${lng.toFixed(4)})`; }
                        this.quickLoadingLocation = false;
                    }, (err) => { alert('GPS error: ' + err.message); this.quickLoadingLocation = false; });
                },

                updateOfflineCount() {
                    this.offlineQueueCount = JSON.parse(localStorage.getItem('travel_budget_offline_queue') || '[]').length;
                },

                submitQuickExpense() {
                    const exp = { trip_id: this.quickTripId, amount: this.quickAmount, category: this.quickCategory, note: this.quickNote, location_name: this.quickLocation, spent_at: new Date().toISOString().slice(0,19).replace('T',' ') };
                    if (!exp.trip_id || !exp.amount) { alert('Pilih perjalanan & masukkan jumlah.'); return; }
                    if (!navigator.onLine) {
                        const q = JSON.parse(localStorage.getItem('travel_budget_offline_queue') || '[]');
                        q.push(exp); localStorage.setItem('travel_budget_offline_queue', JSON.stringify(q));
                        this.updateOfflineCount(); this.quickAddOpen = false; this.quickAmount=''; this.quickNote=''; this.quickLocation='';
                        return;
                    }
                    this.sendExpenseToServer(exp);
                },

                async sendExpenseToServer(exp) {
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    try {
                        const r = await fetch(`/trips/${exp.trip_id}/expenses`, { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf}, body:JSON.stringify(exp) });
                        if (r.ok) { this.quickAddOpen=false; this.quickAmount=''; this.quickNote=''; this.quickLocation=''; window.location.reload(); }
                        else { const d = await r.json(); alert('Gagal: ' + (d.message || 'Error')); }
                    } catch { alert('Tidak dapat terhubung ke server.'); }
                },

                async syncOfflineExpenses() {
                    const q = JSON.parse(localStorage.getItem('travel_budget_offline_queue') || '[]');
                    if (!q.length) return;
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    let ok = 0; const fail = [];
                    for (const e of q) {
                        try { const r = await fetch(`/trips/${e.trip_id}/expenses`, { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf}, body:JSON.stringify(e) }); if(r.ok) ok++; else fail.push(e); } catch { fail.push(e); }
                    }
                    localStorage.setItem('travel_budget_offline_queue', JSON.stringify(fail));
                    this.updateOfflineCount();
                    if (ok > 0) { alert(`${ok} pengeluaran offline berhasil disinkronkan!`); window.location.reload(); }
                }
            };
        }

        function toastManager() {
            return {
                toasts: [], counter: 0,
                show(type, message) {
                    const id = ++this.counter;
                    const icons = {
                        success: '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        error:   '<svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    };
                    const toast = { id, type, message, icon: icons[type]||icons.success, visible:true, progress:100 };
                    this.toasts.push(toast);
                    const iv = setInterval(() => { toast.progress -= 2; if(toast.progress <= 0) { clearInterval(iv); this.dismiss(id); } }, 80);
                },
                dismiss(id) {
                    const t = this.toasts.find(t => t.id===id); if(t) t.visible=false;
                    setTimeout(() => this.toasts = this.toasts.filter(t => t.id!==id), 300);
                }
            };
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW reg error:', err));
            });
        }
    </script>
</body>
</html>