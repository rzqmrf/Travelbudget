<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-800">Lupa Password?</h1>
        <p class="text-sm text-slate-400 mt-1">Masukkan email Anda dan kami akan mengirimkan link reset password.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-xl shadow-indigo-600/15">
            Kirim Link Reset
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-bold">&larr; Kembali ke Login</a>
        </div>
    </form>
</x-guest-layout>