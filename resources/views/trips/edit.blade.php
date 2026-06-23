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

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                <form method="POST" action="{{ route('trips.update', $trip) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Perjalanan')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $trip->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Vehicle (readonly helper in edit for budget consistency, or can select another one) -->
                    <div>
                        <x-input-label for="vehicle_id" :value="__('Kendaraan')" />
                        <select id="vehicle_id" name="vehicle_id" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $trip->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->type->icon() }} {{ $vehicle->name }}
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

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" :value="__('Catatan Perjalanan')" />
                        <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">{{ old('notes', $trip->notes) }}</textarea>
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
                        <x-primary-button class="ms-4">
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
