@extends('layouts.pusmendik')

@section('title', 'Simulasi TKA - Pusat Asesmen Pendidikan')

@section('content')
<div class="relative w-full max-w-md my-auto group animate-card-entrance">
    <!-- Multi-Layered Soft Ambient Background Shadows -->
    <div class="absolute -inset-3 bg-gradient-to-r from-blue-600/20 via-sky-400/25 to-indigo-600/20 rounded-3xl blur-2xl opacity-75 group-hover:opacity-95 transition duration-700 -z-10 pointer-events-none"></div>
    <div class="absolute -inset-0.5 bg-gradient-to-b from-blue-500/15 to-transparent rounded-2xl blur-md -z-10 pointer-events-none"></div>

    <div class="relative w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-[0_20px_50px_rgba(15,23,42,0.12),0_0_35px_rgba(30,115,190,0.15)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.5),0_0_35px_rgba(30,115,190,0.25)] border border-white/80 dark:border-slate-800 p-8 my-auto transform transition-all duration-300">
        <!-- Blue Graduation Cap Icon Circle (Sesuai Gambar 1) -->
        <div class="flex justify-center mb-5">
            <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-[#1e73be] to-sky-500 flex items-center justify-center shadow-lg shadow-blue-500/30 text-white text-3xl transition-transform hover:scale-105 duration-300">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
        </div>

        <!-- Title & Subtitle -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">Simulasi TKA</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pilih jenjang dan mata pelajaran untuk memulai simulasi</p>
        </div>

        <form action="{{ route('login') }}" method="GET" class="space-y-4">
            <!-- Jenjang Pendidikan (Sesuai Gambar 1) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 flex items-center space-x-1.5">
                    <i class="fa-solid fa-layer-group text-[#1e73be] dark:text-sky-400"></i>
                    <span>Jenjang Pendidikan:</span>
                </label>
                <input type="text" readonly value="SMA/MA/SMK/MAK/Sederajat" 
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 font-medium focus:outline-none focus:ring-2 focus:ring-[#1e73be] shadow-sm">
            </div>

            <!-- Jenis Mata Pelajaran (Sesuai Gambar 1) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 flex items-center space-x-1.5">
                    <i class="fa-solid fa-table-list text-[#1e73be] dark:text-sky-400"></i>
                    <span>Jenis Mata Pelajaran:</span>
                </label>
                <input type="text" readonly value="Mata Pelajaran Wajib" 
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 font-medium focus:outline-none focus:ring-2 focus:ring-[#1e73be] shadow-sm">
            </div>

            <!-- Mata Pelajaran Dropdown (Sesuai Gambar 1) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 flex items-center space-x-1.5">
                    <i class="fa-solid fa-book text-[#1e73be] dark:text-sky-400"></i>
                    <span>Mata Pelajaran:</span>
                </label>
                <div class="relative">
                    <select name="subject" class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 font-medium focus:outline-none focus:ring-2 focus:ring-[#1e73be] appearance-none pr-10 cursor-pointer shadow-sm">
                        <option value="">Pilih mata pelajaran...</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ $sub->code === 'BING-01' ? 'selected' : '' }}>
                                {{ $sub->name }} ({{ $sub->code }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Button: Mulai Simulasi (Sesuai Gambar 1) -->
            <div class="pt-3">
                <button type="submit" 
                    class="interactive-click w-full py-3 px-4 bg-gradient-to-r from-[#1e73be] to-[#155d9b] hover:from-[#1967ab] hover:to-[#0f4a80] text-white font-semibold text-sm rounded-xl shadow-[0_10px_20px_-3px_rgba(30,115,190,0.35)] hover:shadow-[0_14px_28px_-4px_rgba(30,115,190,0.45)] transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-play text-xs"></i>
                    <span>Mulai Simulasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
