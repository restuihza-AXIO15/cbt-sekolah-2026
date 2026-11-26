@extends('layouts.admin')

@section('title', 'Analisis Butir Soal & Statistik - Admin CBT')

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Analisis Butir Soal & Statistik</h1>
            <nav class="flex text-xs text-slate-400 dark:text-slate-500 mt-1 font-medium space-x-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 dark:hover:text-sky-400">Home</a>
                <span>/</span>
                <a href="{{ route('admin.rekap-nilai') }}" class="hover:text-blue-600 dark:hover:text-sky-400">Rekap Nilai</a>
                <span>/</span>
                <span class="text-blue-600 dark:text-sky-400 font-semibold">Analisis Soal</span>
            </nav>
        </div>

        <!-- Filter Ujian Selector & Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('admin.analisis-soal') }}" method="GET" class="flex items-center">
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
                <a href="{{ route('admin.rekap-nilai', ['exam_id' => $selectedExam->id]) }}" 
                   class="interactive-click px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                    <span>Detail Nilai</span>
                </a>

                <a href="{{ route('admin.rekap-nilai.export-excel', ['exam_id' => $selectedExam->id]) }}" 
                   class="interactive-click px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5"
                   title="Unduh Nilai Excel Resmi">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
            @endif

            <button onclick="window.print()" class="interactive-click px-3.5 py-2 bg-slate-700 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Analisis</span>
            </button>
        </div>
    </div>

    @if($selectedExam)
        <!-- Exam Meta Info Banner -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm p-5 transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700 gap-2">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">{{ $selectedExam->title }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $selectedExam->subject->code ?? '-' }} - {{ $selectedExam->subject->name ?? '-' }} | Jenjang: {{ $selectedExam->education_level ?? 'SMA/SMK' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded text-xs font-bold bg-[#0284c7] text-white">
                        {{ $selectedExam->category ?? 'UTS' }}
                    </span>
                    <span class="px-3 py-1 rounded text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                        KKM: {{ number_format($selectedExam->passing_grade, 2) }}
                    </span>
                </div>
            </div>

            <!-- Stats KPI Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4">
                <!-- Rata-rata -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Rata-Rata Nilai</span>
                    <span class="text-2xl font-bold text-slate-800 dark:text-white block mt-1">
                        {{ number_format($stats['rata_rata'], 2) }}
                    </span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">
                        Tertinggi: <b class="text-emerald-600">{{ $stats['tertinggi'] }}</b> | Terendah: <b class="text-rose-600">{{ $stats['terendah'] }}</b>
                    </span>
                </div>

                <!-- Kelulusan -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Tingkat Kelulusan</span>
                    <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 block mt-1">
                        {{ $stats['pass_rate'] }}%
                    </span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">
                        Lulus: <b class="text-emerald-600">{{ $stats['lulus'] }}</b> | Remedial: <b class="text-rose-600">{{ $stats['remedial'] }}</b>
                    </span>
                </div>

                <!-- Total Peserta Selesai -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Peserta Ujian</span>
                    <span class="text-2xl font-bold text-blue-600 dark:text-sky-400 block mt-1">
                        {{ $stats['total_peserta'] }}
                    </span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">
                        Telah menyelesaikan sesi
                    </span>
                </div>

                <!-- Butir Soal -->
                <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-700/60">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Total Butir Soal</span>
                    <span class="text-2xl font-bold text-purple-600 dark:text-purple-400 block mt-1">
                        {{ count($analysis) }}
                    </span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">
                        Soal pilihan ganda teruji
                    </span>
                </div>
            </div>

            <!-- Score Distribution Bar Breakdown -->
            @if($stats['total_peserta'] > 0)
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Distribusi Rentang Nilai Peserta</span>
                        <span class="text-[11px] text-slate-400">Total: {{ $stats['total_peserta'] }} Siswa</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        <div class="p-2.5 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/40">
                            <div class="flex justify-between items-baseline">
                                <span class="font-bold text-red-800 dark:text-red-300">&lt; 60 (Kurang)</span>
                                <span class="font-bold text-red-700 dark:text-red-400">{{ $stats['ranges']['0-59'] }} Siswa</span>
                            </div>
                            <div class="w-full bg-red-200 dark:bg-red-900/60 rounded-full h-1.5 mt-2 overflow-hidden">
                                <div class="bg-red-600 h-1.5 rounded-full" style="width: {{ ($stats['ranges']['0-59'] / $stats['total_peserta']) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/40">
                            <div class="flex justify-between items-baseline">
                                <span class="font-bold text-amber-800 dark:text-amber-300">60 - 74 (Cukup)</span>
                                <span class="font-bold text-amber-700 dark:text-amber-400">{{ $stats['ranges']['60-74'] }} Siswa</span>
                            </div>
                            <div class="w-full bg-amber-200 dark:bg-amber-900/60 rounded-full h-1.5 mt-2 overflow-hidden">
                                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ ($stats['ranges']['60-74'] / $stats['total_peserta']) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/40">
                            <div class="flex justify-between items-baseline">
                                <span class="font-bold text-blue-800 dark:text-blue-300">75 - 84 (Baik)</span>
                                <span class="font-bold text-blue-700 dark:text-blue-400">{{ $stats['ranges']['75-84'] }} Siswa</span>
                            </div>
                            <div class="w-full bg-blue-200 dark:bg-blue-900/60 rounded-full h-1.5 mt-2 overflow-hidden">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ ($stats['ranges']['75-84'] / $stats['total_peserta']) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/40">
                            <div class="flex justify-between items-baseline">
                                <span class="font-bold text-emerald-800 dark:text-emerald-300">85 - 100 (Sangat Baik)</span>
                                <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ $stats['ranges']['85-100'] }} Siswa</span>
                            </div>
                            <div class="w-full bg-emerald-200 dark:bg-emerald-900/60 rounded-full h-1.5 mt-2 overflow-hidden">
                                <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ ($stats['ranges']['85-100'] / $stats['total_peserta']) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Table: Analisis Daya Pembeda & Tingkat Kesukaran Butir Soal -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center space-x-2 text-slate-800 dark:text-white font-bold text-sm">
                    <i class="fa-solid fa-list-check text-purple-600 dark:text-purple-400"></i>
                    <span>Tabel Analisis Tingkat Kesukaran Butir Soal</span>
                </div>
                <div class="flex items-center space-x-3 text-xs">
                    <span class="inline-flex items-center space-x-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Mudah (&gt;70%)</span>
                    </span>
                    <span class="inline-flex items-center space-x-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Sedang (30-70%)</span>
                    </span>
                    <span class="inline-flex items-center space-x-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Sukar (&lt;30%)</span>
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Pertanyaan / Soal</th>
                            <th class="py-3 px-4 text-center">Kunci</th>
                            <th class="py-3 px-4 text-center">Total Jawab</th>
                            <th class="py-3 px-4 text-center">Benar</th>
                            <th class="py-3 px-4 text-center">Salah</th>
                            <th class="py-3 px-4 w-44">Tingkat Akurasi</th>
                            <th class="py-3 px-4 text-center">Klasifikasi</th>
                            <th class="py-3 px-4 text-center">Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-200">
                        @forelse($analysis as $item)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-500 dark:text-slate-400">
                                    {{ $item['number'] }}
                                </td>
                                <td class="py-3.5 px-4 max-w-md">
                                    <span class="font-medium text-slate-800 dark:text-slate-200 line-clamp-2">
                                        {{ Str::limit(strip_tags($item['text']), 120) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-sky-300 font-mono font-bold text-xs shadow-sm">
                                        {{ $item['correct_answer'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-semibold text-slate-600 dark:text-slate-400">
                                    {{ $item['answered_count'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $item['correct_count'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-rose-600 dark:text-rose-400">
                                    {{ $item['incorrect_count'] }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                            @php
                                                $barColor = $item['difficulty_category'] === 'Mudah' ? 'bg-emerald-500' : ($item['difficulty_category'] === 'Sedang' ? 'bg-blue-500' : 'bg-rose-500');
                                            @endphp
                                            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $item['accuracy_percentage'] }}%"></div>
                                        </div>
                                        <span class="font-mono font-bold text-[11px] w-12 text-right">
                                            {{ $item['accuracy_percentage'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($item['difficulty_category'] === 'Mudah')
                                        <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 rounded text-[11px] font-bold">
                                            Mudah
                                        </span>
                                    @elseif($item['difficulty_category'] === 'Sedang')
                                        <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 rounded text-[11px] font-bold">
                                            Sedang
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300 rounded text-[11px] font-bold">
                                            Sukar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center text-[11px]">
                                    @if($item['difficulty_category'] === 'Sukar')
                                        <span class="text-rose-600 dark:text-rose-400 font-semibold" title="Sebagian besar siswa gagal menjawab. Perlu telaah materi/soal.">
                                            Perlu Remedial
                                        </span>
                                    @elseif($item['difficulty_category'] === 'Mudah')
                                        <span class="text-slate-500 dark:text-slate-400 font-medium" title="Daya pembeda rendah, hampir semua benar.">
                                            Daya Pembeda Rendah
                                        </span>
                                    @else
                                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold" title="Tingkat kesulitan ideal dan proporsional.">
                                            Baik / Ideal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                    Belum ada data butir soal yang dapat dianalisis untuk ujian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-800 rounded-xl p-8 border border-slate-200 dark:border-slate-700 text-center text-slate-400 dark:text-slate-500 text-xs">
            Pilih atau buat jadwal ujian terlebih dahulu untuk melihat analisis butir soal.
        </div>
    @endif

</div>
@endsection
