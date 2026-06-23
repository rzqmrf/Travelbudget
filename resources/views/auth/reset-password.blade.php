<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-800">Reset Password</h1>
        <p class="text-sm text-slate-400 mt-1">Buat password baru untuk akun Anda</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Password Baru</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="Ulangi password baru" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-xl shadow-indigo-600/15">
            Reset Password
        </button>
    </form>
</x-guest-layout>