@extends('layouts.admin')

@section('title', 'Administrasi Tes & Waktu Ujian - Admin CBT')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Administrasi Tes & Jadwal Ujian</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Atur paket ujian, alokasi waktu pengerjaan (standar 120 menit/fleksibel), KKM, dan status aktif.
            </p>
        </div>

        <button onclick="document.getElementById('modalTambahJadwal').classList.remove('hidden')" 
            class="interactive-click px-4 py-2 bg-[#1e73be] hover:bg-[#155d9b] text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-1.5 self-start">
            <i class="fa-solid fa-plus"></i>
            <span>Buat Jadwal Ujian Baru</span>
        </button>
    </div>

    <!-- Table of Exams -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Daftar Jadwal Ujian & Alokasi Waktu</h3>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $exams->count() }} Paket Ujian Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Tes / Paket</th>
                        <th class="py-3 px-4">Mata Pelajaran</th>
                        <th class="py-3 px-4 text-center">Kategori</th>
                        <th class="py-3 px-4 text-center">Waktu Pengerjaan</th>
                        <th class="py-3 px-4 text-center">KKM</th>
                        <th class="py-3 px-4 text-center">Anti-Curang</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($exams as $idx => $ex)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-4 text-center font-medium">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $ex->title }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-700 dark:text-slate-300">
                                {{ $ex->subject->name ?? '-' }} ({{ $ex->subject->code ?? '-' }})
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300">
                                    {{ $ex->category }}
                                </span>
                            </td>
                            <!-- Alokasi Waktu Ujian (Sesuai User Request: 120 menit default) -->
                            <td class="py-3.5 px-4 text-center font-bold text-blue-700 dark:text-blue-400">
                                {{ $ex->duration_minutes }} Menit
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                {{ number_format($ex->passing_grade, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center text-[11px] text-slate-500 dark:text-slate-400">
                                Maks {{ $ex->max_violations }}x strike
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($ex->status === 'active')
                                    <span class="px-2.5 py-1 bg-emerald-500 text-white rounded-full font-bold text-[10px] shadow-sm animate-pulse">
                                        AKTIF (RUNNING)
                                    </span>
                                @elseif($ex->status === 'completed')
                                    <span class="px-2.5 py-1 bg-slate-600 dark:bg-slate-700 text-white rounded-full font-bold text-[10px] shadow-sm">
                                        SELESAI
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-400 text-slate-900 rounded-full font-bold text-[10px] shadow-sm">
                                        DRAFT
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1">
                                    @if($ex->status !== 'active')
                                        <form action="{{ route('admin.jadwal.status', $ex->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="interactive-click px-2 py-1 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:hover:bg-emerald-900 text-emerald-800 dark:text-emerald-300 rounded text-[11px] font-semibold transition" title="Aktifkan Ujian">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    @if($ex->status === 'active')
                                        <form action="{{ route('admin.jadwal.status', $ex->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="interactive-click px-2 py-1 bg-slate-700 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white rounded text-[11px] font-semibold transition" title="Tandai Selesai agar Guru Bisa Akses Nilai">
                                                Selesaikan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 dark:text-slate-500">Belum ada jadwal ujian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Buat Jadwal Ujian Baru -->
    <div id="modalTambahJadwal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base">Buat Jadwal & Alokasi Waktu Ujian</h3>
                <button onclick="document.getElementById('modalTambahJadwal').classList.add('hidden')" class="interactive-click text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.jadwal.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Mata Pelajaran</label>
                    <select name="subject_id" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                        @foreach($subjects as $sb)
                            <option value="{{ $sb->id }}">{{ $sb->name }} ({{ $sb->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Judul / Paket Ujian</label>
                    <input type="text" name="title" required placeholder="Contoh: Bahasa Inggris (Paket 1)" 
                        class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                        <select name="category" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                            <option value="UTS">UTS (Penilaian Tengah Semester)</option>
                            <option value="UAS">UAS (Penilaian Akhir Semester)</option>
                            <option value="Simulasi TKA">Simulasi TKA / ANBK</option>
                            <option value="Asesmen Sumatif">Asesmen Sumatif Akhir</option>
                        </select>
                    </div>

                    <!-- Alokasi Waktu (Default 120 Menit Sesuai Permintaan User) -->
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alokasi Waktu (Menit)</label>
                        <input type="number" name="duration_minutes" value="120" required min="10" max="300"
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg font-bold text-blue-700 dark:text-blue-400 focus:ring-2 focus:ring-[#1e73be]">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500">*Standar 120 menit per 1 mapel</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nilai KKM (Passing Grade)</label>
                        <input type="number" step="0.1" name="passing_grade" value="75.0" required 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Toleransi Pelanggaran Tab</label>
                        <input type="number" name="max_violations" value="3" required min="1" max="10"
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500">*Otomatis terkunci jika tercapai</span>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="flex items-center space-x-2 text-slate-700 dark:text-slate-300 font-medium">
                        <input type="checkbox" name="shuffle_questions" value="1" checked class="rounded text-blue-600">
                        <span>Acak Urutan Soal untuk Tiap Siswa</span>
                    </label>
                    <label class="flex items-center space-x-2 text-slate-700 dark:text-slate-300 font-medium">
                        <input type="checkbox" name="shuffle_options" value="1" checked class="rounded text-blue-600">
                        <span>Acak Urutan Pilihan Jawaban (A, B, C, D, E)</span>
                    </label>
                </div>

                <div class="pt-2">
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Awal</label>
                    <select name="status" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                        <option value="active">Langsung Aktifkan Sekarang</option>
                        <option value="draft">Simpan Sebagai Draft</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahJadwal').classList.add('hidden')"
                        class="interactive-click px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                        class="interactive-click px-5 py-2 bg-[#1e73be] hover:bg-[#155d9b] text-white rounded-lg font-bold shadow transition">
                        Simpan Jadwal Ujian
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
