@extends('layouts.guru')

@section('title', 'SMKN 15 JAKARTA')

@section('content')
<div class="space-y-6">

    <!-- Header & Exam Selector -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Rekapitulasi Nilai Siswa</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Mata Pelajaran: <strong class="text-sky-700 dark:text-sky-400">{{ $subject->name }}</strong> &bull; Nilai hanya dapat diakses setelah asesmen diselesaikan oleh Admin.
            </p>
        </div>

        <!-- Filter Ujian Selector & Tombol Unduh Excel -->
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('guru.rekap-nilai') }}" method="GET" class="flex items-center space-x-2">
                <select name="exam_id" onchange="this.form.submit()" 
                    class="text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-sm">
                    @foreach($exams as $ex)
                        <option value="{{ $ex->id }}" {{ $selectedExam && $selectedExam->id == $ex->id ? 'selected' : '' }}>
                            {{ $ex->title }} ({{ strtoupper($ex->status) }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if($isCompleted)
                <!-- Unduh Format Template Excel -->
                <a href="{{ route('guru.rekap-nilai.template-excel', ['exam_id' => $selectedExam->id]) }}" 
                    class="interactive-click px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition flex items-center space-x-1.5 border border-slate-200 dark:border-slate-700 shadow-sm"
                    title="Unduh format template penilaian Excel kosong">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Template Nilai</span>
                </a>

                <!-- Unduh Nilai Excel Resmi -->
                <a href="{{ route('guru.rekap-nilai.export-excel', ['exam_id' => $selectedExam->id]) }}" 
                    class="interactive-click px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1.5"
                    title="Unduh seluruh rekap nilai lengkap dengan kop sekolah ke format Excel">
                    <i class="fa-solid fa-file-excel text-sm"></i>
                    <span>Unduh Excel (.xls)</span>
                </a>

                <button onclick="window.print()" class="interactive-click px-3.5 py-2 bg-slate-700 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak PDF</span>
                </button>
            @endif
        </div>
    </div>

    @if(!$selectedExam)
        <div class="bg-white dark:bg-slate-900 rounded-xl p-8 border border-slate-200 dark:border-slate-800 text-center text-slate-400 dark:text-slate-500 text-xs">
            Belum ada jadwal ujian untuk mata pelajaran ini.
        </div>
    @elseif(!$isCompleted)
        <!-- KONDISI ASESMEN BELUM SELESAI (SESUAI PERMINTAAN USER: Guru hanya bisa ambil nilai jika asesmen telah selesai) -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border-2 border-dashed border-amber-300 dark:border-amber-600/60 p-8 sm:p-12 text-center max-w-2xl mx-auto shadow-sm">
            <div class="w-16 h-16 bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
                <i class="fa-solid fa-lock"></i>
            </div>

            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Rekap Nilai Belum Dapat Diakses</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-lg mx-auto leading-relaxed">
                Asesmen <strong class="text-slate-800 dark:text-slate-200">"{{ $selectedExam->title }}"</strong> saat ini masih berstatus 
                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $selectedExam->status === 'active' ? 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 animate-pulse' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                    {{ strtoupper($selectedExam->status) }}
                </span>.
            </p>

            <div class="mt-5 p-4 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-800 text-xs text-amber-900 dark:text-amber-200 max-w-md mx-auto text-left space-y-1.5">
                <div class="font-bold flex items-center space-x-1.5 text-amber-700 dark:text-amber-400">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Kebijakan Keamanan Nilai:</span>
                </div>
                <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-normal">
                    Sesuai instruksi sistem ujian sekolah, <strong>Guru hanya dapat melihat dan mengambil nilai jika asesmen telah dinyatakan SELESAI</strong> secara resmi oleh Administrator CBT agar seluruh proses pengerjaan siswa terverifikasi secara tuntas.
                </p>
            </div>

            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-4 italic">
                *Silakan hubungi Admin / Proktor jika asesmen di ruangan telah selesai dan siap dikunci statusnya.
            </p>
        </div>
    @else
        <!-- KONDISI ASESMEN SELESAI (DETAIL NILAI SESUAI GAMBAR 7 & 8) -->
        <!-- Exam Overview Card (Sesuai Gambar 7 & 8) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-6 relative">
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">{{ $selectedExam->title }}</h3>
                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 rounded-full text-xs font-bold flex items-center space-x-1">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                    <span>ASESMEN SELESAI</span>
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-8 pt-4 text-xs">
                <!-- Mata Kuliah / Pelajaran -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-800/60 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Mata Pelajaran</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $selectedExam->subject->code ?? '-' }} - {{ $selectedExam->subject->name ?? '-' }}</span>
                </div>

                <!-- Prodi / Jurusan -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-800/60 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Jenjang / Kelas</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $selectedExam->education_level ?? 'SMA/MA/SMK/MAK/Sederajat' }}</span>
                </div>

                <!-- Kategori (Badge UTS Sesuai Gambar 7 & 8) -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-800/60 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Kategori</span>
                    <div>
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#0284c7] text-white">
                            {{ $selectedExam->category ?? 'UTS' }}
                        </span>
                    </div>
                </div>

                <!-- Tanggal & Waktu -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-800/60 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Tanggal</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">
                        {{ $selectedExam->start_time ? $selectedExam->start_time->format('d/m/Y') : now()->format('d/m/Y') }} 
                        ({{ $selectedExam->duration_minutes }} menit)
                    </span>
                </div>

                <!-- Nilai Minimal (KKM) -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-800/60 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Nilai Minimal</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($selectedExam->passing_grade, 2) }}</span>
                </div>

                <!-- Total Peserta -->
                <div class="flex items-baseline space-x-4 border-b border-slate-50 dark:border-slate-800/60 pb-2">
                    <span class="w-28 text-slate-500 dark:text-slate-400 shrink-0 font-medium">Total Peserta</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $attempts->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Table: Daftar Nilai Peserta (Sesuai Gambar 7 & 8) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-slate-800 dark:text-slate-100 font-bold text-sm">
                    <i class="fa-solid fa-users text-sky-600 dark:text-sky-400"></i>
                    <span>Daftar Nilai Peserta</span>
                </div>
                <div class="text-xs text-slate-400 dark:text-slate-500">
                    Menampilkan {{ $attempts->count() }} data peserta yang telah menyelesaikan ujian
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
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
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($attempts as $index => $at)
                            @php
                                $isPass = $at->score >= $selectedExam->passing_grade;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
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
    @endif

</div>
@endsection
