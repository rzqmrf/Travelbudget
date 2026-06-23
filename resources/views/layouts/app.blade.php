<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TravelBudget - Kelola Budget Perjalanan Anda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900 selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-100 shadow-xl lg:shadow-none lg:translate-x-0 lg:static lg:z-auto transition-transform duration-300 ease-in-out flex flex-col no-print">
            <!-- Logo -->
            <div class="h-16 flex items-center gap-2.5 px-5 border-b border-slate-100 shrink-0">
                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <span class="text-base font-extrabold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent tracking-tight">TravelBudget</span>
                    <span class="block text-[9px] font-semibold text-slate-400 -mt-0.5 uppercase tracking-widest">Budget Planner</span>
                </div>
                <button @click="sidebarOpen = false" class="ml-auto lg:hidden p-1 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Nav Items -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                @php
                $navItems = [
                ['route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Dashboard'],
                ['route' => 'trips.index', 'activePattern' => 'trips.*', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7', 'label' => 'Perjalanan'],
                ['route' => 'vehicles.index', 'activePattern' => 'vehicles.*', 'icon' => 'M8 17h.01M12 17h.01M16 17h.01M3 11l1.5-5.25A2 2 0 016.4 4h11.2a2 2 0 011.9 1.75L21 11M3 11h18M3 11v6a1 1 0 001 1h1a2 2 0 002-2v0a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 002 2h1a1 1 0 001-1v-6', 'label' => 'Kendaraan'],
                ['route' => 'statistics', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'label' => 'Statistik'],
                ['route' => 'templates.index', 'activePattern' => 'templates.*', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', 'label' => 'Template'],
                ];
                @endphp

                @foreach($navItems as $item)
                @php
                $isActive = isset($item['activePattern'])
                ? request()->routeIs($item['activePattern'])
                : request()->routeIs($item['route']);
                @endphp
                <a href="{{ route($item['route']) }}"
                    class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                  {{ $isActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                    @if($isActive)
                    <span class="nav-active-indicator"></span>
                    @endif
                    <svg class="w-5 h-5 shrink-0 {{ $isActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $isActive ? '2' : '1.5' }}" d="{{ $item['icon'] }}" />
                    </svg>
                    <span>{{ $item['label'] }}</span>
                    @if($item['route'] === 'trips.index' && isset($sharedTripsCount) && $sharedTripsCount > 0)
                    <span class="ml-auto px-2 py-0.5 text-[10px] font-bold bg-indigo-100 text-indigo-700 rounded-full">{{ $sharedTripsCount }}</span>
                    @endif
                </a>
                @endforeach
            </nav>

            <!-- User Section -->
            <div class="border-t border-slate-100 p-3 shrink-0">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer" x-data="{ open: false }">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-violet-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="relative">
                        <button @click="open = !open" class="p-1 rounded-lg hover:bg-slate-100">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute bottom-full right-0 mb-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil Saya
                            </a>
                            <hr class="my-1.5 border-slate-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header class="h-16 bg-white/80 backdrop-blur-xl border-b border-slate-100 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30 no-print">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-xl hover:bg-slate-100 transition">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @isset($header)
                    {{ $header }}
                    @endisset
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('trips.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-lg shadow-indigo-600/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Trip Baru
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-grow gradient-mesh">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-100 py-4 text-center text-[11px] text-slate-400 no-print">
                &copy; {{ date('Y') }} TravelBudget. Semua Hak Dilindungi.
            </footer>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div x-data="toastManager()" class="fixed top-4 right-4 z-[9999] space-y-3 pointer-events-none no-print">
        @if(session('success'))
        <div x-init="show('success', '{{ session('success') }}')"
            class="pointer-events-auto"></div>
        @endif
        @if(session('error'))
        <div x-init="show('error', '{{ session('error') }}')"
            class="pointer-events-auto"></div>
        @endif

        <template x-for="(toast, index) in toasts" :key="toast.id">
            <div x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
                class="max-w-sm w-full bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 flex items-start gap-3 pointer-events-auto">
                <div class="shrink-0 mt-0.5" x-html="toast.icon"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800" x-text="toast.message"></p>
                    <div class="toast-progress mt-2">
                        <div class="toast-progress-bar" :style="{ width: toast.progress + '%' }"></div>
                    </div>
                </div>
                <button @click="dismiss(toast.id)" class="shrink-0 p-1 rounded-lg hover:bg-slate-100 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Floating Action Button (Mobile) -->
    <a href="{{ route('trips.create') }}" class="fab sm:hidden no-print" title="Trip Baru">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </a>

    @stack('scripts')

    <script>
        function toastManager() {
            return {
                toasts: [],
                counter: 0,
                show(type, message) {
                    const id = ++this.counter;
                    const iconMap = {
                        success: '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        error: '<svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    };
                    const toast = {
                        id,
                        type,
                        message,
                        icon: iconMap[type] || iconMap.success,
                        visible: true,
                        progress: 100
                    };
                    this.toasts.push(toast);
                    // Animate progress
                    const interval = setInterval(() => {
                        toast.progress -= 2;
                        if (toast.progress <= 0) {
                            clearInterval(interval);
                            this.dismiss(id);
                        }
                    }, 80);
                },
                dismiss(id) {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            };
        }
    </script>
</body>

</html>