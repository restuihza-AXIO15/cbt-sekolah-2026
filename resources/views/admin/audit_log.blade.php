@extends('layouts.admin')

@section('title', 'Audit & Log Keamanan Ujian - Admin CBT')

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Audit & Log Keamanan Ujian</h1>
            <nav class="flex text-xs text-slate-400 dark:text-slate-500 mt-1 font-medium space-x-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-sky-400">Home</a>
                <span>/</span>
                <span class="text-slate-500 dark:text-slate-400">Keamanan</span>
                <span>/</span>
                <span class="text-blue-600 dark:text-sky-400 font-semibold">Audit Log</span>
            </nav>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.monitoring') }}" 
               class="interactive-click px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Monitoring Live</span>
            </a>

            <button onclick="location.reload()" class="interactive-click px-3.5 py-2 bg-slate-700 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Segarkan</span>
            </button>
        </div>
    </div>

    <!-- Security KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pelanggaran -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700 shadow-sm flex items-center space-x-3.5 transition-colors duration-300">
            <div class="w-11 h-11 rounded-xl bg-rose-100 dark:bg-rose-950/70 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Pelanggaran Terdeteksi</span>
                <span class="text-2xl font-bold text-slate-800 dark:text-white block mt-0.5">{{ $violations->total() }} Insiden</span>
            </div>
        </div>

        <!-- Token Terbit -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700 shadow-sm flex items-center space-x-3.5 transition-colors duration-300">
            <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-key"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Riwayat Token</span>
                <span class="text-2xl font-bold text-slate-800 dark:text-white block mt-0.5">{{ $recentTokens->count() }} Kali Rilis</span>
            </div>
        </div>

        <!-- Mode Pengawasan -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700 shadow-sm flex items-center space-x-3.5 transition-colors duration-300">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Integritas CBT</span>
                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 block mt-0.5">Strict Mode Aktif</span>
            </div>
        </div>

        <!-- Proteksi Anti-Curang -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200/90 dark:border-slate-700 shadow-sm flex items-center space-x-3.5 transition-colors duration-300">
            <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-950/70 text-blue-600 dark:text-sky-400 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-fingerprint"></i>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Standar Asesmen</span>
                <span class="text-sm font-bold text-slate-800 dark:text-white block mt-0.5">ANBK & Pusmendik</span>
            </div>
        </div>
    </div>

    <!-- Table 1: Log Pelanggaran Integritas Peserta -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center space-x-2 text-slate-800 dark:text-white font-bold text-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                <span>Log Pelanggaran Integritas Peserta (Realtime)</span>
            </div>
            <div class="text-xs text-slate-400 dark:text-slate-500">
                Menampilkan {{ $violations->count() }} dari {{ $violations->total() }} data
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Waktu Kejadian</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Kelas & Ruang</th>
                        <th class="py-3 px-4">Mata Ujian</th>
                        <th class="py-3 px-4 text-center">Jenis Pelanggaran</th>
                        <th class="py-3 px-4">Keterangan / Detil</th>
                        <th class="py-3 px-4 text-center">Status Sesi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-200">
                    @forelse($violations as $idx => $vl)
                        @php
                            $user = $vl->user ?? $vl->attempt?->user;
                            $exam = $vl->attempt?->exam;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400">
                                {{ $violations->firstItem() + $idx }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">
                                {{ $vl->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $user->name ?? 'Siswa Tidak Dikenal' }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $user->username ?? '-' }} (NISN: {{ $user->nisn ?? '-' }})</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                <span class="font-medium block">{{ $user->class_name ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $user->room ?? 'Lab 1' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $exam->title ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $exam->subject->name ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if(Str::contains(strtolower($vl->violation_type), ['tab', 'blur', 'switch']))
                                    <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 rounded font-bold text-[10px]">
                                        <i class="fa-solid fa-arrow-up-right-from-square mr-1 text-[9px]"></i> Pindah Tab
                                    </span>
                                @elseif(Str::contains(strtolower($vl->violation_type), ['fullscreen', 'screen']))
                                    <span class="px-2.5 py-1 bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300 rounded font-bold text-[10px]">
                                        <i class="fa-solid fa-compress mr-1 text-[9px]"></i> Keluar Fullscreen
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-950/80 text-purple-800 dark:text-purple-300 rounded font-bold text-[10px]">
                                        {{ strtoupper($vl->violation_type) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-mono text-[11px]">
                                {{ $vl->details ?? 'Deteksi kehilangan fokus jendela ujian.' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($vl->attempt && $vl->attempt->isLocked())
                                    <span class="px-2 py-0.5 bg-rose-600 text-white rounded font-bold text-[10px]">
                                        TERKUNCI
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded font-medium text-[10px]">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                Tidak ada log pelanggaran kecurangan yang tercatat. Sistem ujian berjalan tertib.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $violations->links() }}
        </div>
    </div>

    <!-- Table 2: Jejak Audit Rilis Token Ujian -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center space-x-2 text-slate-800 dark:text-white font-bold text-sm">
                <i class="fa-solid fa-key text-amber-500"></i>
                <span>Jejak Audit Rilis Token Ujian (20 Aktivitas Terakhir)</span>
            </div>
            <a href="{{ route('admin.token') }}" class="text-xs text-blue-600 dark:text-sky-400 hover:underline font-semibold flex items-center space-x-1">
                <span>Kelola Token</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode Token</th>
                        <th class="py-3 px-4">Diterbitkan Oleh</th>
                        <th class="py-3 px-4">Sesi / Mata Ujian</th>
                        <th class="py-3 px-4">Waktu Diterbitkan</th>
                        <th class="py-3 px-4">Berlaku Sampai</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-200">
                    @forelse($recentTokens as $index => $tk)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-mono font-bold text-sm text-blue-600 dark:text-sky-400">
                                {{ $tk->token_code }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800 dark:text-slate-200">
                                {{ $tk->creator->name ?? 'Administrator' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                {{ $tk->exam->title ?? 'Sesi Ujian Umum' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-500">
                                {{ $tk->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-500">
                                {{ $tk->valid_until ? $tk->valid_until->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($tk->is_active && $tk->valid_until > now())
                                    <span class="px-2.5 py-1 bg-emerald-600 text-white rounded font-bold text-[10px] shadow-sm animate-pulse">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded font-medium text-[10px]">
                                        KEDALUWARSA
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400 dark:text-slate-500">
                                Belum ada riwayat token ujian yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
