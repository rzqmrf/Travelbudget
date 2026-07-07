<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('trips.show', $trip) }}" class="text-slate-400 hover:text-slate-600 transition">
                &larr; Batal
            </a>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Edit Perjalanan: {{ $trip->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10" x-data="tripEditor()">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <form method="POST" action="{{ route('trips.update', $trip) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Trip Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Perjalanan')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $trip->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Vehicle Selector -->
                    <div>
                        <x-input-label for="vehicle_id" :value="__('Kendaraan')" />
                        <select id="vehicle_id" name="vehicle_id" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $trip->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->type->icon() }} {{ $vehicle->name }} ({{ $vehicle->fuel_type }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                    </div>

                    <!-- Budget Amount -->
                    <div>
                        <x-input-label for="budget_amount" :value="__('Anggaran Perjalanan (Rupiah)')" />
                        <x-text-input id="budget_amount" class="block mt-1 w-full" type="number" name="budget_amount" :value="old('budget_amount', intval($trip->budget_amount))" required />
                        <x-input-error :messages="$errors->get('budget_amount')" class="mt-2" />
                    </div>

                    <!-- Daily Budget Limit -->
                    <div>
                        <x-input-label for="daily_budget_limit" value="Batas Budget Harian (Rupiah, Opsional)" />
                        <x-text-input id="daily_budget_limit" class="block mt-1 w-full" type="number" name="daily_budget_limit" :value="old('daily_budget_limit', $trip->daily_budget_limit ? intval($trip->daily_budget_limit) : '')" placeholder="Biarkan kosong jika tidak ada limit" />
                        <p class="text-[10px] text-slate-400 mt-1">Sistem akan memberi peringatan jika pengeluaran harian melebihi batas ini.</p>
                        <x-input-error :messages="$errors->get('daily_budget_limit')" class="mt-2" />
                    </div>

                    <!-- Round Trip Toggle -->
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <label for="is_round_trip" class="flex items-center gap-3 cursor-pointer flex-1">
                            <div class="relative">
                                <input type="checkbox" id="is_round_trip" name="is_round_trip"
                                    x-model="isRoundTrip"
                                    value="1" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-checked:bg-indigo-600 rounded-full transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-slate-800">Pulang-Pergi (Round Trip)</span>
                                <p class="text-[10px] text-slate-400">Jarak &amp; biaya bensin dikalikan 2 saat kalkulasi rute</p>
                            </div>
                        </label>
                    </div>

                    <!-- Return Date Picker -->
                    <div x-show="isRoundTrip" x-transition x-cloak class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50 space-y-2">
                        <x-input-label for="return_date" value="Tanggal Kepulangan" />
                        <input type="date" id="return_date" name="return_date"
                            value="{{ old('return_date', $trip->return_date ? $trip->return_date->format('Y-m-d') : '') }}"
                            class="block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm" />
                        <x-input-error :messages="$errors->get('return_date')" class="mt-2" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" :value="__('Catatan Perjalanan')" />
                        <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm" placeholder="Catatan akomodasi, destinasi kuliner, dll.">{{ old('notes', $trip->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <!-- Hidden read-only inputs for geographic location consistency -->
                    <input type="hidden" name="origin_name" value="{{ $trip->origin_name }}">
                    <input type="hidden" name="origin_lat" value="{{ $trip->origin_lat }}">
                    <input type="hidden" name="origin_lng" value="{{ $trip->origin_lng }}">
                    <input type="hidden" name="destination_name" value="{{ $trip->destination_name }}">
                    <input type="hidden" name="destination_lat" value="{{ $trip->destination_lat }}">
                    <input type="hidden" name="destination_lng" value="{{ $trip->destination_lng }}">

                    <div class="flex items-center justify-end border-t border-slate-50 pt-6">
                        <x-primary-button>
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function tripEditor() {
            return {
                isRoundTrip: {{ $trip->is_round_trip ? 'true' : 'false' }},
            };
        }
    </script>
    @endpush
</x-app-layout>
