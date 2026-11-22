@extends('layouts.guru')

@section('title', 'Bank Soal - Portal Guru')

@section('content')
<div class="space-y-6">

    <!-- Header & Toggle Form Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Bank Soal Mata Pelajaran</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Mata Pelajaran: <strong class="text-sky-700 dark:text-sky-400">{{ $subject->name }} ({{ $subject->code }})</strong> &bull; Total: {{ $questions->total() }} Butir Soal
            </p>
        </div>

        <button onclick="document.getElementById('modalTambahSoal').classList.remove('hidden')" 
            class="interactive-click px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-2 self-start">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Butir Soal Baru</span>
        </button>
    </div>

    <!-- Questions List Table / Cards -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Daftar Soal Tersimpan</h3>
            <span class="text-xs text-slate-400 dark:text-slate-500">Menampilkan {{ $questions->count() }} dari {{ $questions->total() }} soal</span>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($questions as $idx => $q)
                <div class="p-5 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 font-bold text-xs flex items-center justify-center">
                                {{ $questions->firstItem() + $idx }}
                            </span>
                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 rounded font-bold text-[10px]">
                                Kunci: {{ $q->correct_answer }}
                            </span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                Bobot: {{ $q->score_weight }}
                            </span>
                        </div>

                        <form action="{{ route('guru.bank-soal.delete', $q->id) }}" method="POST" onsubmit="return confirm('Hapus butir soal nomor {{ $questions->firstItem() + $idx }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="interactive-click px-2.5 py-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded text-xs transition border border-red-200 dark:border-red-900" title="Hapus Soal">
                                <i class="fa-solid fa-trash text-[11px] mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>

                    <!-- Stimulus / Teks Bacaan jika ada -->
                    @if($q->stimulus)
                        <div class="p-3 bg-amber-50/70 dark:bg-amber-950/20 border-l-4 border-amber-400 rounded-r-lg text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-serif max-h-32 overflow-y-auto whitespace-pre-line">
                            <span class="text-[10px] font-bold text-amber-800 dark:text-amber-400 uppercase block font-sans mb-1">
                                <i class="fa-solid fa-file-lines mr-1"></i> Stimulus / Teks Bacaan:
                            </span>
                            {{ Str::limit($q->stimulus, 300) }}
                        </div>
                    @endif

                    <!-- Pertanyaan -->
                    <div class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white leading-relaxed">
                        {{ $q->question_text }}
                    </div>

                    <!-- Pilihan Ganda -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs pt-1">
                        <div class="p-2 rounded border {{ $q->correct_answer == 'A' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 font-semibold text-emerald-900 dark:text-emerald-300' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300' }}">
                            <span class="font-bold mr-1">A.</span> {{ $q->option_a }}
                        </div>
                        <div class="p-2 rounded border {{ $q->correct_answer == 'B' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 font-semibold text-emerald-900 dark:text-emerald-300' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300' }}">
                            <span class="font-bold mr-1">B.</span> {{ $q->option_b }}
                        </div>
                        <div class="p-2 rounded border {{ $q->correct_answer == 'C' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 font-semibold text-emerald-900 dark:text-emerald-300' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300' }}">
                            <span class="font-bold mr-1">C.</span> {{ $q->option_c }}
                        </div>
                        <div class="p-2 rounded border {{ $q->correct_answer == 'D' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 font-semibold text-emerald-900 dark:text-emerald-300' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300' }}">
                            <span class="font-bold mr-1">D.</span> {{ $q->option_d }}
                        </div>
                        @if($q->option_e)
                            <div class="p-2 rounded border md:col-span-2 {{ $q->correct_answer == 'E' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 font-semibold text-emerald-900 dark:text-emerald-300' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300' }}">
                                <span class="font-bold mr-1">E.</span> {{ $q->option_e }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400 dark:text-slate-500 text-xs">
                    Belum ada butir soal pada mata pelajaran ini. Silakan klik tombol "Tambah Butir Soal Baru".
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $questions->links() }}
        </div>
    </div>

    <!-- Modal Tambah Soal Baru -->
    <div id="modalTambahSoal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 dark:border-slate-800 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base">Tambah Butir Soal Pilihan Ganda</h3>
                <button onclick="document.getElementById('modalTambahSoal').classList.add('hidden')" class="interactive-click text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('guru.bank-soal.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <!-- Stimulus Bacaan (Opsional) -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Stimulus / Teks Bacaan (Opsional - Kosongkan jika soal mandiri)
                    </label>
                    <textarea name="stimulus" rows="4" placeholder="Ketikkan teks cerita, dialog, artikel, atau konteks bacaan untuk beberapa soal sekaligus..."
                        class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500"></textarea>
                </div>

                <!-- Teks Pertanyaan (Wajib) -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Pertanyaan / Pokok Soal <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question_text" rows="3" required placeholder="Ketikkan pertanyaan soal di sini..."
                        class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500"></textarea>
                </div>

                <!-- Pilihan Jawaban A, B, C, D, E -->
                <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <label class="block font-bold text-slate-800 dark:text-slate-100">Pilihan Jawaban:</label>

                    <div class="flex items-center space-x-2">
                        <span class="w-6 font-bold text-slate-600 dark:text-slate-400">A.</span>
                        <input type="text" name="option_a" required placeholder="Teks opsi pilihan A"
                            class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="w-6 font-bold text-slate-600 dark:text-slate-400">B.</span>
                        <input type="text" name="option_b" required placeholder="Teks opsi pilihan B"
                            class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="w-6 font-bold text-slate-600 dark:text-slate-400">C.</span>
                        <input type="text" name="option_c" required placeholder="Teks opsi pilihan C"
                            class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="w-6 font-bold text-slate-600 dark:text-slate-400">D.</span>
                        <input type="text" name="option_d" required placeholder="Teks opsi pilihan D"
                            class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="w-6 font-bold text-slate-600 dark:text-slate-400">E.</span>
                        <input type="text" name="option_e" placeholder="Teks opsi pilihan E (opsional untuk SMA)"
                            class="flex-1 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Kunci Jawaban Benar <span class="text-red-500">*</span>
                        </label>
                        <select name="correct_answer" required class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg font-bold text-emerald-700 dark:text-emerald-400 focus:ring-2 focus:ring-sky-500">
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                            <option value="E">Opsi E</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bobot Nilai</label>
                        <input type="number" step="0.1" name="score_weight" value="1.0" required min="0.1"
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg font-bold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahSoal').classList.add('hidden')"
                        class="interactive-click px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                        class="interactive-click px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-bold shadow transition">
                        Simpan ke Bank Soal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
