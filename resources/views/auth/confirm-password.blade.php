<x-guest-layout>
    <div class="mb-8">
        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800 text-center">Konfirmasi Password</h1>
        <p class="text-sm text-slate-400 mt-2 text-center">Area ini aman. Harap konfirmasi password Anda sebelum melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="Masukkan password Anda" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-xl shadow-indigo-600/15">
            Konfirmasi
        </button>
    </form>
</x-guest-layout>