@extends('layouts.admin')

@section('title', 'Manajemen Pengawasan (Proctoring) - Admin CBT')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Manajemen Pengawasan (Proctoring)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pemantauan peserta real-time, deteksi pelanggaran kecurangan, reset sesi siswa, dan pembukaan kunci ujian.
            </p>
        </div>

        <button onclick="location.reload()" 
            class="interactive-click px-3.5 py-2 bg-slate-700 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-1.5 self-start">
            <i class="fa-solid fa-arrows-rotate"></i>
            <span>Segarkan Pemantauan</span>
        </button>
    </div>

    <!-- Active Exam Banner -->
    @if($activeExam)
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-blue-200 dark:border-slate-700 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-3 transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-sky-300 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wide">
                        Sesi Ujian Aktif: {{ $activeExam->title }}
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Mapel: {{ $activeExam->subject->name ?? '-' }} | Durasi: {{ $activeExam->duration_minutes }} Menit | Toleransi Curang: {{ $activeExam->max_violations }} Kali
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 rounded-full font-bold text-[10px]">
                    {{ $attempts->where('status', 'ongoing')->count() }} Mengerjakan
                </span>
                <span class="px-2.5 py-1 bg-red-100 dark:bg-red-950/80 text-red-800 dark:text-red-300 rounded-full font-bold text-[10px]">
                    {{ $attempts->where('status', 'locked')->count() }} Terkunci
                </span>
                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-full font-bold text-[10px]">
                    {{ $attempts->where('status', 'finished')->count() }} Selesai
                </span>
            </div>
        </div>

        <!-- Filter Ruang, Sesi & Aksi Massal Proktor (Fitur Kompleks Admin) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm p-4 flex flex-col lg:flex-row items-center justify-between gap-4 transition-colors duration-300">
            <!-- Filter Ruang & Sesi -->
            <form action="{{ route('admin.monitoring') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <div class="flex items-center space-x-1.5 text-xs text-slate-500 dark:text-slate-400 font-semibold mr-1">
                    <i class="fa-solid fa-filter text-blue-500"></i>
                    <span>Filter:</span>
                </div>

                <select name="ruang" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#1e73be]">
                    <option value="">Semua Ruangan</option>
                    @foreach($availableRooms as $rm)
                        <option value="{{ $rm }}" {{ request('ruang') == $rm ? 'selected' : '' }}>{{ $rm }}</option>
                    @endforeach
                </select>

                <select name="sesi" onchange="this.form.submit()" class="text-xs bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-[#1e73be]">
                    <option value="">Semua Sesi</option>
                    @foreach($availableSessions as $ss)
                        <option value="{{ $ss }}" {{ request('sesi') == $ss ? 'selected' : '' }}>{{ $ss }}</option>
                    @endforeach
                </select>

                @if(request('ruang') || request('sesi'))
                    <a href="{{ route('admin.monitoring') }}" class="interactive-click text-xs text-red-500 hover:text-red-700 px-2 py-1 font-semibold">
                        Reset Filter
                    </a>
                @endif
            </form>

            <!-- Aksi Massal Pengawas -->
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto justify-end">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Aksi Massal:</span>

                <!-- Buka Kunci Massal -->
                <form action="{{ route('admin.monitoring.bulk-action') }}" method="POST" onsubmit="return confirm('Buka kunci semua peserta yang terkunci saat ini?')">
                    @csrf
                    <input type="hidden" name="action_type" value="unlock_all">
                    <input type="hidden" name="exam_id" value="{{ $activeExam->id }}">
                    <input type="hidden" name="room" value="{{ request('ruang') }}">
                    <button type="submit" 
                        class="interactive-click px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1.5"
                        title="Buka semua peserta yang terkunci karena pelanggaran">
                        <i class="fa-solid fa-lock-open text-[11px]"></i>
                        <span>Buka Kunci Massal</span>
                    </button>
                </form>

                <!-- Selesaikan Massal -->
                <form action="{{ route('admin.monitoring.bulk-action') }}" method="POST" onsubmit="return confirm('Paksa selesai ujian untuk seluruh peserta yang masih mengerjakan?')">
                    @csrf
                    <input type="hidden" name="action_type" value="finish_all">
                    <input type="hidden" name="exam_id" value="{{ $activeExam->id }}">
                    <input type="hidden" name="room" value="{{ request('ruang') }}">
                    <button type="submit" 
                        class="interactive-click px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1.5"
                        title="Paksa selesai dan hitung nilai untuk semua peserta aktif">
                        <i class="fa-solid fa-flag-checkered text-[11px]"></i>
                        <span>Selesaikan Massal</span>
                    </button>
                </form>

                <!-- Reset Massal Ruangan -->
                <form action="{{ route('admin.monitoring.bulk-action') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin mereset sesi semua peserta di filter ruangan ini?')">
                    @csrf
                    <input type="hidden" name="action_type" value="reset_all">
                    <input type="hidden" name="exam_id" value="{{ $activeExam->id }}">
                    <input type="hidden" name="room" value="{{ request('ruang') }}">
                    <button type="submit" 
                        class="interactive-click px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1.5"
                        title="Reset sesi ujian untuk seluruh siswa pada ruangan/sesi terpilih">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i>
                        <span>Reset Massal</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Monitoring Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Daftar Status Peserta Ujian</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Peserta</th>
                        <th class="py-3 px-4">Kelas & Ruang</th>
                        <th class="py-3 px-4 text-center">Sisa Waktu</th>
                        <th class="py-3 px-4 text-center">Pelanggaran</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Pengawas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-200">
                    @forelse($attempts as $idx => $at)
                        @php
                            $rem = $at->remainingSeconds();
                            $h = floor($rem / 3600);
                            $m = floor(($rem % 3600) / 60);
                            $s = $rem % 60;
                            $remFormatted = sprintf('%02d:%02d:%02d', $h, $m, $s);
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition {{ $at->isLocked() ? 'bg-red-50/50 dark:bg-red-950/30' : '' }}">
                            <td class="py-3.5 px-4 text-center font-medium">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $at->user->name }}</span>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">{{ $at->user->username }} (NISN: {{ $at->user->nisn ?: '-' }})</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                <span class="font-semibold block">{{ $at->user->class_name }}</span>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500">{{ $at->user->room ?: 'Lab Komputer 1' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold {{ $rem <= 300 ? 'text-red-600 dark:text-red-400' : 'text-blue-700 dark:text-sky-400' }}">
                                {{ $at->isFinished() ? '00:00:00' : $remFormatted }}
                            </td>
                            <!-- Pelanggaran Anti-Curang (Tab switch, Blur, dll) -->
                            <td class="py-3.5 px-4 text-center">
                                @if($at->violations_count > 0)
                                    <span class="px-2 py-0.5 rounded font-bold text-[11px] {{ $at->violations_count >= 3 ? 'bg-red-600 text-white' : 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300' }}">
                                        {{ $at->violations_count }}x Peringatan
                                    </span>
                                @else
                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-[11px]">
                                        <i class="fa-solid fa-check text-[10px]"></i> Tertib
                                    </span>
                                @endif
                            </td>
                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                @if($at->isLocked())
                                    <span class="px-2.5 py-1 bg-red-600 text-white rounded font-bold text-[10px] shadow-sm animate-bounce">
                                        TERKUNCI
                                    </span>
                                @elseif($at->isFinished())
                                    <span class="px-2.5 py-1 bg-emerald-600 text-white rounded font-bold text-[10px]">
                                        SELESAI
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-blue-500 text-white rounded font-bold text-[10px]">
                                        MENGERJAKAN
                                    </span>
                                @endif
                            </td>
                            <!-- Aksi Pengawas -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    @if($at->isLocked())
                                        <!-- Buka Kunci -->
                                        <form action="{{ route('admin.monitoring.unlock', $at->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="interactive-click px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold shadow-sm transition flex items-center space-x-1" title="Buka Kunci Ujian Peserta">
                                                <i class="fa-solid fa-lock-open"></i>
                                                <span>Buka Kunci</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Reset Login / Ujian (Jika Laptop Siswa Crash) -->
                                    <form action="{{ route('admin.monitoring.reset', $at->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset sesi siswa ini? Data ujian siswa akan diulang.')">
                                        @csrf
                                        <button type="submit" class="interactive-click px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[11px] font-bold shadow-sm transition" title="Reset Login jika komputer siswa hang/mati">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            <span>Reset</span>
                                        </button>
                                    </form>

                                    @if(!$at->isFinished())
                                        <!-- Paksa Selesai -->
                                        <form action="{{ route('admin.monitoring.force-finish', $at->id) }}" method="POST" onsubmit="return confirm('Paksa selesai ujian peserta sekarang?')">
                                            @csrf
                                            <button type="submit" class="interactive-click px-2 py-1 bg-slate-700 hover:bg-slate-800 text-white rounded text-[11px] font-bold transition" title="Paksa Selesai">
                                                <i class="fa-solid fa-flag-checkered"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                Belum ada data peserta yang sedang ujian pada sesi aktif ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
