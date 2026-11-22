@extends('layouts.admin')

@section('title', 'Data Master Mata Pelajaran - Admin CBT')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Data Master Mata Pelajaran</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar mata pelajaran yang diujikan dalam sistem CBT.</p>
        </div>

        <button onclick="document.getElementById('modalTambahMapel').classList.remove('hidden')" 
            class="interactive-click px-4 py-2 bg-[#1e73be] hover:bg-[#155d9b] text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-1.5">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Mata Pelajaran</span>
        </button>
    </div>

    <!-- Table of Subjects -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Daftar Mata Pelajaran Terdaftar</h3>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $subjects->count() }} Mapel</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kode Mapel</th>
                        <th class="py-3 px-4">Nama Mata Pelajaran</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Guru Pengampu</th>
                        <th class="py-3 px-4 text-center">Jumlah Soal</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($subjects as $idx => $sb)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3 px-4 text-center font-medium">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-blue-700 dark:text-blue-400">{{ $sb->code }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $sb->name }}</td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">{{ $sb->category }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200">
                                {{ $sb->teacher ? $sb->teacher->name : 'Belum Ditugaskan' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                {{ $sb->questions->count() }} Butir
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('admin.master-data.subject.delete', $sb->id) }}" method="POST" onsubmit="return confirm('Hapus mata pelajaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="interactive-click px-2 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 rounded text-[11px] font-semibold border border-rose-200 dark:border-rose-800 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400 dark:text-slate-500">Belum ada mata pelajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Mapel -->
    <div id="modalTambahMapel" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base">Tambah Mata Pelajaran</h3>
                <button onclick="document.getElementById('modalTambahMapel').classList.add('hidden')" class="interactive-click text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.master-data.subject.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Mapel</label>
                    <input type="text" name="code" required placeholder="Contoh: BIO-01" 
                        class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg uppercase font-mono text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Mata Pelajaran</label>
                    <input type="text" name="name" required placeholder="Contoh: Biologi" 
                        class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                        <option value="Mata Pelajaran Wajib">Mata Pelajaran Wajib</option>
                        <option value="Mata Pelajaran Peminatan">Mata Pelajaran Peminatan</option>
                        <option value="Muatan Lokal">Muatan Lokal</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Guru Pengampu</label>
                    <select name="teacher_id" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                        <option value="">-- Pilih Guru Pengampu --</option>
                        @foreach($teachers as $tc)
                            <option value="{{ $tc->id }}">{{ $tc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahMapel').classList.add('hidden')"
                        class="interactive-click px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                        class="interactive-click px-5 py-2 bg-[#1e73be] hover:bg-[#155d9b] text-white rounded-lg font-bold shadow transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
