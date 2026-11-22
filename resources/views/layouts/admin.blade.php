<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin CBT 2026 - Pusat Asesmen Pendidikan')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        pusmendik: {
                            blue: '#1e73be',
                            darkblue: '#155d9b',
                            light: '#e8f2fc',
                            accent: '#0284c7'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Immediate Theme Initialization (Anti-Flicker FOUT) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('cbt_theme');
            const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }
        html.dark body {
            background-color: #0b1120;
        }
        .sidebar-active {
            background-color: #2563eb;
            color: #ffffff !important;
            font-weight: 600;
        }
        /* Smooth Micro-Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Smooth Theme Transitions */
        html.theme-transition,
        html.theme-transition *,
        html.theme-transition *:before,
        html.theme-transition *:after {
            transition: background-color 0.35s ease, color 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease !important;
        }

        /* Professional Interactive Click & Ripple Effect */
        .interactive-click {
            position: relative;
            overflow: hidden;
            user-select: none;
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.22s ease, background-color 0.22s ease, border-color 0.22s ease;
        }
        .interactive-click:hover {
            transform: translateY(-1.5px) scale(1.02);
        }
        .interactive-click:active {
            transform: translateY(0.5px) scale(0.92) !important;
        }

        .ripple-wave {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            transform: scale(0);
            background: radial-gradient(circle, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0.1) 70%, transparent 100%);
            animation: ripplePulse 0.55s cubic-bezier(0, 0, 0.2, 1) forwards;
        }
        html.dark .ripple-wave {
            background: radial-gradient(circle, rgba(147, 197, 253, 0.4) 0%, rgba(59, 130, 246, 0.15) 70%, transparent 100%);
        }
        @keyframes ripplePulse {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(3.5); opacity: 0; }
        }

        @keyframes iconSpring {
            0% { transform: scale(1); }
            30% { transform: scale(0.78) rotate(-12deg); }
            70% { transform: scale(1.22) rotate(6deg); }
            100% { transform: scale(1) rotate(0deg); }
        }
        .animate-icon-spring {
            animation: iconSpring 0.42s cubic-bezier(0.34, 1.56, 0.64, 1) forwards !important;
        }

        @keyframes themeSpin {
            0% { transform: rotate(0deg) scale(0.65); opacity: 0.3; }
            50% { transform: rotate(190deg) scale(1.28); opacity: 0.9; }
            100% { transform: rotate(360deg) scale(1); opacity: 1; }
        }
        .animate-theme-spin {
            animation: themeSpin 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Smooth Interactive transitions */
        button, a, input, select, textarea {
            transition: all 0.2s ease-in-out;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px -3px rgba(0, 0, 0, 0.08);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white transition-colors duration-300">

    <!-- Left Sidebar (Sesuai Gambar 6 & 7) -->
    <aside id="mainSidebar" class="w-64 bg-[#1e293b] text-slate-300 flex flex-col shrink-0 min-h-screen border-r border-slate-700 transition-all duration-300">
        <!-- Logo & Brand (Dapat Diubah melalui Menu Pengaturan) -->
        <div class="p-5 border-b border-slate-700/80 flex flex-col items-center text-center">
            <img src="{{ $setting->getLogoUrl() }}" alt="Logo Sekolah" class="w-14 h-14 object-contain mb-2 drop-shadow transition-transform hover:scale-105 duration-300">
            <h2 class="text-xs font-bold uppercase tracking-wider text-white">{{ $setting->app_title ?? 'TES KEMAMPUAN AKADEMIK' }}</h2>
            <p class="text-[10px] text-slate-400 mt-0.5 font-medium leading-tight">{{ $setting->sub_title ?? 'PUSAT ASESMEN PENDIDIKAN & CBT' }}</p>
        </div>

        <!-- Quick Action Header Buttons (Sesuai Gambar 6: Dashboard, Pengaturan, Keluar) -->
        <div class="grid grid-cols-3 border-b border-slate-700/80 bg-slate-800/60 text-center text-[10px] py-2">
            <a href="{{ route('admin.dashboard') }}" class="py-1 text-slate-300 hover:text-white transition flex flex-col items-center">
                <i class="fa-solid fa-house mb-0.5 text-xs"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.pengaturan') }}" class="py-1 text-slate-300 hover:text-white transition flex flex-col items-center border-x border-slate-700">
                <i class="fa-solid fa-gear mb-0.5 text-xs"></i>
                <span>Pengaturan</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-full py-1 text-red-400 hover:text-red-300 transition flex flex-col items-center">
                    <i class="fa-solid fa-right-from-bracket mb-0.5 text-xs"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>

        <!-- User Brief (Sesuai Gambar 7: Andi / Admin) -->
        <div class="px-5 py-3 border-b border-slate-700/80 flex items-center space-x-3 bg-slate-900/40">
            <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-sm shadow">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <h4 class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                <p class="text-[10px] text-slate-400">T.A {{ $setting->academic_year ?? '2026/2027' }} {{ $setting->semester ?? 'Ganjil' }}</p>
            </div>
        </div>

        <!-- Navigation Menu (Sesuai Gambar 6) -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-xs">
            <div class="px-3 pt-1 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">MENU UTAMA</div>

            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-gauge-high w-4 text-center"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.master-data') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.master-data*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-graduation-cap w-4 text-center"></i>
                <span>Data Master</span>
            </a>

            <a href="{{ route('admin.peserta') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.peserta*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-user-graduate w-4 text-center"></i>
                <span>Data Peserta (Siswa)</span>
            </a>

            <a href="{{ route('admin.cetak-kartu') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.cetak-kartu*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-address-card w-4 text-center text-emerald-400"></i>
                <span>Cetak Kartu Peserta</span>
            </a>

            <a href="{{ route('admin.guru') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.guru*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-chalkboard-user w-4 text-center"></i>
                <span>Manajemen Guru</span>
            </a>

            <a href="{{ route('admin.jadwal') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.jadwal*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-clock w-4 text-center text-blue-400"></i>
                <span>Administrasi Tes (Waktu)</span>
            </a>

            <a href="{{ route('admin.token') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.token*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-key w-4 text-center text-amber-400"></i>
                <span>Rilis Token Ujian (10m)</span>
            </a>

            <a href="{{ route('admin.monitoring') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.monitoring*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-shield-halved w-4 text-center text-red-400"></i>
                <span>Manajemen Pengawasan</span>
            </a>

            <!-- Rekap dan Laporan (Sesuai Gambar 6, 7 & 8) -->
            <a href="{{ route('admin.rekap-nilai') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.rekap-nilai*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-chart-column w-4 text-center text-green-400"></i>
                <span>Rekap dan Laporan Nilai</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">MANAJEMEN PENGGUNA</div>

            <a href="{{ route('admin.pengaturan') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.pengaturan*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-sliders w-4 text-center"></i>
                <span>Pengaturan Akun & Sekolah</span>
            </a>
        </nav>
    </aside>

    <!-- Right Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100 dark:bg-[#0b1120] transition-colors duration-300">
        
        <!-- Top Bar (Sesuai Gambar 7 & 8: [Keluar] yellow button, [Website] green button) -->
        <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-3 flex items-center justify-between shadow-sm sticky top-0 z-20 transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <button type="button" onclick="toggleSidebar()" class="interactive-click text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 lg:hidden">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
                    Aplikasi CBT Ujian Sekolah & Asesmen Terintegrasi 2026
                </div>
            </div>

            <!-- Top Right Action Buttons (Sesuai Gambar 7: Keluar Kuning & Website Hijau + Theme Toggle) -->
            <div class="flex items-center space-x-2.5 sm:space-x-3">
                
                <!-- Dark / Light Mode Toggle Button dengan Animasi Mulus & Ikon Berputar -->
                <button type="button" 
                    onclick="toggleCBTTheme(this)" 
                    id="cbtThemeToggleBtn"
                    title="Ubah Mode Tampilan (Terang / Gelap)"
                    class="interactive-click relative flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 border border-slate-200 dark:border-slate-700 shadow-sm transition-all focus:outline-none">
                    <i id="cbtThemeIcon" class="fa-solid fa-moon text-xs"></i>
                </button>

                <!-- Live Token Quick Display -->
                @php
                    $navToken = \App\Models\Token::where('is_active', true)->where('valid_until', '>', now())->latest()->first();
                @endphp
                @if($navToken)
                    <div class="hidden md:flex items-center space-x-1.5 px-2.5 py-1 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800/60 rounded-md text-xs font-mono font-bold text-amber-900 dark:text-amber-300 shadow-sm">
                        <i class="fa-solid fa-key text-amber-600 dark:text-amber-400 text-[10px]"></i>
                        <span>TOKEN: {{ $navToken->token_code }}</span>
                    </div>
                @endif

                <!-- Website Green Button (Sesuai Gambar 7) -->
                <a href="{{ route('landing') }}" target="_blank" 
                   class="interactive-click px-3 py-1.5 bg-[#16a34a] hover:bg-[#15803d] text-white text-xs font-semibold rounded shadow-sm transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-globe text-xs"></i>
                    <span>Website</span>
                </a>

                <!-- Keluar Yellow Button (Sesuai Gambar 7) -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                        class="interactive-click px-3 py-1.5 bg-[#eab308] hover:bg-[#ca8a04] text-white text-xs font-semibold rounded shadow-sm transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-1 p-6 overflow-y-auto bg-slate-100 dark:bg-[#0b1120] transition-colors duration-300">
            @if(session('success'))
                <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-r-lg text-xs text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-base text-emerald-600 dark:text-emerald-400"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="interactive-click text-emerald-500 hover:text-emerald-800 dark:hover:text-emerald-300 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 rounded-r-lg text-xs text-red-800 dark:text-red-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-base text-red-600 dark:text-red-400"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="interactive-click text-red-500 hover:text-red-800 dark:hover:text-red-300 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Admin Footer -->
        <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 px-6 py-3 text-xs text-slate-500 dark:text-slate-400 flex justify-between items-center transition-colors duration-300">
            <span>&copy; 2026 {{ $setting->school_name ?? 'SMA NEGERI 1 UNGGULAN' }}. All rights reserved.</span>
            <span>Versi CBT 2.6 - Build Standar ANBK Pusmendik</span>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sb = document.getElementById('mainSidebar');
            sb.classList.toggle('hidden');
        }

        // Universal CBT Theme & Professional Icon Animations Script
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const icons = document.querySelectorAll('#cbtThemeIcon, .theme-toggle-icon');
            icons.forEach(icon => {
                icon.classList.remove('animate-theme-spin');
                void icon.offsetWidth;
                if (isDark) {
                    icon.className = 'fa-solid fa-sun text-amber-400 text-xs animate-theme-spin';
                } else {
                    icon.className = 'fa-solid fa-moon text-slate-700 dark:text-amber-400 text-xs animate-theme-spin';
                }
            });
        }

        function toggleCBTTheme(btn) {
            const html = document.documentElement;
            html.classList.add('theme-transition');
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('cbt_theme', isDark ? 'dark' : 'light');
            updateThemeIcons();
            setTimeout(() => {
                html.classList.remove('theme-transition');
            }, 400);
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcons);

        // Professional Spring Bounce & Ripple Animation on Click
        document.addEventListener('click', function(e) {
            const target = e.target.closest('.interactive-click, button, .clickable-icon, a');
            if (!target) return;

            const icon = target.querySelector('i.fa-solid, i.fa-regular, i.fa-brands, svg') || (target.tagName.toLowerCase() === 'i' ? target : null);
            if (icon && !icon.classList.contains('no-spring')) {
                icon.classList.remove('animate-icon-spring');
                void icon.offsetWidth;
                icon.classList.add('animate-icon-spring');
            }

            const rect = target.getBoundingClientRect();
            if (rect.width > 0 && rect.height > 0) {
                const ripple = document.createElement('span');
                ripple.className = 'ripple-wave';
                const diameter = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = `${diameter}px`;
                ripple.style.left = `${e.clientX - rect.left - (diameter / 2)}px`;
                ripple.style.top = `${e.clientY - rect.top - (diameter / 2)}px`;
                target.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
