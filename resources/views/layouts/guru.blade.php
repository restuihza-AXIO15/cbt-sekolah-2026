<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Guru - CBT 2026')</title>

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
            background-color: #0284c7;
            color: #ffffff !important;
            font-weight: 600;
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
    </style>
</head>
<body class="min-h-screen flex text-slate-800 dark:text-slate-100 bg-slate-100 dark:bg-[#0b1120] transition-colors duration-300">

    <!-- Sidebar Guru -->
    <aside class="w-64 bg-[#0f172a] text-slate-300 flex flex-col shrink-0 min-h-screen border-r border-slate-800">
        <div class="p-5 border-b border-slate-800 flex items-center space-x-3">
            <img src="{{ asset('assets/tutwuri.svg') }}" alt="Tut Wuri Handayani" class="w-10 h-10 object-contain">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">PORTAL GURU</h2>
                <p class="text-[10px] text-sky-400 font-medium">Bank Soal & Rekap Nilai CBT</p>
            </div>
        </div>

        <!-- Profil Guru -->
        <div class="px-5 py-3 border-b border-slate-800 bg-slate-900/60 flex items-center space-x-3">
            <div class="w-8 h-8 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-xs">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <h4 class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                <p class="text-[10px] text-slate-400">Guru Mata Pelajaran</p>
            </div>
        </div>

        <!-- Menu Guru (Sesuai Batasan Khusus Permintaan User) -->
        <nav class="flex-1 px-3 py-4 space-y-1 text-xs">
            <div class="px-3 pt-1 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">MENU GURU</div>

            <a href="{{ route('guru.dashboard') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('guru.dashboard') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-house w-4 text-center"></i>
                <span>Beranda Guru</span>
            </a>

            <!-- 1. Menambahkan Soal (Bank Soal) -->
            <a href="{{ route('guru.bank-soal') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('guru.bank-soal*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-file-pen w-4 text-center text-amber-400"></i>
                <span>Bank Soal (Buat Soal)</span>
            </a>

            <!-- 2. Mengambil Nilai Jika Asesmen Selesai -->
            <a href="{{ route('guru.rekap-nilai') }}" 
               class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('guru.rekap-nilai*') ? 'sidebar-active' : '' }}">
                <i class="fa-solid fa-chart-line w-4 text-center text-emerald-400"></i>
                <span>Rekap Nilai Siswa</span>
            </a>
        </nav>

        <div class="p-3 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg text-xs font-semibold flex items-center justify-center space-x-2 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar Portal</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100 dark:bg-[#0b1120] transition-colors duration-300">
        <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-3 flex items-center justify-between shadow-sm transition-colors duration-300">
            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                {{ $setting->school_name ?? 'SMA NEGERI 1 UNGGULAN' }} &bull; Sistem Asesmen CBT Terintegrasi
            </div>
            <div class="flex items-center space-x-2.5">
                <!-- Dark / Light Mode Toggle Button -->
                <button type="button" 
                    onclick="toggleCBTTheme(this)" 
                    id="cbtThemeToggleBtn"
                    title="Ubah Mode Tampilan (Terang / Gelap)"
                    class="interactive-click relative flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 border border-slate-200 dark:border-slate-700 shadow-sm transition-all focus:outline-none">
                    <i id="cbtThemeIcon" class="fa-solid fa-moon text-xs"></i>
                </button>

                <a href="{{ route('landing') }}" target="_blank" class="interactive-click px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-globe text-xs"></i>
                    <span>Halaman Siswa</span>
                </a>
            </div>
        </header>

        <main class="flex-1 p-6 overflow-y-auto bg-slate-100 dark:bg-[#0b1120] transition-colors duration-300">
            @if(session('success'))
                <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-500 rounded-r-lg text-xs text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="interactive-click text-emerald-500 hover:text-emerald-800 dark:hover:text-emerald-300">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 bg-red-50 dark:bg-red-950/40 border-l-4 border-red-500 rounded-r-lg text-xs text-red-800 dark:text-red-300 flex items-center justify-between shadow-sm">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="interactive-click text-red-500 hover:text-red-800 dark:hover:text-red-300">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Universal CBT Theme & Professional Icon Animations Script -->
    <script>
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
