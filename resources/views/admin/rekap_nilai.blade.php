@extends('layouts.admin')

@section('title', 'Detail Nilai - Rekap CBT 2026')

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb (Sesuai Gambar 7 & 8) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Detail Nilai</h1>
            <nav class="flex text-xs text-slate-400 dark:text-slate-500 mt-1 font-medium space-x-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-sky-400">Home</a>
                <span>/</span>
                <span class="text-slate-500 dark:text-slate-400">Rekap Nilai</span>
                <span>/</span>
                <span class="text-blue-600 dark:text-sky-400 font-semibold">Detail</span>
            </nav>
        </div>

        <!-- Filter Ujian Selector & Export Actions -->
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('admin.rekap-nilai') }}" method="GET" class="flex items-center">
                <select name="exam_id" onchange="this.form.submit()" 
                    class="text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    @foreach($exams as $ex)
                        <option value="{{ $ex->id }}" {{ $selectedExam && $selectedExam->id == $ex->id ? 'selected' : '' }}>
                            {{ $ex->title }} ({{ strtoupper($ex->status) }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if($selectedExam)
                <!-- Unduh Nilai Langsung Excel (.xls) Resmi -->
                <a href="{{ route('admin.rekap-nilai.export-excel', ['exam_id' => $selectedExam->id]) }}" 
                   class="interactive-click px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5"
                   title="Unduh Nilai Resmi Siswa dalam Format Excel dengan Kop Sekolah">
                    <i class="fa-solid fa-file-excel text-sm"></i>
                    <span>Unduh Excel</span>
                </a>

                <!-- Download Template Format Nilai -->
                <a href="{{ route('admin.rekap-nilai.template-excel', ['exam_id' => $selectedExam->id]) }}" 
                   class="interactive-click px-3 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5"
                   title="Unduh Format Template Lembar Nilai Excel">
                    <i class="fa-solid fa-file-arrow-down text-emerald-600 dark:text-emerald-400"></i>
                    <span>Template Nilai</span>
                </a>

                <!-- Analisis Butir Soal & Daya Pembeda -->
                <a href="{{ route('admin.analisis-soal', ['exam_id' => $selectedExam->id]) }}" 
                   class="interactive-click px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5"
                   title="Buka Analisis Butir Soal & Tingkat Kesukaran">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Analisis Soal</span>
                </a>
            @endif

            <button onclick="window.print()" class="interactive-click px-3 py-2 bg-slate-700 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak</span>
            </button>
        </div>
    </div>

    @if($selectedExam)
        <!-- Exam Overview Card (Sesuai Gambar 7 & 8) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm p-6 relative transition-colors duration-300">
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">{{ $selectedExam->title }}</h3>
                <a href="{{ route('admin.jadwal') }}" 
                   class="interactive-click px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                    <span>Kembali</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-8 pt-4 text-xs">
                <!-- Mata Kuliah / Pelajaran -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Mata Pelajaran</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $selectedExam->subject->code ?? '-' }} - {{ $selectedExam->subject->name ?? '-' }}</span>
                </div>

                <!-- Prodi / Jurusan -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Jenjang / Kelas</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $selectedExam->education_level ?? 'SMA/MA/SMK/MAK/Sederajat' }}</span>
                </div>

                <!-- Kategori (Badge UTS Sesuai Gambar 7 & 8) -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Kategori</span>
                    <div>
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#0284c7] text-white">
                            {{ $selectedExam->category ?? 'UTS' }}
                        </span>
                    </div>
                </div>

                <!-- Tanggal & Waktu -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Tanggal</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">
                        {{ $selectedExam->start_time ? $selectedExam->start_time->format('d/m/Y') : now()->format('d/m/Y') }} 
                        ({{ $selectedExam->duration_minutes }} menit)
                    </span>
                </div>

                <!-- Nilai Minimal (KKM) -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Nilai Minimal</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ number_format($selectedExam->passing_grade, 2) }}</span>
                </div>

                <!-- Total Peserta -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Total Peserta</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $attempts->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Table: Daftar Nilai Peserta (Sesuai Gambar 7 & 8) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-slate-800 dark:text-white font-bold text-sm">
                    <i class="fa-solid fa-users text-[#1e73be] dark:text-sky-400"></i>
                    <span>Daftar Nilai Peserta</span>
                </div>
                <div class="text-xs text-slate-400 dark:text-slate-500">
                    Menampilkan {{ $attempts->count() }} data peserta yang telah menyelesaikan ujian
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">NIM / NISN</th>
                            <th class="py-3 px-4">Nama</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Nilai</th>
                            <th class="py-3 px-4 text-center">Benar</th>
                            <th class="py-3 px-4 text-center">Salah</th>
                            <th class="py-3 px-4 text-center">Tidak Dijawab</th>
                            <th class="py-3 px-4">Durasi</th>
                            <th class="py-3 px-4 text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-200">
                        @forelse($attempts as $index => $at)
                            @php
                                $isPass = $at->score >= $selectedExam->passing_grade;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                                <td class="py-3.5 px-4 text-center font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $at->user->nisn ?: $at->user->username }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white">
                                    {{ $at->user->name }}
                                </td>
                                <!-- Status Badge [Selesai] Green (Sesuai Gambar 7 & 8) -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 bg-emerald-500 text-white rounded text-[11px] font-bold shadow-sm">
                                        Selesai
                                    </span>
                                </td>
                                <!-- Nilai (Sesuai Gambar 7 & 8: 100.00, 91.67) -->
                                <td class="py-3.5 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400 text-sm">
                                    {{ number_format($at->score, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-semibold">{{ $at->correct_count }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold text-red-500 dark:text-red-400">{{ $at->incorrect_count }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold text-slate-400 dark:text-slate-500">{{ $at->unanswered_count }}</td>
                                <!-- Durasi (Sesuai Gambar 7 & 8: e.g. 67 menit 0 detik) -->
                                <td class="py-3.5 px-4 font-medium text-slate-600 dark:text-slate-300">
                                    {{ $at->durationFormatted() }}
                                </td>
                                <!-- Keterangan Badge [LULUS] / [REMIDIAL] (Sesuai Gambar 7 & 8) -->
                                <td class="py-3.5 px-4 text-center">
                                    @if($isPass)
                                        <span class="px-2.5 py-1 bg-emerald-600 text-white rounded text-[11px] font-bold shadow-sm">
                                            LULUS
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-rose-600 text-white rounded text-[11px] font-bold shadow-sm">
                                            REMIDIAL
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada peserta yang menyelesaikan ujian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-800 rounded-xl p-8 border border-slate-200 dark:border-slate-700 text-center text-slate-400 dark:text-slate-500 text-xs">
            Tidak ada data ujian untuk ditampilkan.
        </div>
    @endif

</div>
@endsection
