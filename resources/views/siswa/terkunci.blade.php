@extends('layouts.pusmendik')

@section('title', 'Ujian Terkunci - PUSMENDIK')

@section('content')
<div class="w-full max-w-lg bg-white dark:bg-slate-900/95 rounded-2xl shadow-xl border border-red-200 dark:border-red-900/60 p-8 my-auto text-center transform transition-all">
    <!-- Lock Icon -->
    <div class="w-20 h-20 bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-5 text-3xl shadow-sm">
        <i class="fa-solid fa-lock"></i>
    </div>

    <h2 class="text-xl font-bold text-red-700 dark:text-red-400 tracking-tight">Ujian Anda Terkunci!</h2>
    <p class="text-xs text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
        Sistem mendeteksi aktivitas mencurigakan yang melanggar tata tertib ujian (seperti berpindah tab/jendela, tangkapan layar, atau membuka aplikasi lain) melebihi batas toleransi.
    </p>

    <div class="mt-6 bg-red-50 dark:bg-red-950/30 rounded-xl p-5 border border-red-200 dark:border-red-900/60 text-left text-xs space-y-2">
        <div class="flex justify-between border-b border-red-200 dark:border-red-900/50 pb-2">
            <span class="text-slate-500 dark:text-slate-400">Nama Siswa:</span>
            <span class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</span>
        </div>
        <div class="flex justify-between border-b border-red-200 dark:border-red-900/50 pb-2">
            <span class="text-slate-500 dark:text-slate-400">Jumlah Pelanggaran:</span>
            <span class="font-bold text-red-600 dark:text-red-400">{{ $attempt ? $attempt->violations_count : 3 }} Kali (Batas Maksimal)</span>
        </div>
        <div class="flex justify-between pt-1">
            <span class="text-slate-500 dark:text-slate-400">Solusi:</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200">Hubungi Pengawas / Proktor Ruang Ujian</span>
        </div>
    </div>

    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-4 italic">
        *Pengawas dapat membuka kunci ujian Anda melalui menu Manajemen Pengawasan di Dashboard Admin.
    </p>

    <div class="mt-6">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="interactive-click px-5 py-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg transition">
                Logout
            </button>
        </form>
    </div>
</div>
@endsection
