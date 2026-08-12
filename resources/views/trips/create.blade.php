<x-app-layout>
    @push('styles')
        @if($googleMapsApiKey)
        {{-- Google Maps loaded in scripts --}}
        @else
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        @endif
        <style>
            #map {
                height: 320px;
                border-radius: 1.5rem;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                border: 1px solid rgb(241 245 249);
            }

            @media (min-width: 768px) {
                #map {
                    height: 500px;
                }
            }

            .route-card {
                transition: all 0.2s ease;
            }

            .route-card:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgb(0 0 0 / 0.08);
            }

            .route-card.selected {
                transform: translateY(-1px);
                box-shadow: 0 0 0 2px #4F46E5, 0 4px 12px rgb(79 70 229 / 0.2);
            }

            .search-dropdown {
                backdrop-filter: blur(8px);
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('trips.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                &larr; Kembali
            </a>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Buat Rencana Perjalanan Baru
            </h2>
        </div>
    </x-slot>

    <div class="py-10" x-data="tripPlanner()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($vehicles->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center max-w-2xl mx-auto">
                    <span class="text-5xl block mb-4">🚗</span>
                    <h3 class="text-lg font-bold text-slate-800">Tambahkan kendaraan terlebih dahulu</h3>
                    <p class="text-slate-500 text-sm mt-1">
                        Untuk menghitung konsumsi bensin dan estimasi biaya secara akurat, Anda harus mendaftarkan setidaknya satu kendaraan terlebih dahulu.
                    </p>
                    <a href="{{ route('vehicles.create') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition shadow-lg shadow-indigo-600/10">
                        Tambah Kendaraan Sekarang
                    </a>
                </div>
            @else
                <form method="POST" action="{{ route('trips.store') }}" @submit="prepareSubmit($event)" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    @csrf

                    <!-- Left Column: Input Form -->
                    <div class="lg:col-span-5 space-y-6">

                        <!-- Trip Info Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
                            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-50 pb-3">Informasi Trip &amp; Budget</h3>

                            <!-- Trip Name -->
                            <div>
                                <x-input-label for="name" value="Nama Perjalanan" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    required placeholder="Contoh: Mudik Lebaran, Roadtrip Bali"
                                    value="{{ $template?->name ?? old('name') }}" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Vehicle Selector -->
                            <div>
                                <x-input-label for="vehicle_id" value="Kendaraan yang Digunakan" />
                                <select id="vehicle_id" name="vehicle_id"
                                    x-model="selectedVehicleId"
                                    @change="recalculateFuel()"
                                    class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                                data-consumption="{{ $vehicle->fuel_consumption }}"
                                                data-price="{{ $vehicle->fuel_price }}"
                                                {{ $vehicle->is_default ? 'selected' : '' }}>
                                            {{ $vehicle->type->icon() }} {{ $vehicle->name }} ({{ $vehicle->fuel_type }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                            </div>

                            <!-- Budget Amount -->
                            <div>
                                <x-input-label for="budget_amount" value="Anggaran yang Dibawa (Rupiah)" />
                                <x-text-input id="budget_amount" class="block mt-1 w-full" type="number"
                                    name="budget_amount" required placeholder="Contoh: 1000000"
                                    value="{{ $template?->default_budget ?? old('budget_amount') }}" />
                                <x-input-error :messages="$errors->get('budget_amount')" class="mt-2" />
                            </div>

                            <!-- Daily Budget Limit -->
                            <div>
                                <x-input-label for="daily_budget_limit" value="Batas Budget Harian (Opsional)" />
                                <x-text-input id="daily_budget_limit" class="block mt-1 w-full" type="number"
                                    name="daily_budget_limit" placeholder="Biarkan kosong jika tidak ada limit"
                                    value="{{ old('daily_budget_limit') }}" />
                                <p class="text-[10px] text-slate-400 mt-1">Sistem akan memberi peringatan jika pengeluaran melebihi batas ini per hari.</p>
                                <x-input-error :messages="$errors->get('daily_budget_limit')" class="mt-2" />
                            </div>

                            <!-- Round Trip Toggle -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <label for="is_round_trip" class="flex items-center gap-3 cursor-pointer flex-1">
                                    <div class="relative">
                                        <input type="checkbox" id="is_round_trip" name="is_round_trip"
                                            x-model="isRoundTrip" @change="recalculateFuel()"
                                            value="1" class="sr-only peer">
                                        <div class="w-10 h-5 bg-slate-200 peer-checked:bg-indigo-600 rounded-full transition-colors"></div>
                                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-slate-800">Pulang-Pergi (Round Trip)</span>
                                        <p class="text-[10px] text-slate-400">Jarak &amp; biaya bensin dikalikan 2</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Return Date Picker -->
                            <div x-show="isRoundTrip" x-transition x-cloak class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-100/50 space-y-2">
                                <x-input-label for="return_date" value="Tanggal Kepulangan" />
                                <input type="date" id="return_date" name="return_date"
                                    value="{{ old('return_date') }}"
                                    class="block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm" />
                                <x-input-error :messages="$errors->get('return_date')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" value="Catatan Tambahan (Opsional)" />
                                <textarea id="notes" name="notes" rows="3"
                                    class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm"
                                    placeholder="Catatan tempat istirahat, pengeluaran darurat, dll.">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Route Planning Card -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
                            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-50 pb-3">Tentukan Rute</h3>

                            <!-- Origin Search -->
                            <div class="relative">
                                <x-input-label for="origin_search" value="Titik Awal (Origin)" />
                                <div class="flex gap-2 mt-1">
                                    <div class="relative flex-1">
                                        <x-text-input id="origin_search" class="w-full pr-8" type="text"
                                            x-model="originSearchQuery"
                                            @keydown.enter.prevent="searchPlace('origin')"
                                            @input.debounce.500ms="autoSearch('origin')"
                                            placeholder="Cari lokasi awal..." />
                                        <span x-show="originName" class="absolute right-2 top-1/2 -translate-y-1/2 text-emerald-500 text-sm">✓</span>
                                    </div>
                                    <button type="button" @click="searchPlace('origin')"
                                        class="px-4 py-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl text-sm font-semibold text-slate-700 transition">
                                        Cari
                                    </button>
                                </div>
                                <div x-show="originSearchResults.length > 0"
                                    x-cloak
                                    class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-100 rounded-xl shadow-xl search-dropdown max-h-60 overflow-y-auto">
                                    <template x-for="place in originSearchResults" :key="place.name">
                                        <button type="button" @click="selectPlace('origin', place)"
                                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 text-xs border-b border-slate-50 last:border-b-0 transition">
                                            <span class="font-semibold block text-slate-700 truncate" x-text="place.name"></span>
                                            <span class="text-slate-400 text-[10px]" x-text="place.type"></span>
                                        </button>
                                    </template>
                                </div>
                                <div class="mt-2 text-xs text-indigo-600 font-semibold" x-show="originName">
                                    ✓ <span class="text-slate-700 font-medium" x-text="originName"></span>
                                </div>
                            </div>

                            <!-- Destination Search -->
                            <div class="relative">
                                <x-input-label for="destination_search" value="Titik Tujuan (Destination)" />
                                <div class="flex gap-2 mt-1">
                                    <div class="relative flex-1">
                                        <x-text-input id="destination_search" class="w-full pr-8" type="text"
                                            x-model="destinationSearchQuery"
                                            @keydown.enter.prevent="searchPlace('destination')"
                                            @input.debounce.500ms="autoSearch('destination')"
                                            placeholder="Cari lokasi tujuan..." />
                                        <span x-show="destinationName" class="absolute right-2 top-1/2 -translate-y-1/2 text-emerald-500 text-sm">✓</span>
                                    </div>
                                    <button type="button" @click="searchPlace('destination')"
                                        class="px-4 py-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl text-sm font-semibold text-slate-700 transition">
                                        Cari
                                    </button>
                                </div>
                                <div x-show="destinationSearchResults.length > 0"
                                    x-cloak
                                    class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-100 rounded-xl shadow-xl search-dropdown max-h-60 overflow-y-auto">
                                    <template x-for="place in destinationSearchResults" :key="place.name">
                                        <button type="button" @click="selectPlace('destination', place)"
                                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 text-xs border-b border-slate-50 last:border-b-0 transition">
                                            <span class="font-semibold block text-slate-700 truncate" x-text="place.name"></span>
                                            <span class="text-slate-400 text-[10px]" x-text="place.type"></span>
                                        </button>
                                    </template>
                                </div>
                                <div class="mt-2 text-xs text-indigo-600 font-semibold" x-show="destinationName">
                                    ✓ <span class="text-slate-700 font-medium" x-text="destinationName"></span>
                                </div>
                            </div>

                            <!-- Loading indicator -->
                            <div class="flex items-center justify-center py-3 gap-2" x-show="isLoading" x-cloak>
                                <div class="w-4 h-4 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-xs text-slate-500 font-medium">Mencari rute terbaik...</span>
                            </div>

                            <!-- Route Summary Badge -->
                            <div x-show="selectedRouteIndex !== null" x-cloak
                                class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold text-indigo-800" x-text="routes[selectedRouteIndex]?.route_name ?? ''"></p>
                                        <p class="text-[10px] text-indigo-600 mt-0.5">
                                            <span x-text="routes[selectedRouteIndex]?.distance_km ?? ''"></span> km
                                            <template x-if="isRoundTrip"> &nbsp;× 2 (pulang-pergi)</template>
                                            &nbsp;·&nbsp;
                                            <span x-text="formatDuration(routes[selectedRouteIndex]?.duration_minutes ?? 0)"></span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-indigo-500">Est. Bensin</p>
                                        <p class="text-sm font-extrabold text-indigo-700"
                                            x-text="'Rp ' + formatRupiah(currentFuelCost())"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Map & Route Comparison -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Map Card -->
                        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
                            <div id="map"></div>
                            <div class="mt-2 text-center text-xs text-slate-400">
                                @if($googleMapsApiKey)
                                    Klik kiri untuk menetapkan Titik Awal, klik kanan untuk Titik Tujuan
                                @else
                                    Klik kiri untuk Titik Awal (pertama), lalu Titik Tujuan · Gunakan pencarian untuk presisi lebih tinggi
                                @endif
                            </div>
                        </div>

                        <!-- Route Comparison Options -->
                        <div x-show="routes.length > 0" x-cloak class="space-y-3">
                            <h4 class="text-sm font-bold text-slate-800">Pilih Rute Terbaik</h4>
                            <div class="grid grid-cols-1 gap-3">
                                <template x-for="(route, index) in routes" :key="index">
                                    <div @click="selectRoute(index)"
                                         :class="selectedRouteIndex === index ? 'route-card selected border-indigo-600 bg-indigo-50/40' : 'route-card border-slate-100 hover:border-slate-200 bg-white'"
                                         class="p-5 rounded-2xl border shadow-sm cursor-pointer flex items-center justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: routeColor(index) }"></span>
                                                <span class="font-bold text-slate-800 text-sm" x-text="route.route_name"></span>
                                                <span class="text-[10px] text-slate-400" x-text="route.summary ? '(' + route.summary + ')' : ''"></span>
                                            </div>
                                            <div class="flex gap-4 mt-2 text-xs text-slate-500">
                                                <span>📏 <strong class="text-slate-700" x-text="route.distance_km + ' km'"></strong></span>
                                                <span>⏱️ <strong class="text-slate-700" x-text="formatDuration(route.duration_minutes)"></strong></span>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-[10px] text-slate-400 block">Est. Bensin</span>
                                            <span class="text-base font-extrabold text-indigo-600"
                                                x-text="'Rp ' + formatRupiah(route.estimated_fuel_cost)"></span>
                                            <template x-if="isRoundTrip">
                                                <span class="text-[10px] text-violet-500 block">Pulang-pergi (×2)</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Form Data -->
                        <input type="hidden" name="origin_name" x-model="originName">
                        <input type="hidden" name="origin_lat" x-model="originLat">
                        <input type="hidden" name="origin_lng" x-model="originLng">

                        <input type="hidden" name="destination_name" x-model="destinationName">
                        <input type="hidden" name="destination_lat" x-model="destinationLat">
                        <input type="hidden" name="destination_lng" x-model="destinationLng">

                        <!-- Hidden input for routes list JSON -->
                        <input type="hidden" name="routes" :value="JSON.stringify(routes)">

                        <!-- Submit Button -->
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm" x-show="selectedRouteIndex !== null" x-cloak>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                                <div class="text-left sm:text-right" x-show="selectedRouteIndex !== null">
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Total Estimasi Bensin</p>
                                    <p class="text-lg font-extrabold text-indigo-700 dark:text-indigo-400"
                                        x-text="'Rp ' + formatRupiah(currentFuelCost())"></p>
                                </div>
                                <x-primary-button class="w-full sm:w-auto justify-center py-3">
                                    Simpan Rencana Perjalanan 🚀
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @push('scripts')
        @if($googleMapsApiKey)
            <script>
                // Define callback for Google Maps to call when loaded
                window.initGoogleMaps = function () {
                    window.__googleMapsLoaded = true;
                    document.dispatchEvent(new Event('google-maps-ready'));
                };
            </script>
            <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places&callback=initGoogleMaps&language=id" async defer></script>
        @else
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        @endif
        <script>
            const GOOGLE_MAPS_API_KEY = @json($googleMapsApiKey);

            function tripPlanner() {
                return {
                    // Search states
                    originSearchQuery: @json($template?->origin_name ?? ''),
                    destinationSearchQuery: @json($template?->destination_name ?? ''),
                    originSearchResults: [],
                    destinationSearchResults: [],

                    // Location states
                    originName: @json($template?->origin_name ?? ''),
                    originLat: @json($template?->origin_lat ?? null),
                    originLng: @json($template?->origin_lng ?? null),
                    destinationName: @json($template?->destination_name ?? ''),
                    destinationLat: @json($template?->destination_lat ?? null),
                    destinationLng: @json($template?->destination_lng ?? null),

                    // Options
                    isRoundTrip: false,
                    selectedVehicleId: '{{ $vehicles->first()?->id }}',

                    // Maps & Routes states
                    map: null,
                    googleMap: null,
                    originMarker: null,
                    destinationMarker: null,
                    routeLayers: [],
                    routes: [],
                    selectedRouteIndex: null,
                    isLoading: false,

                    init() {
                        this.$nextTick(() => {
                            if (GOOGLE_MAPS_API_KEY) {
                                if (window.__googleMapsLoaded) {
                                    this.initGoogleMap();
                                } else {
                                    document.addEventListener('google-maps-ready', () => this.initGoogleMap());
                                }
                            } else {
                                this.initLeafletMap();
                            }

                            // If template loaded with origin/destination, auto-fetch routes
                            if (this.originLat && this.destinationLat) {
                                this.$nextTick(() => this.fetchRoutes());
                            }
                        });
                    },

                    // =====================
                    // GOOGLE MAPS INIT
                    // =====================
                    initGoogleMap() {
                        const center = this.originLat
                            ? { lat: parseFloat(this.originLat), lng: parseFloat(this.originLng) }
                            : { lat: -2.5489, lng: 118.0149 };

                        this.googleMap = new google.maps.Map(document.getElementById('map'), {
                            center,
                            zoom: this.originLat ? 12 : 5,
                            mapTypeControl: false,
                            streetViewControl: false,
                            fullscreenControl: true,
                            styles: [
                                { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }
                            ]
                        });

                        // Click: set origin first, then destination
                        this.googleMap.addListener('click', (e) => {
                            const lat = e.latLng.lat();
                            const lng = e.latLng.lng();
                            if (!this.originLat) {
                                this.setMarkerGoogle('origin', lat, lng, `${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                            } else {
                                this.setMarkerGoogle('destination', lat, lng, `${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                                this.fetchRoutes();
                            }
                        });

                        // Right-click: set destination
                        this.googleMap.addListener('rightclick', (e) => {
                            const lat = e.latLng.lat();
                            const lng = e.latLng.lng();
                            this.setMarkerGoogle('destination', lat, lng, `${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                            this.fetchRoutes();
                        });

                        // If template loaded, place markers
                        if (this.originLat && this.originLng) {
                            this.setMarkerGoogle('origin', this.originLat, this.originLng, this.originName);
                        }
                        if (this.destinationLat && this.destinationLng) {
                            this.setMarkerGoogle('destination', this.destinationLat, this.destinationLng, this.destinationName);
                        }
                    },

                    setMarkerGoogle(type, lat, lng, name) {
                        lat = parseFloat(lat);
                        lng = parseFloat(lng);
                        if (type === 'origin') {
                            this.originLat = lat;
                            this.originLng = lng;
                            this.originName = name;
                            if (this.originMarker) {
                                this.originMarker.setPosition({ lat, lng });
                            } else {
                                this.originMarker = new google.maps.Marker({
                                    position: { lat, lng },
                                    map: this.googleMap,
                                    draggable: true,
                                    label: { text: 'A', color: 'white', fontWeight: 'bold' },
                                    icon: {
                                        path: google.maps.SymbolPath.CIRCLE,
                                        fillColor: '#10B981',
                                        fillOpacity: 1,
                                        strokeColor: '#fff',
                                        strokeWeight: 2,
                                        scale: 12,
                                    },
                                    title: 'Titik Asal'
                                });
                                this.originMarker.addListener('dragend', (e) => {
                                    this.originLat = e.latLng.lat();
                                    this.originLng = e.latLng.lng();
                                    this.originName = `${this.originLat.toFixed(4)}, ${this.originLng.toFixed(4)}`;
                                    this.fetchRoutes();
                                });
                            }
                        } else {
                            this.destinationLat = lat;
                            this.destinationLng = lng;
                            this.destinationName = name;
                            if (this.destinationMarker) {
                                this.destinationMarker.setPosition({ lat, lng });
                            } else {
                                this.destinationMarker = new google.maps.Marker({
                                    position: { lat, lng },
                                    map: this.googleMap,
                                    draggable: true,
                                    label: { text: 'B', color: 'white', fontWeight: 'bold' },
                                    icon: {
                                        path: google.maps.SymbolPath.CIRCLE,
                                        fillColor: '#EF4444',
                                        fillOpacity: 1,
                                        strokeColor: '#fff',
                                        strokeWeight: 2,
                                        scale: 12,
                                    },
                                    title: 'Titik Tujuan'
                                });
                                this.destinationMarker.addListener('dragend', (e) => {
                                    this.destinationLat = e.latLng.lat();
                                    this.destinationLng = e.latLng.lng();
                                    this.destinationName = `${this.destinationLat.toFixed(4)}, ${this.destinationLng.toFixed(4)}`;
                                    this.fetchRoutes();
                                });
                            }
                        }
                    },

                    // =====================
                    // LEAFLET MAP INIT
                    // =====================
                    initLeafletMap() {
                        const center = this.originLat
                            ? [parseFloat(this.originLat), parseFloat(this.originLng)]
                            : [-2.5489, 118.0149];

                        this.map = L.map('map').setView(center, this.originLat ? 12 : 5);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.map);

                        // Try to geolocate user
                        if (navigator.geolocation && !this.originLat) {
                            navigator.geolocation.getCurrentPosition((position) => {
                                this.map.setView([position.coords.latitude, position.coords.longitude], 13);
                            });
                        }

                        // Left-click: set origin then destination
                        this.map.on('click', (e) => {
                            if (!this.originLat) {
                                this.setMarkerLeaflet('origin', e.latlng.lat, e.latlng.lng, `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`);
                            } else {
                                this.setMarkerLeaflet('destination', e.latlng.lat, e.latlng.lng, `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`);
                                this.fetchRoutes();
                            }
                        });

                        // Right-click: set destination
                        this.map.on('contextmenu', (e) => {
                            this.setMarkerLeaflet('destination', e.latlng.lat, e.latlng.lng, `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`);
                            this.fetchRoutes();
                        });

                        // If template loaded, place markers
                        if (this.originLat && this.originLng) {
                            this.setMarkerLeaflet('origin', this.originLat, this.originLng, this.originName);
                        }
                        if (this.destinationLat && this.destinationLng) {
                            this.setMarkerLeaflet('destination', this.destinationLat, this.destinationLng, this.destinationName);
                        }
                    },

                    setMarkerLeaflet(type, lat, lng, name) {
                        lat = parseFloat(lat);
                        lng = parseFloat(lng);
                        if (type === 'origin') {
                            this.originLat = lat;
                            this.originLng = lng;
                            this.originName = name;
                            if (this.originMarker) {
                                this.originMarker.setLatLng([lat, lng]);
                            } else {
                                this.originMarker = L.marker([lat, lng], { draggable: true })
                                    .addTo(this.map)
                                    .bindPopup('<b>Titik Asal</b>')
                                    .openPopup();
                                this.originMarker.on('dragend', (e) => {
                                    const pos = e.target.getLatLng();
                                    this.originLat = pos.lat;
                                    this.originLng = pos.lng;
                                    this.originName = `${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}`;
                                    this.fetchRoutes();
                                });
                            }
                        } else {
                            this.destinationLat = lat;
                            this.destinationLng = lng;
                            this.destinationName = name;
                            if (this.destinationMarker) {
                                this.destinationMarker.setLatLng([lat, lng]);
                            } else {
                                this.destinationMarker = L.marker([lat, lng], { draggable: true })
                                    .addTo(this.map)
                                    .bindPopup('<b>Titik Tujuan</b>')
                                    .openPopup();
                                this.destinationMarker.on('dragend', (e) => {
                                    const pos = e.target.getLatLng();
                                    this.destinationLat = pos.lat;
                                    this.destinationLng = pos.lng;
                                    this.destinationName = `${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}`;
                                    this.fetchRoutes();
                                });
                            }
                        }
                    },

                    // =====================
                    // SHARED LOGIC
                    // =====================
                    async autoSearch(type) {
                        const query = type === 'origin' ? this.originSearchQuery : this.destinationSearchQuery;
                        if (query.length >= 3) {
                            await this.searchPlace(type);
                        }
                    },

                    async searchPlace(type) {
                        const query = type === 'origin' ? this.originSearchQuery : this.destinationSearchQuery;
                        if (query.length < 2) return;

                        this.isLoading = true;
                        try {
                            const response = await fetch(`/api/map/search?q=${encodeURIComponent(query)}`);
                            const data = await response.json();
                            if (type === 'origin') {
                                this.originSearchResults = data;
                            } else {
                                this.destinationSearchResults = data;
                            }
                        } catch (err) {
                            console.error('Search error:', err);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    selectPlace(type, place) {
                        if (GOOGLE_MAPS_API_KEY) {
                            this.setMarkerGoogle(type, place.lat, place.lng, place.name);
                            this.googleMap.setView
                                ? this.googleMap.setCenter({ lat: parseFloat(place.lat), lng: parseFloat(place.lng) })
                                : this.googleMap.panTo({ lat: parseFloat(place.lat), lng: parseFloat(place.lng) });
                            this.googleMap.setZoom(13);
                        } else {
                            this.setMarkerLeaflet(type, place.lat, place.lng, place.name);
                            this.map.setView([parseFloat(place.lat), parseFloat(place.lng)], 13);
                        }

                        if (type === 'origin') {
                            this.originSearchResults = [];
                            this.originSearchQuery = '';
                        } else {
                            this.destinationSearchResults = [];
                            this.destinationSearchQuery = '';
                        }

                        this.fetchRoutes();
                    },

                    async fetchRoutes() {
                        if (!this.originLat || !this.destinationLat) return;

                        this.isLoading = true;
                        this.clearRouteLayers();
                        this.routes = [];
                        this.selectedRouteIndex = null;

                        try {
                            const response = await fetch(
                                `/api/map/route?origin_lat=${this.originLat}&origin_lng=${this.originLng}&dest_lat=${this.destinationLat}&dest_lng=${this.destinationLng}`
                            );
                            const data = await response.json();

                            if (data.routes && data.routes.length > 0) {
                                this.routes = data.routes;
                                this.recalculateFuel();
                                this.drawAllRoutes();
                                this.selectRoute(0);
                            } else {
                                alert('Tidak ada rute yang ditemukan untuk lokasi tersebut.');
                            }
                        } catch (err) {
                            console.error('Route fetch error:', err);
                            alert('Gagal mengambil rute dari server.');
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    recalculateFuel() {
                        const selectEl = document.getElementById('vehicle_id');
                        if (!selectEl) return;

                        const selectedOpt = selectEl.options[selectEl.selectedIndex];
                        if (!selectedOpt) return;

                        const consumption = parseFloat(selectedOpt.getAttribute('data-consumption')) || 10;
                        const price = parseFloat(selectedOpt.getAttribute('data-price')) || 10000;
                        const multiplier = this.isRoundTrip ? 2 : 1;

                        this.routes.forEach(route => {
                            const liters = (route.distance_km * multiplier) / consumption;
                            route.estimated_fuel_cost = Math.ceil((liters * price) / 100) * 100;
                        });
                    },

                    currentFuelCost() {
                        if (this.selectedRouteIndex === null || !this.routes[this.selectedRouteIndex]) return 0;
                        return this.routes[this.selectedRouteIndex].estimated_fuel_cost ?? 0;
                    },

                    clearRouteLayers() {
                        if (GOOGLE_MAPS_API_KEY) {
                            this.routeLayers.forEach(layer => {
                                if (layer && typeof layer.setMap === 'function') layer.setMap(null);
                            });
                        } else {
                            this.routeLayers.forEach(layer => {
                                if (this.map && layer) this.map.removeLayer(layer);
                            });
                        }
                        this.routeLayers = [];
                    },

                    routeColor(index) {
                        const colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444'];
                        return colors[index % colors.length];
                    },

                    drawAllRoutes() {
                        if (GOOGLE_MAPS_API_KEY) {
                            this.drawAllRoutesGoogle();
                        } else {
                            this.drawAllRoutesLeaflet();
                        }
                    },

                    drawAllRoutesGoogle() {
                        const bounds = new google.maps.LatLngBounds();

                        this.routes.forEach((route, index) => {
                            let geom;
                            try { geom = JSON.parse(route.geometry); } catch (e) { return; }
                            const path = geom.coordinates.map(coord => ({ lat: coord[1], lng: coord[0] }));

                            const polyline = new google.maps.Polyline({
                                path,
                                geodesic: true,
                                strokeColor: this.routeColor(index),
                                strokeOpacity: index === 0 ? 0.9 : 0.4,
                                strokeWeight: index === 0 ? 6 : 4,
                                map: this.googleMap,
                                clickable: true
                            });

                            polyline.addListener('click', () => this.selectRoute(index));
                            this.routeLayers.push(polyline);

                            path.forEach(p => bounds.extend(p));
                        });

                        if (!bounds.isEmpty()) {
                            this.googleMap.fitBounds(bounds, 50);
                        }
                    },

                    drawAllRoutesLeaflet() {
                        const boundsArr = [];

                        this.routes.forEach((route, index) => {
                            let geom;
                            try { geom = JSON.parse(route.geometry); } catch (e) { return; }
                            const latLngs = geom.coordinates.map(coord => [coord[1], coord[0]]);

                            const polyline = L.polyline(latLngs, {
                                color: this.routeColor(index),
                                weight: index === 0 ? 6 : 4,
                                opacity: index === 0 ? 0.8 : 0.4
                            }).addTo(this.map);

                            polyline.on('click', () => this.selectRoute(index));
                            this.routeLayers.push(polyline);
                            boundsArr.push(latLngs);
                        });

                        if (boundsArr.length > 0) {
                            this.map.fitBounds(L.latLngBounds(boundsArr.flat()));
                        }
                    },

                    selectRoute(index) {
                        this.selectedRouteIndex = index;

                        if (GOOGLE_MAPS_API_KEY) {
                            this.routeLayers.forEach((layer, idx) => {
                                if (layer && typeof layer.setOptions === 'function') {
                                    layer.setOptions({
                                        strokeWeight: idx === index ? 7 : 4,
                                        strokeOpacity: idx === index ? 1 : 0.3,
                                        zIndex: idx === index ? 10 : 1
                                    });
                                }
                            });
                        } else {
                            this.routeLayers.forEach((layer, idx) => {
                                if (layer) {
                                    layer.setStyle({ weight: idx === index ? 7 : 4, opacity: idx === index ? 0.9 : 0.3 });
                                    if (idx === index) layer.bringToFront();
                                }
                            });
                        }

                        this.routes.forEach((route, idx) => {
                            route.is_selected = (idx === index);
                        });
                    },

                    formatDuration(minutes) {
                        if (!minutes) return '-';
                        const hours = Math.floor(minutes / 60);
                        const mins = minutes % 60;
                        return hours > 0 ? `${hours}j ${mins}m` : `${mins}m`;
                    },

                    formatRupiah(amount) {
                        if (!amount) return '0';
                        return new Intl.NumberFormat('id-ID').format(Math.round(amount));
                    },

                    prepareSubmit(e) {
                        if (!this.originName || !this.originLat) {
                            e.preventDefault();
                            alert('Tentukan titik asal terlebih dahulu.');
                            return;
                        }
                        if (!this.destinationName || !this.destinationLat) {
                            e.preventDefault();
                            alert('Tentukan titik tujuan terlebih dahulu.');
                            return;
                        }
                        if (this.selectedRouteIndex === null) {
                            e.preventDefault();
                            alert('Pilih salah satu rute terlebih dahulu.');
                            return;
                        }
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>
