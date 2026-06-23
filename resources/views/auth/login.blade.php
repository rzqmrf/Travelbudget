<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-800">Selamat Datang Kembali</h1>
        <p class="text-sm text-slate-400 mt-1">Masuk ke akun TravelBudget Anda</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4" name="remember">
                <span class="ms-2 text-sm text-slate-500">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
            <a class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold" href="{{ route('password.request') }}">
                Lupa password?
            </a>
            @endif
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-xl shadow-indigo-600/15 hover:shadow-2xl hover:shadow-indigo-600/20">
            Masuk
        </button>

        <div class="text-center pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-400">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-bold">Daftar Sekarang</a>
            </p>
        </div>
    </form>
</x-guest-layout>