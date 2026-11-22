<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PUSMENDIK - APLIKASI ANBK / CBT UJIAN SEKOLAH')</title>
    
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
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
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
        
        /* Modern Atmospheric Background Grid & Ambient Shadows */
        .ambient-bg {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 115, 190, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.08) 0px, transparent 50%),
                radial-gradient(#cbd5e1 1.2px, transparent 1.2px);
            background-size: 100% 100%, 100% 100%, 28px 28px;
        }
        html.dark .ambient-bg {
            background-color: #0b1120;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 115, 190, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.12) 0px, transparent 50%),
                radial-gradient(#1e293b 1.2px, transparent 1.2px);
        }

        /* Floating Ambient Glow Orbs (Bayangan Latar Belakang) */
        @keyframes orbFloat1 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.55; }
            50% { transform: translate(30px, -25px) scale(1.12); opacity: 0.8; }
        }
        @keyframes orbFloat2 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.45; }
            50% { transform: translate(-35px, 20px) scale(1.08); opacity: 0.7; }
        }
        .orb-1 {
            animation: orbFloat1 9s ease-in-out infinite;
        }
        .orb-2 {
            animation: orbFloat2 12s ease-in-out infinite;
        }

        /* Smooth Card Entrance Animation */
        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(28px) scale(0.96);
                filter: blur(6px);
            }
            60% {
                filter: blur(0px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .animate-card-entrance {
            animation: cardEntrance 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Smooth Theme Transitions */
        html.theme-transition,
        html.theme-transition *,
        html.theme-transition *:before,
        html.theme-transition *:after {
            transition: background-color 0.35s ease, color 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease, fill 0.35s ease !important;
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

        /* Ripple wave on click */
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

        /* Spring bounce animation for icons when clicked */
        @keyframes iconSpring {
            0% { transform: scale(1); }
            30% { transform: scale(0.78) rotate(-12deg); }
            70% { transform: scale(1.22) rotate(6deg); }
            100% { transform: scale(1) rotate(0deg); }
        }
        .animate-icon-spring {
            animation: iconSpring 0.42s cubic-bezier(0.34, 1.56, 0.64, 1) forwards !important;
        }

        /* Theme Toggle Spin Animation */
        @keyframes themeSpin {
            0% { transform: rotate(0deg) scale(0.65); opacity: 0.3; }
            50% { transform: rotate(190deg) scale(1.28); opacity: 0.9; }
            100% { transform: rotate(360deg) scale(1); opacity: 1; }
        }
        .animate-theme-spin {
            animation: themeSpin 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Micro-Transitions */
        button, a, input, select {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .shadow-glow {
            box-shadow: 0 20px 40px -15px rgba(30, 115, 190, 0.22), 0 0 25px -5px rgba(30, 115, 190, 0.12);
        }
        .shadow-glow-lg {
            box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.18), 0 0 35px -5px rgba(30, 115, 190, 0.18);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col ambient-bg select-none text-slate-800 dark:text-slate-100 antialiased relative overflow-x-hidden transition-colors duration-300">

    <!-- Atmospheric Background Shadows & Ambient Glows (Latarnya Kasih Bayangan Mulus) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <!-- Bayangan Halus Kiri Atas (Top Left Ambient Blue Shadow) -->
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-blue-400/20 dark:bg-blue-600/15 blur-[90px] orb-1"></div>
        <!-- Bayangan Halus Tengah Belakang Card (Center Drop Shadow Aura) -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] rounded-full bg-gradient-to-tr from-blue-500/15 via-sky-400/10 to-transparent dark:from-blue-600/20 dark:via-indigo-500/10 blur-[110px]"></div>
        <!-- Bayangan Halus Kanan Bawah (Bottom Right Soft Shadow) -->
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-sky-400/15 dark:bg-indigo-500/15 blur-[90px] orb-2"></div>
    </div>

    <!-- Top Blue Header Banner (Sesuai Gambar 1 - 5) -->
    <header class="bg-[#1e73be] dark:bg-slate-900 text-white py-3 px-4 sm:px-8 shadow-md dark:border-b dark:border-slate-800/80 transition-colors duration-300 relative z-20">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Brand / Logo -->
            <div class="flex items-center space-x-3">
                <img src="{{ $setting->getLogoUrl() }}" alt="Logo Sekolah" class="w-10 h-10 object-contain drop-shadow transition-transform hover:scale-105 duration-200">
                <div>
                    <h1 class="text-base sm:text-lg font-bold tracking-wide uppercase leading-tight">PUSMENDIK</h1>
                    <p class="text-xs text-blue-100 dark:text-slate-400 font-medium">APLIKASI ANBK - {{ $setting->school_name ?? 'CBT UJIAN SEKOLAH' }}</p>
                </div>
            </div>

            <!-- Right Profile / Status Pill & Dark Mode Toggle -->
            <div class="flex items-center space-x-2.5 sm:space-x-3">
                
                <!-- Dark / Light Mode Toggle Button dengan Animasi Mulus & Ikon Berputar -->
                <button type="button" 
                    onclick="toggleCBTTheme(this)" 
                    id="cbtThemeToggleBtn"
                    title="Ubah Mode Tampilan (Terang / Gelap)"
                    class="interactive-click relative flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/15 hover:bg-white/25 dark:bg-slate-800 dark:hover:bg-slate-700 text-white dark:text-amber-400 border border-white/20 dark:border-slate-700 shadow-sm backdrop-blur-md transition-all focus:outline-none">
                    <i id="cbtThemeIcon" class="fa-solid fa-moon text-xs sm:text-sm"></i>
                </button>

                @auth
                    <div class="flex items-center space-x-2 bg-blue-700/60 dark:bg-slate-800/80 backdrop-blur-sm border border-blue-400/40 dark:border-slate-700 text-white px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium">
                        <span>{{ Auth::user()->username }} - {{ strtoupper(Auth::user()->name) }}</span>
                        <div class="w-6 h-6 rounded-full bg-white dark:bg-blue-600 text-[#1e73be] dark:text-white flex items-center justify-center text-xs">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar" class="interactive-click bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs transition shadow">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="interactive-click bg-white/20 hover:bg-white/30 dark:bg-blue-600 dark:hover:bg-blue-700 text-white border border-white/40 dark:border-blue-500 px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Login CBT</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col justify-center items-center p-4 sm:p-6 relative z-10">
        @if(session('success') && !request()->routeIs('login'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const isDark = document.documentElement.classList.contains('dark');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Berhasil',
                        text: "{{ session('success') }}",
                        timer: 3500,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        background: isDark ? '#0f172a' : '#ffffff',
                        color: isDark ? '#f1f5f9' : '#0f172a',
                        customClass: {
                            popup: 'rounded-xl shadow-xl border border-slate-200 dark:border-slate-800'
                        }
                    });
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const isDark = document.documentElement.classList.contains('dark');
                    Swal.fire({
                        icon: 'error',
                        title: 'Perhatian',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#1e73be',
                        background: isDark ? '#0f172a' : '#ffffff',
                        color: isDark ? '#f1f5f9' : '#0f172a',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800'
                        }
                    });
                });
            </script>
        @endif

        @yield('content')
    </main>

    <!-- Footer (Sesuai Gambar 1) -->
    <footer class="py-4 text-center text-xs text-slate-500 dark:text-slate-400 relative z-10 transition-colors duration-300">
        Copyright © 2026 Pusmendik & {{ $setting->school_name ?? 'SMAN 1 UNGGULAN' }}. All rights reserved.
    </footer>

    <!-- Universal CBT Theme & Professional Icon Animations Script -->
    <script>
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const icons = document.querySelectorAll('#cbtThemeIcon, .theme-toggle-icon');
            icons.forEach(icon => {
                icon.classList.remove('animate-theme-spin');
                void icon.offsetWidth; // Trigger reflow for smooth animation
                if (isDark) {
                    icon.className = 'fa-solid fa-sun text-amber-400 text-xs sm:text-sm animate-theme-spin';
                } else {
                    icon.className = 'fa-solid fa-moon text-sky-200 dark:text-sky-300 text-xs sm:text-sm animate-theme-spin';
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

        // Initialize icon state on load
        document.addEventListener('DOMContentLoaded', updateThemeIcons);

        // Professional Spring Bounce & Ripple Animation on Click for all interactive icons/buttons
        document.addEventListener('click', function(e) {
            const target = e.target.closest('.interactive-click, button, .clickable-icon, a, .option-radio-card');
            if (!target) return;

            // Trigger spring bounce on the icon inside target if present
            const icon = target.querySelector('i.fa-solid, i.fa-regular, i.fa-brands, svg') || (target.tagName.toLowerCase() === 'i' ? target : null);
            if (icon && !icon.classList.contains('no-spring')) {
                icon.classList.remove('animate-icon-spring');
                void icon.offsetWidth;
                icon.classList.add('animate-icon-spring');
            }

            // Material-style ripple wave
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
