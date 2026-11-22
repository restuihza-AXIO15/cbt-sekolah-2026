@extends('layouts.guru')

@section('title', 'Beranda Guru - Portal CBT 2026')

@section('content')
<div class="space-y-6">

    <!-- Welcome Banner Guru -->
    <div class="bg-gradient-to-r from-sky-700 via-sky-600 to-blue-700 rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="px-2.5 py-0.5 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-semibold uppercase tracking-wider">
                Portal Pendidik & Pembuat Soal
            </span>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Selamat Datang, {{ $user->name }}</h1>
            <p class="text-xs text-sky-100 max-w-xl">
                Mata Pelajaran Ampuan: <strong class="text-white">{{ $subject ? $subject->name . ' (' . $subject->code . ')' : 'Belum Ditugaskan' }}</strong>. Anda dapat mengelola bank soal dan mengambil rekapitulasi nilai setelah ujian diselesaikan oleh Admin.
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 text-center shrink-0 w-full sm:w-auto">
            <p class="text-[11px] text-sky-100 font-medium">Bank Soal Anda:</p>
            <div class="text-3xl font-bold text-white my-1">
                {{ $totalSoal }} <span class="text-xs font-normal text-sky-200">Butir</span>
            </div>
            <a href="{{ route('guru.bank-soal') }}" class="inline-block mt-1 px-3 py-1 bg-white hover:bg-sky-50 text-sky-800 font-bold text-xs rounded-lg shadow transition">
                + Tambah Soal
            </a>
        </div>
    </div>

    <!-- Kebijakan Akses Guru -->
    <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-xl p-4 text-xs text-blue-900 dark:text-blue-300 flex items-start space-x-3 transition-colors duration-300">
        <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400 text-base mt-0.5"></i>
        <div>
            <h4 class="font-bold">Ketentuan Hak Akses Akun Guru:</h4>
            <p class="text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">
                1. Guru memiliki hak akses penuh untuk <strong>membuat dan mengelola bank soal</strong> (teks stimulus, pilihan ganda A-E, dan kunci jawaban).<br>
                2. Pengaturan token ujian, alokasi waktu ujian, reset siswa, dan administrasi peserta diatur terpusat oleh <strong>Admin CBT</strong>.<br>
                3. Guru <strong>hanya dapat melihat dan mengambil rekap nilai siswa setelah asesmen dinyatakan SELESAI</strong> oleh Admin untuk menjaga integritas ujian.
            </p>
        </div>
    </div>

    <!-- Status Ujian Mapel Guru -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Daftar Asesmen Mapel Ini -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm p-5 transition-colors duration-300">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700 mb-3">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center space-x-2">
                    <i class="fa-solid fa-list-check text-sky-600 dark:text-sky-400"></i>
                    <span>Jadwal Asesmen Mata Pelajaran</span>
                </h3>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">{{ $exams->count() }} Paket</span>
            </div>

            <div class="space-y-3">
                @forelse($exams as $ex)
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-white">{{ $ex->title }}</h4>
                            <p class="text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">
                                Kategori: {{ $ex->category }} &bull; Durasi: {{ $ex->duration_minutes }} Menit
                            </p>
                        </div>

                        <div>
                            @if($ex->status === 'completed')
                                <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 rounded-full font-bold text-[10px] flex items-center space-x-1">
                                    <i class="fa-solid fa-check"></i>
                                    <span>SELESAI (Nilai Siap)</span>
                                </span>
                            @elseif($ex->status === 'active')
                                <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 rounded-full font-bold text-[10px] flex items-center space-x-1 animate-pulse">
                                    <i class="fa-solid fa-spinner fa-spin"></i>
                                    <span>Sedang Berjalan</span>
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 rounded-full font-bold text-[10px]">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 dark:text-slate-500 text-xs">
                        Belum ada jadwal asesmen untuk mata pelajaran ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Access Nilai & Bank Soal -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm p-5 flex flex-col justify-between transition-colors duration-300">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700 mb-3">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center space-x-2">
                        <i class="fa-solid fa-chart-column text-emerald-600 dark:text-emerald-400"></i>
                        <span>Status Rekap Nilai</span>
                    </h3>
                </div>

                <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 rounded-xl space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 dark:text-slate-300">Asesmen yang Telah Selesai:</span>
                        <span class="font-bold text-emerald-800 dark:text-emerald-300 text-sm">{{ $completedExamsCount }} Selesai</span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                        Nilai peserta pada asesmen yang telah berstatus selesai sudah dapat diunduh, dicetak, dan dianalisis ketuntasan belajarnya.
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 gap-3 text-xs">
                <a href="{{ route('guru.bank-soal') }}" class="interactive-click p-2.5 bg-sky-50 dark:bg-sky-950/40 hover:bg-sky-100 dark:hover:bg-sky-900/60 text-sky-800 dark:text-sky-300 rounded-lg font-bold text-center border border-sky-200 dark:border-sky-800 transition">
                    <i class="fa-solid fa-file-pen block mb-1 text-sm"></i>
                    Kelola Bank Soal
                </a>
                <a href="{{ route('guru.rekap-nilai') }}" class="interactive-click p-2.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 rounded-lg font-bold text-center border border-emerald-200 dark:border-emerald-800 transition">
                    <i class="fa-solid fa-chart-line block mb-1 text-sm"></i>
                    Buka Rekap Nilai
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
