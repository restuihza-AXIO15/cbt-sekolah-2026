@extends('layouts.admin')

@section('title', 'Rilis Token Ujian (10 Menit) - Admin CBT')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Rilis Token Ujian</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pengelolaan token ujian sekolah dengan batas kedaluwarsa ketat 10 menit.
            </p>
        </div>

        <form action="{{ route('admin.token.generate') }}" method="POST">
            @csrf
            <button type="submit" 
                class="interactive-click px-4 py-2.5 bg-[#1e73be] hover:bg-[#155d9b] text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Rilis Token Baru (10 Menit)</span>
            </button>
        </form>
    </div>

    <!-- Active Token Display Card (Sesuai Gambar 3 Left Box) -->
    <div class="bg-gradient-to-br from-white to-blue-50/50 dark:from-slate-800 dark:to-slate-850 rounded-2xl border border-blue-200/80 dark:border-slate-700 shadow-md p-6 sm:p-8 transition-colors duration-300">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center md:text-left">
                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-950/70 text-[#1e73be] dark:text-sky-300 text-xs font-bold rounded-full uppercase tracking-wider">
                    TOKEN UJIAN AKTIF SAAT INI
                </span>
                <div class="flex items-center justify-center md:justify-start space-x-3 mt-2">
                    <span class="text-4xl sm:text-5xl font-mono font-black tracking-widest text-slate-900 dark:text-white bg-white dark:bg-slate-900 px-6 py-2 rounded-xl border-2 border-[#1e73be] dark:border-sky-500 shadow-inner">
                        {{ $activeToken ? $activeToken->token_code : '------' }}
                    </span>
                    @if($activeToken)
                        <button type="button" onclick="copyToken('{{ $activeToken->token_code }}')" 
                            class="interactive-click px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-semibold border border-slate-300 dark:border-slate-600 transition" title="Salin Token">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Siswa memasukkan 6 karakter token ini pada halaman Konfirmasi Data Peserta.
                </p>
            </div>

            <!-- Countdown Timer & Actions -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-700 text-center shadow-sm w-full md:w-72 space-y-3 transition-colors duration-300">
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide block">Sisa Masa Berlaku Token</span>
                    <span id="adminTokenTimer" class="font-mono text-2xl font-black text-blue-700 dark:text-sky-400 block mt-1">
                        @if($activeToken && !$activeToken->isExpired())
                            --:--
                        @else
                            <span class="text-rose-600 text-lg">EXPIRED</span>
                        @endif
                    </span>
                </div>

                @if($activeToken && !$activeToken->isExpired())
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                        Kedaluwarsa pada: <br><strong>{{ $activeToken->valid_until->format('H:i:s') }} WIB</strong>
                    </p>
                    <form action="{{ route('admin.token.expire', $activeToken->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="interactive-click w-full py-1.5 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-400 font-semibold text-xs rounded border border-rose-200 dark:border-rose-900 transition">
                            Hentikan Token Ini
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.token.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="interactive-click w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded shadow transition">
                            Buat Token Sekarang
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- History Token Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Riwayat Token yang Diterbitkan</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode Token</th>
                        <th class="py-3 px-4">Mata Ujian Terkait</th>
                        <th class="py-3 px-4">Waktu Diterbitkan</th>
                        <th class="py-3 px-4">Batas Berlaku (10m)</th>
                        <th class="py-3 px-4">Dikeluarkan Oleh</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-200">
                    @forelse($tokenHistory as $index => $tok)
                        @php
                            $isStillValid = !$tok->isExpired();
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                            <td class="py-3 px-4 text-center font-medium">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-sm text-slate-900 dark:text-white tracking-wider">
                                {{ $tok->token_code }}
                            </td>
                            <td class="py-3 px-4 font-medium">
                                {{ $tok->exam ? $tok->exam->title : 'Semua Ujian Aktif' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-400">
                                {{ $tok->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-400">
                                {{ $tok->valid_until->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                {{ $tok->creator ? $tok->creator->name : 'Admin' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($isStillValid)
                                    <span class="px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 rounded-full font-bold text-[10px]">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-full font-bold text-[10px]">
                                        KEDALUWARSA
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400 dark:text-slate-500">Belum ada riwayat token.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function copyToken(text) {
        navigator.clipboard.writeText(text);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Token ${text} disalin!`,
            showConfirmButton: false,
            timer: 2000
        });
    }

    @if($activeToken && !$activeToken->isExpired())
        let tokenRemaining = {{ $activeToken->remainingSeconds() }};
        const timerEl = document.getElementById('adminTokenTimer');

        function updateAdminTokenTimer() {
            if (tokenRemaining <= 0) {
                timerEl.innerHTML = '<span class="text-rose-600 text-lg">EXPIRED</span>';
                return;
            }
            const mins = Math.floor(tokenRemaining / 60);
            const secs = tokenRemaining % 60;
            timerEl.innerText = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            tokenRemaining--;
        }
        updateAdminTokenTimer();
        setInterval(updateAdminTokenTimer, 1000);
    @endif
</script>
@endpush
@endsection
