<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-800">Buat Akun Baru</h1>
        <p class="text-sm text-slate-400 mt-1">Mulai kelola budget perjalananmu</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="Nama lengkap Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white placeholder:text-slate-300"
                placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-xl shadow-indigo-600/15 hover:shadow-2xl hover:shadow-indigo-600/20">
            Daftar Sekarang
        </button>

        <div class="text-center pt-4 border-t border-slate-100">
            <p class="text-sm text-slate-400">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-bold">Masuk di sini</a>
            </p>
        </div>
    </form>
</x-guest-layout>