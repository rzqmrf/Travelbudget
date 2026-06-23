<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            #map {
                height: 500px;
                border-radius: 1.5rem;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                border: 1px solid rgb(241 245 249);
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

    <div class="py-10" x-data="tripPlanner()">
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
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-50 pb-3">Informasi Trip & Budget</h3>

                            <!-- Trip Name -->
                            <div>
                                <x-input-label for="name" value="Nama Perjalanan" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required placeholder="Contoh: Mudik Lebaran, Roadtrip Bali" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Vehicle Selector -->
                            <div>
                                <x-input-label for="vehicle_id" value="Kendaraan yang Digunakan" />
                                <select id="vehicle_id" name="vehicle_id" x-model="selectedVehicleId" @change="recalculateFuel()" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
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
                                <x-text-input id="budget_amount" class="block mt-1 w-full" type="number" name="budget_amount" required placeholder="Contoh: 1000000" />
                                <x-input-error :messages="$errors->get('budget_amount')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" value="Catatan Tambahan (Opsional)" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Catatan tempat istirahat, pengeluaran darurat, dll."></textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Search & Route Plan -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-50 pb-3 font-semibold">Tentukan Rute</h3>

                            <!-- Origin Search -->
                            <div class="relative">
                                <x-input-label for="origin_search" value="Titik Awal (Origin)" />
                                <div class="flex gap-2 mt-1">
                                    <x-text-input id="origin_search" class="flex-1" type="text" x-model="originSearchQuery" @keydown.enter.prevent="searchPlace('origin')" placeholder="Cari lokasi awal..." />
                                    <button type="button" @click="searchPlace('origin')" class="px-4 py-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl text-sm font-semibold text-slate-700 transition">
                                        Cari
                                    </button>
                                </div>
                                <div x-show="originSearchResults.length > 0" class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-100 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="place in originSearchResults" :key="place.name">
                                        <button type="button" @click="selectPlace('origin', place)" class="w-full text-left px-4 py-3 hover:bg-slate-50 text-xs border-b border-slate-50 last:border-b-0 transition truncate">
                                            <span class="font-semibold block text-slate-700" x-text="place.name"></span>
                                        </button>
                                    </template>
                                </div>
                                <div class="mt-2 text-xs text-indigo-600 font-semibold" x-show="originName">
                                    Terpilih: <span class="text-slate-700 font-medium" x-text="originName"></span>
                                </div>
                            </div>

                            <!-- Destination Search -->
                            <div class="relative">
                                <x-input-label for="destination_search" value="Titik Tujuan (Destination)" />
                                <div class="flex gap-2 mt-1">
                                    <x-text-input id="destination_search" class="flex-1" type="text" x-model="destinationSearchQuery" @keydown.enter.prevent="searchPlace('destination')" placeholder="Cari lokasi tujuan..." />
                                    <button type="button" @click="searchPlace('destination')" class="px-4 py-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl text-sm font-semibold text-slate-700 transition">
                                        Cari
                                    </button>
                                </div>
                                <div x-show="destinationSearchResults.length > 0" class="absolute left-0 right-0 z-50 mt-1 bg-white border border-slate-100 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="place in destinationSearchResults" :key="place.name">
                                        <button type="button" @click="selectPlace('destination', place)" class="w-full text-left px-4 py-3 hover:bg-slate-50 text-xs border-b border-slate-50 last:border-b-0 transition truncate">
                                            <span class="font-semibold block text-slate-700" x-text="place.name"></span>
                                        </button>
                                    </template>
                                </div>
                                <div class="mt-2 text-xs text-indigo-600 font-semibold" x-show="destinationName">
                                    Terpilih: <span class="text-slate-700 font-medium" x-text="destinationName"></span>
                                </div>
                            </div>

                            <!-- Search Progress Loader -->
                            <div class="flex items-center justify-center py-2" x-show="isLoading">
                                <span class="animate-spin mr-2">🔄</span>
                                <span class="text-xs text-slate-500">Memuat rute terbaik...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Map & Route Comparison -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Leaflet Map Card -->
                        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
                            <div id="map"></div>
                            <div class="mt-2 text-center text-xs text-slate-400">
                                Klik kanan pada peta untuk menetapkan Titik Awal, atau Klik kiri untuk Titik Tujuan
                            </div>
                        </div>

                        <!-- Route Comparison Options -->
                        <div x-show="routes.length > 0" class="space-y-4">
                            <h4 class="text-lg font-bold text-slate-800">Pilih Opsi Rute Terbaik</h4>
                            <div class="grid grid-cols-1 gap-4">
                                <template x-for="(route, index) in routes" :key="index">
                                    <div @click="selectRoute(index)" 
                                         :class="selectedRouteIndex === index ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-100 hover:border-slate-200 bg-white'"
                                         class="p-5 rounded-2xl border shadow-sm cursor-pointer transition flex items-center justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: routeColor(index) }"></span>
                                                <span class="font-bold text-slate-800" x-text="route.route_name"></span>
                                                <span class="text-xs text-slate-400" x-text="route.summary ? '(' + route.summary + ')' : ''"></span>
                                            </div>
                                            <div class="flex gap-4 mt-2 text-xs text-slate-500">
                                                <span>📏 <strong class="text-slate-700" x-text="route.distance_km + ' km'"></strong></span>
                                                <span>⏱️ <strong class="text-slate-700" x-text="formatDuration(route.duration_minutes)"></strong></span>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <span class="text-xs text-slate-400 block">Estimasi Bensin</span>
                                            <span class="text-base font-extrabold text-indigo-600" x-text="'Rp ' + formatRupiah(route.estimated_fuel_cost)"></span>
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
                        <div class="flex justify-end bg-white p-6 rounded-2xl border border-slate-100 shadow-sm" x-show="selectedRouteIndex !== null">
                            <x-primary-button>
                                Simpan Rencana Perjalanan
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            function tripPlanner() {
                return {
                    // Search states
                    originSearchQuery: '',
                    destinationSearchQuery: '',
                    originSearchResults: [],
                    destinationSearchResults: [],

                    // Location states
                    originName: '',
                    originLat: null,
                    originLng: null,
                    destinationName: '',
                    destinationLat: null,
                    destinationLng: null,

                    // Vehicles/Rates
                    selectedVehicleId: '{{ $vehicles->first()?->id }}',
                    
                    // Maps & Routes states
                    map: null,
                    originMarker: null,
                    destinationMarker: null,
                    routeLayers: [],
                    routes: [],
                    selectedRouteIndex: null,
                    isLoading: false,

                    init() {
                        this.$nextTick(() => {
                            this.initMap();
                        });
                    },

                    initMap() {
                        // Default to Indonesia center coordinates
                        this.map = L.map('map').setView([-2.5489, 118.0149], 5);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.map);

                        // Try to geolocate user to set initial view
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((position) => {
                                this.map.setView([position.coords.latitude, position.coords.longitude], 13);
                            });
                        }

                        // Right-click: Set origin
                        this.map.on('contextmenu', (e) => {
                            this.setMarker('origin', e.latlng.lat, e.latlng.lng, `Koordinat: ${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`);
                            this.fetchRoutes();
                        });

                        // Left-click: Set destination
                        this.map.on('click', (e) => {
                            // If origin is not set, set origin first. Otherwise set destination
                            if (!this.originLat) {
                                this.setMarker('origin', e.latlng.lat, e.latlng.lng, `Koordinat: ${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`);
                            } else {
                                this.setMarker('destination', e.latlng.lat, e.latlng.lng, `Koordinat: ${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`);
                                this.fetchRoutes();
                            }
                        });
                    },

                    setMarker(type, lat, lng, name) {
                        if (type === 'origin') {
                            this.originLat = lat;
                            this.originLng = lng;
                            this.originName = name;

                            if (this.originMarker) {
                                this.originMarker.setLatLng([lat, lng]);
                            } else {
                                this.originMarker = L.marker([lat, lng], { draggable: true })
                                    .addTo(this.map)
                                    .bindPopup('Titik Awal (Origin)')
                                    .openPopup();
                                
                                this.originMarker.on('dragend', (e) => {
                                    const pos = e.target.getLatLng();
                                    this.originLat = pos.lat;
                                    this.originLng = pos.lng;
                                    this.originName = `Koordinat: ${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}`;
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
                                    .bindPopup('Titik Tujuan (Destination)')
                                    .openPopup();
                                
                                this.destinationMarker.on('dragend', (e) => {
                                    const pos = e.target.getLatLng();
                                    this.destinationLat = pos.lat;
                                    this.destinationLng = pos.lng;
                                    this.destinationName = `Koordinat: ${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}`;
                                    this.fetchRoutes();
                                });
                            }
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
                            console.error(err);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    selectPlace(type, place) {
                        this.setMarker(type, place.lat, place.lng, place.name);
                        this.map.setView([place.lat, place.lng], 13);
                        
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
                            const response = await fetch(`/api/map/route?origin_lat=${this.originLat}&origin_lng=${this.originLng}&dest_lat=${this.destinationLat}&dest_lng=${this.destinationLng}`);
                            const data = await response.json();

                            if (data.routes && data.routes.length > 0) {
                                this.routes = data.routes;
                                this.recalculateFuel();
                                this.drawAllRoutes();
                                this.selectRoute(0);
                            } else {
                                alert('Tidak ada rute mengemudi yang ditemukan untuk lokasi tersebut.');
                            }
                        } catch (err) {
                            console.error(err);
                            alert('Gagal mengambil rute dari server OSRM.');
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

                        this.routes.forEach(route => {
                            const liters = route.distance_km / consumption;
                            // Round fuel cost up to the nearest 100 Rupiah
                            route.estimated_fuel_cost = Math.ceil((liters * price) / 100) * 100;
                        });
                    },

                    clearRouteLayers() {
                        this.routeLayers.forEach(layer => this.map.removeLayer(layer));
                        this.routeLayers = [];
                    },

                    routeColor(index) {
                        const colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444'];
                        return colors[index % colors.length];
                    },

                    drawAllRoutes() {
                        const bounds = [];
                        
                        this.routes.forEach((route, index) => {
                            const geom = JSON.parse(route.geometry);
                            // GeoJSON geometry coords are [lng, lat], Leaflet wants [lat, lng]
                            const latLngs = geom.coordinates.map(coord => [coord[1], coord[0]]);
                            
                            const polyline = L.polyline(latLngs, {
                                color: this.routeColor(index),
                                weight: index === 0 ? 6 : 4,
                                opacity: index === 0 ? 0.8 : 0.4
                            }).addTo(this.map);

                            polyline.on('click', () => {
                                this.selectRoute(index);
                            });

                            this.routeLayers.push(polyline);
                            bounds.push(latLngs);
                        });

                        if (bounds.length > 0) {
                            this.map.fitBounds(L.latLngBounds(bounds.flat()));
                        }
                    },

                    selectRoute(index) {
                        this.selectedRouteIndex = index;
                        
                        // Highlight selected polyline
                        this.routeLayers.forEach((layer, idx) => {
                            if (idx === index) {
                                layer.setStyle({ weight: 7, opacity: 0.9 });
                                layer.bringToFront();
                            } else {
                                layer.setStyle({ weight: 4, opacity: 0.3 });
                            }
                        });

                        // Set is_selected in the routes object
                        this.routes.forEach((route, idx) => {
                            route.is_selected = (idx === index);
                        });
                    },

                    formatDuration(minutes) {
                        const hours = Math.floor(minutes / 60);
                        const mins = minutes % 60;
                        return hours > 0 ? `${hours}j ${mins}m` : `${mins}m`;
                    },

                    formatRupiah(amount) {
                        return new Intl.NumberFormat('id-ID').format(amount);
                    },

                    prepareSubmit(e) {
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
