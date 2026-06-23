<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-extrabold text-xl text-slate-800">Profil Saya</h2>
            <p class="text-xs text-slate-400 mt-0.5">Kelola informasi akun dan preferensi Anda</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Account Stats Card -->
            <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-800 rounded-3xl p-6 text-white shadow-2xl shadow-indigo-600/20 relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-5 translate-x-16 -translate-y-16 select-none pointer-events-none">
                    <svg class="w-[300px] h-[300px]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
                <div class="relative z-10 flex items-center gap-5">
                    <div class="w-16 h-16 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center text-2xl font-extrabold border border-white/10">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-extrabold">{{ auth()->user()->name }}</h3>
                        <p class="text-indigo-200/70 text-sm">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="hidden sm:grid grid-cols-3 gap-6 text-center">
                        <div>
                            <span class="text-2xl font-extrabold block">{{ auth()->user()->trips()->count() }}</span>
                            <span class="text-[10px] text-indigo-200/60">Total Trip</span>
                        </div>
                        <div>
                            <span class="text-2xl font-extrabold block">{{ auth()->user()->vehicles()->count() }}</span>
                            <span class="text-[10px] text-indigo-200/60">Kendaraan</span>
                        </div>
                        <div>
                            <span class="text-2xl font-extrabold block">{{ auth()->user()->trips()->completed()->count() }}</span>
                            <span class="text-[10px] text-indigo-200/60">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Profile Information -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-1">Informasi Profil</h3>
                <p class="text-xs text-slate-400 mb-6">Perbarui nama dan alamat email akun Anda</p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4 max-w-lg">
                    @csrf @method('PATCH')

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Nama</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white">
                        @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/10">Simpan Perubahan</button>
                </form>
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-1">Ubah Password</h3>
                <p class="text-xs text-slate-400 mb-6">Pastikan akun Anda menggunakan password yang panjang dan aman</p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4 max-w-lg">
                    @csrf @method('PUT')

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Password Saat Ini</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white">
                        @error('current_password', 'updatePassword') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Password Baru</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white">
                        @error('password', 'updatePassword') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition bg-slate-50/50 hover:bg-white">
                        @error('password_confirmation', 'updatePassword') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/10">Update Password</button>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-6">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h3 class="text-sm font-bold text-rose-600">Zona Berbahaya</h3>
                </div>
                <p class="text-xs text-slate-400 mb-6">Hapus akun secara permanen. Tindakan ini tidak dapat dibatalkan.</p>

                <div x-data="{ confirmDelete: false }">
                    <button @click="confirmDelete = true" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl border border-rose-200 transition">
                        Hapus Akun Saya
                    </button>

                    <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" @click.self="confirmDelete = false">
                        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl" x-transition>
                            <h4 class="text-lg font-bold text-slate-800 mb-2">Hapus Akun Permanen?</h4>
                            <p class="text-sm text-slate-500 mb-6">Semua data perjalanan, kendaraan, dan pengeluaran Anda akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                            <form method="POST" action="{{ route('profile.destroy') }}">
                                @csrf @method('DELETE')
                                <div class="mb-4">
                                    <label class="text-[11px] font-semibold text-slate-500 block mb-1">Konfirmasi Password</label>
                                    <input type="password" name="password" placeholder="Masukkan password untuk konfirmasi"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                                    @error('password', 'deleteUser') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click="confirmDelete = false" class="flex-1 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition">Batal</button>
                                    <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition">Ya, Hapus Akun</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>