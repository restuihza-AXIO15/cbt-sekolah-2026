@extends('layouts.admin')

@section('title', 'Data Peserta Ujian - Admin CBT')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Data Peserta (Siswa)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Kelola data akun siswa, kelas, ruang ujian, dan cetak kartu ujian resmi.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Tombol Cetak Kartu Peserta (Sesuai Permintaan User) -->
            <a href="{{ route('admin.cetak-kartu') }}" target="_blank"
                class="interactive-click px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Kartu Peserta</span>
            </a>

            <button onclick="document.getElementById('modalTambahSiswa').classList.remove('hidden')" 
                class="interactive-click px-4 py-2 bg-[#1e73be] hover:bg-[#155d9b] text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Siswa</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.peserta') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <select name="kelas" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#1e73be]">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                    <option value="{{ $c }}" {{ request('kelas') == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>

            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN / username..." 
                    class="w-full text-xs pl-8 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-[#1e73be]">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            <button type="submit" class="interactive-click px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-semibold">Cari</button>
        </form>

        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
            Total Siswa: {{ $students->total() }} Peserta
        </span>
    </div>

    <!-- Table of Students -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Username & NISN</th>
                        <th class="py-3 px-4 text-center">L/P</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Ruang & Sesi</th>
                        <th class="py-3 px-4 text-center">Password Login</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($students as $idx => $st)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3 px-4 text-center font-medium">{{ $students->firstItem() + $idx }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $st->name }}
                            </td>
                            <td class="py-3 px-4 font-mono">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $st->username }}</span>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 block">NISN: {{ $st->nisn ?: '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold {{ $st->gender == 'L' ? 'text-blue-600 dark:text-blue-400' : 'text-pink-600 dark:text-pink-400' }}">
                                {{ $st->gender }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-700 dark:text-slate-300">
                                {{ $st->class_name ?: '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                <span class="block font-medium">{{ $st->room ?: 'Lab Komputer 1' }}</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ $st->session_name ?: 'Sesi 1' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-blue-700 dark:text-blue-400">
                                {{ $st->plain_password ?: 'pass123' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('admin.cetak-kartu') }}?student_id={{ $st->id }}" target="_blank" 
                                       class="interactive-click px-2 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded text-[11px] font-semibold border border-emerald-200 dark:border-emerald-800 transition" title="Cetak Kartu Siswa Ini">
                                        <i class="fa-solid fa-address-card"></i>
                                    </a>
                                    <form action="{{ route('admin.peserta.delete', $st->id) }}" method="POST" onsubmit="return confirm('Hapus siswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="interactive-click px-2 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 rounded text-[11px] font-semibold border border-rose-200 dark:border-rose-800 transition" title="Hapus Siswa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 dark:text-slate-500">Data siswa tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $students->withQueryString()->links() }}
        </div>
    </div>

    <!-- Modal Tambah Siswa -->
    <div id="modalTambahSiswa" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base">Tambah Data Siswa Baru</h3>
                <button onclick="document.getElementById('modalTambahSiswa').classList.add('hidden')" class="interactive-click text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.peserta.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Siswa</label>
                    <input type="text" name="name" required placeholder="Contoh: Muhammad Rizky" 
                        class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Username Login</label>
                        <input type="text" name="username" required placeholder="Contoh: 25110025" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Password Login</label>
                        <input type="text" name="password" required value="pass123" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg font-mono text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                        <input type="text" name="nisn" placeholder="Contoh: 0081234567" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                            <option value="L">Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas</label>
                        <input type="text" name="class_name" required value="XII MIPA 1" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ruang</label>
                        <input type="text" name="room" value="Lab Komputer 1" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sesi</label>
                        <input type="text" name="session_name" value="Sesi 1 (07.30 - 09.30)" 
                            class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="document.getElementById('modalTambahSiswa').classList.add('hidden')"
                        class="interactive-click px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                        class="interactive-click px-5 py-2 bg-[#1e73be] hover:bg-[#155d9b] text-white rounded-lg font-bold shadow transition">
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
