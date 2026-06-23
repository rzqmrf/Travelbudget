<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vehicles.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                &larr; Kembali
            </a>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                Edit Kendaraan: {{ $vehicle->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Kendaraan / Plat')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $vehicle->name)" required autofocus placeholder="Contoh: Honda Vario, Toyota Avanza" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Type -->
                    <div>
                        <x-input-label for="type" :value="__('Jenis Kendaraan')" />
                        <select id="type" name="type" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                            <option value="motor" {{ old('type', $vehicle->type->value) == 'motor' ? 'selected' : '' }}>🏍️ Motor</option>
                            <option value="mobil" {{ old('type', $vehicle->type->value) == 'mobil' ? 'selected' : '' }}>🚗 Mobil</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <!-- Fuel Consumption -->
                    <div>
                        <x-input-label for="fuel_consumption" :value="__('Konsumsi BBM (KM per Liter)')" />
                        <x-text-input id="fuel_consumption" class="block mt-1 w-full" type="number" step="0.1" name="fuel_consumption" :value="old('fuel_consumption', $vehicle->fuel_consumption)" required placeholder="Contoh: 45.5, 12.0" />
                        <x-input-error :messages="$errors->get('fuel_consumption')" class="mt-2" />
                    </div>

                    <!-- Fuel Type -->
                    <div>
                        <x-input-label for="fuel_type" :value="__('Tipe Bahan Bakar')" />
                        <x-text-input id="fuel_type" class="block mt-1 w-full" type="text" name="fuel_type" :value="old('fuel_type', $vehicle->fuel_type)" required placeholder="Contoh: Pertalite, Pertamax, Solar" />
                        <x-input-error :messages="$errors->get('fuel_type')" class="mt-2" />
                    </div>

                    <!-- Fuel Price -->
                    <div>
                        <x-input-label for="fuel_price" :value="__('Harga BBM (Rupiah per Liter)')" />
                        <x-text-input id="fuel_price" class="block mt-1 w-full" type="number" name="fuel_price" :value="old('fuel_price', $vehicle->fuel_price)" required placeholder="Contoh: 10000, 12950" />
                        <x-input-error :messages="$errors->get('fuel_price')" class="mt-2" />
                    </div>

                    <!-- Set Default Checkbox -->
                    <div class="block">
                        <label for="is_default" class="inline-flex items-center">
                            <input id="is_default" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_default" value="1" {{ old('is_default', $vehicle->is_default) ? 'checked' : '' }} {{ $vehicle->is_default ? 'disabled' : '' }}>
                            <span class="ms-2 text-sm text-slate-600">{{ __('Jadikan kendaraan utama (default)') }}</span>
                        </label>
                        @if($vehicle->is_default)
                            <p class="text-xs text-indigo-500 mt-1">Ini adalah kendaraan utama Anda. Untuk mengubahnya, set kendaraan lain sebagai default.</p>
                        @endif
                        <x-input-error :messages="$errors->get('is_default')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end border-t border-slate-50 pt-6">
                        <x-primary-button class="ms-4">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
