@extends('layouts.pusmendik')

@section('title', 'Login Peserta / Pengguna - CBT PUSMENDIK')

@section('content')
<!-- Card Container dengan Animasi Mulus & Bayangan Latar Belakang (Atmospheric Shadow) -->
<div class="relative w-full max-w-md my-auto group animate-card-entrance">
    
    <!-- Multi-Layered Soft Ambient Background Shadows (Bayangan Latar Mulus) -->
    <div class="absolute -inset-3 bg-gradient-to-r from-blue-600/20 via-sky-400/25 to-indigo-600/20 rounded-3xl blur-2xl opacity-75 group-hover:opacity-95 transition duration-700 -z-10 pointer-events-none"></div>
    <div class="absolute -inset-0.5 bg-gradient-to-b from-blue-500/15 to-transparent rounded-2xl blur-md -z-10 pointer-events-none"></div>

    <!-- Main Login Card -->
    <div class="relative w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-[0_20px_50px_rgba(15,23,42,0.12),0_0_35px_rgba(30,115,190,0.15)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.5),0_0_35px_rgba(30,115,190,0.25)] border border-white/80 dark:border-slate-800 p-8 sm:p-9 transition-all duration-300">
        
        <!-- Header Text (Sesuai Gambar 2) -->
        <div class="mb-6">
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-800/60 rounded-full text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider mb-2">
                <i class="fa-solid fa-shield-halved text-[11px]"></i>
                <span>Portal Resmi Ujian CBT</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Selamat Datang</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                Silakan login dengan menggunakan username dan password yang anda miliki
            </p>
        </div>

        <!-- Banner Notifikasi Logout Estetik & Modern (Sesuai Permintaan User) -->
        @if(session('logout_success') || session('success'))
            <div id="logoutBanner" class="mb-5 overflow-hidden rounded-2xl border border-emerald-300/80 dark:border-emerald-700/60 bg-gradient-to-br from-emerald-500/15 via-teal-500/10 to-cyan-500/15 dark:from-emerald-950/50 dark:via-teal-950/40 dark:to-cyan-950/30 backdrop-blur-md p-4 shadow-[0_10px_25px_-5px_rgba(16,185,129,0.18)] dark:shadow-[0_10px_25px_-5px_rgba(0,0,0,0.4)] transition-all duration-300 transform animate-card-entrance">
                <div class="flex items-start space-x-3.5">
                    <!-- Glowing Icon Shield -->
                    <div class="relative shrink-0 mt-0.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center shadow-md shadow-emerald-500/30">
                            <i class="fa-solid fa-shield-halved text-base animate-pulse"></i>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-600 text-white text-[9px] flex items-center justify-center border-2 border-white dark:border-slate-900 shadow">
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </div>

                    <!-- Text Details -->
                    <div class="flex-1 min-w-0 pr-1">
                        <div class="flex items-center space-x-2">
                            <h4 class="text-xs font-bold text-emerald-950 dark:text-emerald-200">Logout Berhasil</h4>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-700/60">
                                Sesi Ditutup Aman
                            </span>
                        </div>
                        <p class="text-[11px] text-emerald-800/95 dark:text-emerald-300/95 mt-1 leading-relaxed">
                            {{ session('success') ?? 'Sesi ujian Anda telah ditutup secara aman. Data pengerjaan dan kredensial Anda tersimpan di server CBT.' }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button type="button" onclick="dismissLogoutBanner()" 
                        class="interactive-click text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 p-1 -mt-1 -mr-1 transition-colors rounded-lg" title="Tutup pemberitahuan">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Animated Subtle Progress Bar -->
                <div class="w-full bg-emerald-200/50 dark:bg-emerald-900/40 h-1 rounded-full mt-3 overflow-hidden">
                    <div id="logoutTimerBar" class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-[5000ms] ease-linear w-full"></div>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-4 p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 rounded-xl text-xs text-red-600 dark:text-red-400 flex items-center space-x-2 animate-pulse shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-base shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Username Input with User Icon (Sesuai Gambar 2) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Username</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 group-focus-within/input:text-[#1e73be] dark:group-focus-within/input:text-sky-400 transition-colors">
                        <i class="fa-solid fa-user text-sm"></i>
                    </div>
                    <input type="text" name="username" id="username" required
                        value="{{ old('username') }}"
                        placeholder="Ketikkan Username" 
                        autocomplete="username"
                        class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50/60 hover:bg-white focus:bg-white dark:bg-slate-800/80 dark:hover:bg-slate-800 dark:focus:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 font-medium focus:outline-none focus:ring-2 focus:ring-[#1e73be] dark:focus:ring-sky-500 focus:border-transparent focus:shadow-[0_0_0_4px_rgba(30,115,190,0.12)] transition-all duration-200">
                </div>
            </div>

            <!-- Password Input with Lock Icon & Reveal Eye (Sesuai Gambar 2) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 group-focus-within/input:text-[#1e73be] dark:group-focus-within/input:text-sky-400 transition-colors">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" name="password" id="password" required
                        placeholder="Ketikkan Password" 
                        autocomplete="current-password"
                        class="w-full pl-10 pr-10 py-2.5 text-sm bg-slate-50/60 hover:bg-white focus:bg-white dark:bg-slate-800/80 dark:hover:bg-slate-800 dark:focus:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 font-medium focus:outline-none focus:ring-2 focus:ring-[#1e73be] dark:focus:ring-sky-500 focus:border-transparent focus:shadow-[0_0_0_4px_rgba(30,115,190,0.12)] transition-all duration-200">
                    <button type="button" onclick="togglePassword()" 
                        class="interactive-click absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none transition-colors">
                        <i class="fa-solid fa-eye text-sm" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button: Login (Sesuai Gambar 2 - Mulus & Bayangan Dalam) -->
            <div class="pt-2">
                <button type="submit" 
                    class="interactive-click w-full py-3 px-4 bg-gradient-to-r from-[#1e73be] to-[#155d9b] hover:from-[#1967ab] hover:to-[#0f4a80] text-white font-semibold text-sm rounded-xl shadow-[0_10px_20px_-3px_rgba(30,115,190,0.35)] hover:shadow-[0_14px_28px_-4px_rgba(30,115,190,0.45)] transition-all duration-200 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                    <span>Login</span>
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function dismissLogoutBanner() {
        const banner = document.getElementById('logoutBanner');
        if (banner) {
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-8px)';
            setTimeout(() => banner.remove(), 300);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const bar = document.getElementById('logoutTimerBar');
        if (bar) {
            setTimeout(() => {
                bar.style.width = '0%';
            }, 60);
            setTimeout(() => {
                dismissLogoutBanner();
            }, 5100);
        }
    });
</script>
@endpush
@endsection
