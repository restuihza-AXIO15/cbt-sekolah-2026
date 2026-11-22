@extends('layouts.admin')

@section('title', 'Dashboard - CBT Pusat Asesmen Pendidikan')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- Welcome & Active Status Banner -->
    <div class="bg-gradient-to-r from-blue-700 via-[#1e73be] to-sky-600 rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="px-2.5 py-0.5 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-semibold uppercase tracking-wider">
                Panel Pusat Asesmen & Ujian CBT 2026
            </span>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Selamat Datang, {{ Auth::user()->name }}</h1>
            <p class="text-xs text-blue-100 max-w-xl">
                Sistem CBT berbasis Laravel siap melayani asesmen sekolah dengan proteksi anti-curang berlapis, alokasi waktu fleksibel, dan token otomatis 10 menit.
            </p>
        </div>

        <!-- Quick Action: Rilis Token Baru (10 Menit) -->
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 text-center shrink-0 w-full sm:w-auto">
            <p class="text-[11px] text-blue-100 font-medium">Status Token Aktif:</p>
            <div class="text-2xl font-mono font-black tracking-widest text-yellow-300 my-1">
                {{ $activeToken ? $activeToken->token_code : 'TIDAK AKTIF' }}
            </div>
            <form action="{{ route('admin.token.generate') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="w-full px-4 py-1.5 bg-yellow-400 hover:bg-yellow-300 text-slate-900 font-bold text-xs rounded-lg shadow transition flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Rilis Token Baru (10m)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Peserta -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm flex items-center justify-between transition-colors duration-300">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa</p>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $totalSiswa }}</h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Peserta Terdaftar</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm flex items-center justify-between transition-colors duration-300">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Guru</p>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $totalGuru }}</h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Pembuat Bank Soal</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>

        <!-- Mata Pelajaran & Soal -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm flex items-center justify-between transition-colors duration-300">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bank Soal</p>
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $totalSoal }}</h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Dari {{ $totalMapel }} Mata Pelajaran</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </div>

        <!-- Siswa Ujian Real-Time -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm flex items-center justify-between transition-colors duration-300">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Peserta</p>
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $siswaSedangUjian }} <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">Aktif</span></h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">{{ $siswaSelesai }} Selesai Ujian</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-laptop-code"></i>
            </div>
        </div>
    </div>

    <!-- 2 Column Section: Posko Pengumuman (Sesuai Gambar 6) & Jadwal Ujian Aktif -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Posko Pengumuman (Sesuai Gambar 6) -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm p-6 transition-colors duration-300">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-bullhorn text-[#1e73be] dark:text-sky-400"></i>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide">Pengumuman & Petunjuk Pelaksanaan</h3>
                </div>
                <span class="px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold rounded-full">
                    Posko CBT Aktif
                </span>
            </div>

            <!-- News Item (Sesuai Gambar 6: Pengumuman Akademik Posko) -->
            <div class="space-y-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                <div class="p-4 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center space-x-2 text-blue-700 dark:text-sky-400 font-bold mb-1">
                        <i class="fa-solid fa-envelope"></i>
                        <span>PENGUMUMAN ASESMEN TKA & UJIAN SEKOLAH 2026</span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-[11px] mb-2 font-medium">Posko Panitia Pelaksana CBT | Terbit: {{ now()->format('d/m/Y') }}</p>
                    <p>
                        Hasil Ujian Sekolah dan Tes Kemampuan Akademik (TKA) dilaksanakan berbasis komputer (CBT) secara terjadwal. Proktor dan pengawas ruang diwajibkan memastikan seluruh peserta mematuhi aturan anti-curang.
                    </p>
                    <div class="mt-3 pt-3 border-t border-slate-200/80 dark:border-slate-700">
                        <h5 class="font-bold text-slate-800 dark:text-white mb-1.5">Alur Kerja Satuan Pendidikan:</h5>
                        <ol class="list-decimal list-inside space-y-1 text-slate-600 dark:text-slate-300 text-[11px]">
                            <li>Login proktor/admin melalui portal CBT sekolah.</li>
                            <li>Verifikasi data peserta (Daftar Siswa & Cetak Kartu Peserta).</li>
                            <li>Rilis Token Ujian melalui menu Token (berlaku 10 menit).</li>
                            <li>Pantau peserta melalui menu Manajemen Pengawasan secara realtime.</li>
                            <li>Rekap nilai dapat diunduh otomatis setelah asesmen diselesaikan.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ujian Aktif & Sesi Saat Ini -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700 shadow-sm p-6 flex flex-col justify-between transition-colors duration-300">
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-check text-[#1e73be] dark:text-sky-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wide">Ujian Sedang Berjalan</h3>
                    </div>
                    @if($activeExam)
                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 text-[10px] font-bold rounded-full animate-pulse">
                            LIVE RUNNING
                        </span>
                    @else
                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded-full">
                            Draft / Kosong
                        </span>
                    @endif
                </div>

                @if($activeExam)
                    <div class="bg-blue-50/60 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-xl p-4 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Mata Pelajaran:</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $activeExam->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Alokasi Waktu:</span>
                            <span class="font-bold text-blue-700 dark:text-sky-400">{{ $activeExam->duration_minutes }} Menit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Nilai Minimal (KKM):</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ number_format($activeExam->passing_grade, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">Acak Soal & Jawaban:</span>
                            <span class="font-semibold text-emerald-700 dark:text-emerald-400">Aktif (Seeded Safe)</span>
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center text-slate-400 dark:text-slate-500 text-xs">
                        Belum ada jadwal ujian yang diaktifkan. Buka menu Administrasi Tes untuk mengaktifkan ujian.
                    </div>
                @endif
            </div>

            <!-- Quick Links -->
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 gap-2 text-xs">
                <a href="{{ route('admin.monitoring') }}" class="interactive-click p-2.5 bg-slate-100 dark:bg-slate-700/60 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-slate-700 dark:text-slate-200 font-semibold text-center transition flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-users-viewfinder text-red-600 dark:text-red-400"></i>
                    <span>Pantau Proctor</span>
                </a>
                <a href="{{ route('admin.rekap-nilai') }}" class="interactive-click p-2.5 bg-slate-100 dark:bg-slate-700/60 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-slate-700 dark:text-slate-200 font-semibold text-center transition flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-square-poll-vertical text-green-600 dark:text-green-400"></i>
                    <span>Rekap Nilai</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
