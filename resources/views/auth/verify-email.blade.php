<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800">Verifikasi Email</h1>
        <p class="text-sm text-slate-400 mt-2">Kami telah mengirimkan link verifikasi ke email yang Anda daftarkan. Silakan cek inbox Anda.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium text-center">
        Link verifikasi baru telah dikirim ke email Anda!
    </div>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-indigo-600/15">
                Kirim Ulang Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-slate-400 hover:text-slate-600 font-semibold">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>