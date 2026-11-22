@extends('layouts.pusmendik')

@section('title', 'Ujian Selesai - PUSMENDIK')

@section('content')
<div class="w-full max-w-lg bg-white dark:bg-slate-900/95 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 p-8 my-auto text-center transform transition-all">
    <!-- Checkmark Icon -->
    <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-5 text-3xl shadow-sm">
        <i class="fa-solid fa-check"></i>
    </div>

    <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">Terima Kasih, Ujian Telah Selesai!</h2>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
        Seluruh jawaban dan rekaman sesi ujian Anda telah berhasil diunggah dan disimpan secara aman di server CBT.
    </p>

    @if($attempt)
        <div class="mt-6 bg-slate-50 dark:bg-slate-800/70 rounded-xl p-5 border border-slate-200 dark:border-slate-700 text-left text-xs space-y-2.5">
            <div class="flex justify-between border-b border-slate-200 dark:border-slate-700 pb-2">
                <span class="text-slate-500 dark:text-slate-400">Mata Pelajaran:</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $attempt->exam->title ?? '-' }}</span>
            </div>
            <div class="flex justify-between border-b border-slate-200 dark:border-slate-700 pb-2">
                <span class="text-slate-500 dark:text-slate-400">Nama Siswa:</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $user->name }} ({{ $user->username }})</span>
            </div>
            <div class="flex justify-between border-b border-slate-200 dark:border-slate-700 pb-2">
                <span class="text-slate-500 dark:text-slate-400">Waktu Selesai:</span>
                <span class="font-mono text-slate-700 dark:text-slate-300">{{ $attempt->finished_at ? $attempt->finished_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between border-b border-slate-200 dark:border-slate-700 pb-2">
                <span class="text-slate-500 dark:text-slate-400">Durasi Pengerjaan:</span>
                <span class="font-semibold text-blue-600 dark:text-sky-400">{{ $attempt->durationFormatted() }}</span>
            </div>
            <div class="flex justify-between pt-1">
                <span class="text-slate-500 dark:text-slate-400">Status Validasi Server:</span>
                <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 rounded font-semibold text-[11px]">
                    <i class="fa-solid fa-shield-halved text-[10px] mr-1"></i> Terverifikasi Sah
                </span>
            </div>
        </div>
    @endif

    <div class="mt-8 flex items-center justify-center space-x-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" 
                class="interactive-click px-6 py-2.5 bg-[#1e73be] hover:bg-[#155d9b] text-white text-xs font-semibold rounded-lg shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Keluar dari Aplikasi</span>
            </button>
        </form>
    </div>
</div>
@endsection
