@extends('layouts.pusmendik')

@section('title', 'Konfirmasi Data Peserta - PUSMENDIK')

@section('content')
<div class="w-full max-w-5xl mx-auto my-auto grid grid-cols-1 md:grid-cols-12 gap-6 items-start animate-fade-in-up">

    <!-- Left Box: Informasi Peserta & Petunjuk Masuk Ujian (Token TIDAK Ditampilkan di Layar Siswa) -->
    <div class="md:col-span-5 w-full space-y-4">
        <!-- Kartu Identitas Peserta Resmi -->
        <div class="bg-white dark:bg-slate-900/90 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-800 p-6 flex flex-col justify-between transition-colors duration-300">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#1e73be] to-blue-500 text-white flex items-center justify-center font-bold text-xl shadow-md">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded">
                        PESERTA CBT RESMI
                    </span>
                    <h4 class="font-bold text-slate-900 dark:text-white text-base leading-tight mt-1 truncate">{{ $user->name }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">NISN: {{ $user->nisn ?: '-' }}</p>
                </div>
            </div>

            <div class="pt-4 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                    <span class="text-slate-400 dark:text-slate-500">Kelas / Rombel:</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $user->class_name ?: 'XII MIPA 1' }}</span>
                </div>
                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                    <span class="text-slate-400 dark:text-slate-500">Ruang Ujian:</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $user->room ?: 'Lab Komputer 1' }}</span>
                </div>
                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                    <span class="text-slate-400 dark:text-slate-500">Sesi Ujian:</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $user->session_name ?: 'Sesi 1 (07.30 - 09.30)' }}</span>
                </div>
            </div>
        </div>

        <!-- Petunjuk Token & Ujian (Sesuai Permintaan User: Token diberikan Pengawas/Admin) -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50/50 dark:from-amber-950/30 dark:to-orange-950/20 border border-amber-200/90 dark:border-amber-800/50 rounded-2xl p-5 shadow-sm space-y-3 transition-colors duration-300">
            <div class="flex items-center space-x-2 text-amber-900 dark:text-amber-300 font-bold text-xs uppercase tracking-wide">
                <i class="fa-solid fa-bullhorn text-amber-600 dark:text-amber-400 text-sm"></i>
                <span>Petunjuk Token Ujian:</span>
            </div>
            
            <p class="text-xs text-amber-900 dark:text-amber-200 leading-relaxed">
                Token ujian <strong>TIDAK ditampilkan di layar monitor siswa</strong>. Token akan diumumkan atau dibagikan secara resmi oleh <strong>Pengawas Ruang / Admin</strong> saat ujian dimulai.
            </p>

            <div class="pt-2 border-t border-amber-200/60 dark:border-amber-800/40 text-[11px] text-amber-800 dark:text-amber-300 space-y-1.5">
                <div class="flex items-start space-x-1.5">
                    <i class="fa-solid fa-check text-amber-600 dark:text-amber-400 mt-0.5 shrink-0"></i>
                    <span>Ketikkan Nama Lengkap Anda secara manual.</span>
                </div>
                <div class="flex items-start space-x-1.5">
                    <i class="fa-solid fa-check text-amber-600 dark:text-amber-400 mt-0.5 shrink-0"></i>
                    <span>Pilih Tanggal, Bulan, dan Tahun Lahir Anda secara manual.</span>
                </div>
                <div class="flex items-start space-x-1.5">
                    <i class="fa-solid fa-check text-amber-600 dark:text-amber-400 mt-0.5 shrink-0"></i>
                    <span>Masukkan 6 karakter Token yang diumumkan pengawas.</span>
                </div>
                <div class="flex items-start space-x-1.5">
                    <i class="fa-solid fa-clock text-amber-600 dark:text-amber-400 mt-0.5 shrink-0"></i>
                    <span>Masa berlaku token adalah 10 menit sejak dirilis admin.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Card: Form Konfirmasi Data Peserta (Sesuai Gambar 3 - Input Manual) -->
    <div class="md:col-span-7 w-full bg-white dark:bg-slate-900/90 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 p-8 transition-colors duration-300">
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 tracking-tight">Konfirmasi data Peserta</h3>

        @if(session('error'))
            <div class="mb-5 p-3.5 bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 rounded-r-lg text-xs text-red-700 dark:text-red-400 flex items-center space-x-2 animate-pulse">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('siswa.konfirmasi.submit') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Username Readonly (Sesuai Gambar 3) -->
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 font-medium mb-1">Username</label>
                <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 py-1 border-b border-slate-200 dark:border-slate-700">
                    {{ $user->username }}
                </div>
            </div>

            <!-- Nama Peserta Readonly (Sesuai Gambar 3) -->
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 font-medium mb-1">Nama Peserta</label>
                <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 py-1 border-b border-slate-200 dark:border-slate-700">
                    {{ $user->name }} - PESERTA TKA
                </div>
            </div>

            <!-- Jenis Kelamin Readonly (Sesuai Gambar 3) -->
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 font-medium mb-1">Jenis Kelamin</label>
                <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 py-1 border-b border-slate-200 dark:border-slate-700">
                    {{ $user->gender == 'P' ? 'Perempuan' : 'Laki-Laki' }}
                </div>
            </div>

            <!-- Mata Ujian Readonly (Sesuai Gambar 3) -->
            <div>
                <label class="block text-xs text-slate-500 dark:text-slate-400 font-medium mb-1">Mata Ujian</label>
                <div class="text-sm font-semibold text-[#1e73be] dark:text-sky-400 py-1 border-b border-slate-200 dark:border-slate-700">
                    {{ $activeExam ? $activeExam->title : 'Belum Ada Ujian Aktif' }}
                </div>
            </div>

            <!-- Input Verifikasi Nama Peserta (INPUT MANUAL - Kosong Awal) -->
            <div class="pt-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Nama Peserta <span class="text-red-500">* (Ketikkan nama lengkap Anda secara manual)</span>
                </label>
                <input type="text" name="nama_peserta_input" required 
                    value="{{ old('nama_peserta_input', '') }}"
                    placeholder="Ketikkan Nama Peserta sesuai kartu ujian"
                    autocomplete="off"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#1e73be] dark:focus:ring-sky-500 focus:border-transparent transition shadow-sm">
            </div>

            <!-- Input Verifikasi Tanggal Lahir: Hari, Bulan, Tahun (INPUT MANUAL - Pilihan Kosong Awal) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Tanggal Lahir <span class="text-red-500">* (Pilih tanggal lahir Anda)</span>
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <select name="birth_day" required class="w-full px-2.5 py-2.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#1e73be] dark:focus:ring-sky-500 shadow-sm">
                            <option value="" disabled {{ old('birth_day') ? '' : 'selected' }}>Pilih Hari</option>
                            @for($d = 1; $d <= 31; $d++)
                                <option value="{{ $d }}" {{ old('birth_day') == $d ? 'selected' : '' }}>{{ sprintf('%02d', $d) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <select name="birth_month" required class="w-full px-2.5 py-2.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#1e73be] dark:focus:ring-sky-500 shadow-sm">
                            <option value="" disabled {{ old('birth_month') ? '' : 'selected' }}>Pilih Bulan</option>
                            @php
                                $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                            @endphp
                            @foreach($months as $mNum => $mName)
                                <option value="{{ $mNum }}" {{ old('birth_month') == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="birth_year" required class="w-full px-2.5 py-2.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#1e73be] dark:focus:ring-sky-500 shadow-sm">
                            <option value="" disabled {{ old('birth_year') ? '' : 'selected' }}>Pilih Tahun</option>
                            @for($y = 2012; $y >= 2000; $y--)
                                <option value="{{ $y }}" {{ old('birth_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Input Token (INPUT MANUAL - Kosong Awal, Diumumkan oleh Pengawas) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Token <span class="text-red-500">* (Ketikkan token yang diberikan pengawas ujian)</span>
                </label>
                <input type="text" name="token" id="tokenInput" required maxlength="10"
                    value="{{ old('token', '') }}"
                    placeholder="Ketikkan token 6 karakter di sini"
                    autocomplete="off"
                    style="text-transform: uppercase;"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg font-mono font-bold tracking-widest uppercase text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#1e73be] dark:focus:ring-sky-500 focus:border-transparent shadow-sm">
                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 block">
                    *Masa berlaku token 10 menit. Pastikan pengawas telah merilis token untuk sesi ini.
                </span>
            </div>

            <!-- Submit Button (Sesuai Gambar 3) -->
            <div class="pt-4">
                <button type="submit" 
                    class="interactive-click w-full py-3 px-4 bg-[#1e73be] hover:bg-[#155d9b] text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Submit</span>
                </button>
            </div>
        </form>
    </div>
        </form>
    </div>
</div>
@endsection
