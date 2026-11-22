<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Peserta Ujian - {{ $setting->school_name }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS with Dark Mode Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Anti-flicker Theme Boot Script -->
    <script>
        (function() {
            try {
                const storedTheme = localStorage.getItem('cbt_theme');
                const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (storedTheme === 'dark' || (!storedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Micro-interactions: Spring bounce physics */
        @keyframes iconSpring {
            0% { transform: scale(1); }
            35% { transform: scale(0.82); }
            70% { transform: scale(1.22); }
            100% { transform: scale(1); }
        }

        .animate-icon-spring {
            animation: iconSpring 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Theme toggle 360 rotation */
        @keyframes themeSpin {
            0% { transform: rotate(0deg) scale(0.8); opacity: 0.5; }
            50% { transform: rotate(180deg) scale(1.18); }
            100% { transform: rotate(360deg) scale(1); opacity: 1; }
        }

        .animate-theme-spin {
            animation: themeSpin 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Material Radial Ripple Wave */
        .interactive-click {
            position: relative;
            overflow: hidden;
            user-select: none;
            cursor: pointer;
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s ease;
        }

        .interactive-click:active {
            transform: scale(0.975);
        }

        .ripple-wave {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripplePulse 0.5s linear forwards;
            pointer-events: none;
        }

        html.dark .ripple-wave {
            background: rgba(255, 255, 255, 0.25);
        }

        @keyframes ripplePulse {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                margin: 0;
                padding: 0;
            }
            .page-break {
                page-break-after: always;
            }
            .exam-card {
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                break-inside: avoid;
                background-color: #ffffff !important;
                color: #000000 !important;
            }
        }

        .exam-card {
            border: 1px solid #cbd5e1;
            background: #ffffff;
        }
    </style>
</head>
<body class="p-4 sm:p-8 bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100">

    <!-- Print Action Bar (Hidden on print) -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white dark:bg-slate-900 p-4 rounded-xl shadow-md border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.peserta') }}" class="interactive-click px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Data Peserta</span>
            </a>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total: {{ $students->count() }} Kartu Peserta Siap Cetak</span>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Dark / Light Mode Switcher -->
            <button id="themeToggleBtn" type="button" 
                class="interactive-click w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-amber-300 flex items-center justify-center transition border border-slate-200 dark:border-slate-700" 
                title="Beralih Mode Gelap/Terang">
                <i id="themeToggleIcon" class="fa-solid fa-moon text-sm"></i>
            </button>

            <button onclick="window.print()" class="interactive-click px-5 py-2 bg-[#1e73be] hover:bg-[#155d9b] text-white rounded-lg text-xs font-bold shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Semua Kartu (PDF/Printer)</span>
            </button>
        </div>
    </div>

    <!-- Cards Container (2 Cards Per Row) -->
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($students as $index => $st)
            <div class="exam-card rounded-xl p-5 shadow-sm flex flex-col justify-between text-slate-900 {{ ($index + 1) % 4 === 0 ? 'page-break' : '' }}">
                
                <!-- Kop Kartu Peserta -->
                <div class="border-b-2 border-slate-800 pb-3 flex items-center space-x-3">
                    <img src="{{ asset('assets/tutwuri.svg') }}" alt="Tut Wuri Handayani" class="w-12 h-12 object-contain shrink-0">
                    <div class="flex-1 text-center">
                        <h4 class="text-[11px] font-extrabold uppercase leading-tight tracking-wide">
                            KARTU PESERTA UJIAN BERBASIS KOMPUTER (CBT)
                        </h4>
                        <h3 class="text-xs font-black uppercase text-blue-900 leading-tight">
                            {{ $setting->school_name ?? 'SMA NEGERI 1 UNGGULAN' }}
                        </h3>
                        <p class="text-[9px] text-slate-500 font-medium mt-0.5 leading-none">
                            Tahun Ajaran {{ $setting->academic_year ?? '2026/2027' }} - {{ $setting->semester ?? 'Ganjil' }}
                        </p>
                    </div>
                    <img src="{{ $setting->school_logo ? asset($setting->school_logo) : asset('assets/school_logo.svg') }}" alt="Logo Sekolah" class="w-12 h-12 object-contain shrink-0">
                </div>

                <!-- Body Kartu -->
                <div class="py-3.5 flex space-x-4 items-start text-xs">
                    <!-- Foto Siswa / Placeholder -->
                    <div class="shrink-0 flex flex-col items-center">
                        <div class="w-20 h-24 border border-slate-400 bg-slate-50 rounded flex flex-col items-center justify-center text-slate-400 text-center p-1">
                            <i class="fa-solid fa-user text-2xl mb-1"></i>
                            <span class="text-[8px] font-bold">FOTO 3x4</span>
                        </div>
                        <!-- Barcode Simulation -->
                        <div class="mt-2 text-center">
                            <div class="font-mono text-[9px] font-bold tracking-widest bg-slate-100 px-1 py-0.5 border border-slate-300 rounded">
                                ||||| | |||| ||
                            </div>
                            <span class="text-[8px] text-slate-500 font-mono">{{ $st->username }}</span>
                        </div>
                    </div>

                    <!-- Detail Biodata & Kredensial Login Siswa -->
                    <div class="flex-1 space-y-1.5 leading-snug">
                        <div class="flex">
                            <span class="w-24 text-slate-500 text-[11px] shrink-0 font-medium">Nomor Peserta</span>
                            <span class="font-bold text-slate-900">: {{ $st->nisn ?: $st->username }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-slate-500 text-[11px] shrink-0 font-medium">Nama Peserta</span>
                            <span class="font-bold text-slate-900 uppercase">: {{ $st->name }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-slate-500 text-[11px] shrink-0 font-medium">Kelas / Rombel</span>
                            <span class="font-semibold text-slate-800">: {{ $st->class_name ?: 'XII MIPA 1' }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-slate-500 text-[11px] shrink-0 font-medium">Ruang / Sesi</span>
                            <span class="font-semibold text-slate-800">: {{ $st->room ?: 'Lab Komputer 1' }} / {{ $st->session_name ?: 'Sesi 1' }}</span>
                        </div>
                        
                        <!-- Kredensial Login CBT Box -->
                        <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-[11px] space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-blue-900">Username Login:</span>
                                <span class="font-mono font-extrabold text-blue-800 bg-white px-2 py-0.5 rounded border border-blue-200">{{ $st->username }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-blue-900">Password Login:</span>
                                <span class="font-mono font-extrabold text-blue-800 bg-white px-2 py-0.5 rounded border border-blue-200">{{ $st->plain_password ?: 'pass123' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Tanda Tangan -->
                <div class="border-t border-slate-300 pt-2 flex justify-between items-end text-[10px]">
                    <div class="text-[9px] text-slate-400 italic">
                        *Harap dibawa selama ujian berlangsung.
                    </div>
                    <div class="text-center font-medium">
                        <span>Kepala Sekolah,</span>
                        <div class="h-10"></div>
                        <span class="font-bold underline uppercase block">{{ $setting->headmaster_name ?? 'Drs. H. Bambang Suryono, M.Pd.' }}</span>
                        <span class="text-[9px] text-slate-500">NIP. {{ $setting->headmaster_nip ?? '19710312 199602 1 002' }}</span>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Theme Switcher & Micro-interactions Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('themeToggleBtn');
            const icon = document.getElementById('themeToggleIcon');

            function syncThemeIcon() {
                const isDark = document.documentElement.classList.contains('dark');
                if (icon) {
                    if (isDark) {
                        icon.className = 'fa-solid fa-sun text-sm text-amber-300';
                    } else {
                        icon.className = 'fa-solid fa-moon text-sm text-slate-600';
                    }
                }
            }

            syncThemeIcon();

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('cbt_theme', isDark ? 'dark' : 'light');
                    if (icon) {
                        icon.classList.remove('animate-theme-spin');
                        void icon.offsetWidth;
                        icon.classList.add('animate-theme-spin');
                    }
                    syncThemeIcon();
                });
            }

            // Universal Click Spring and Ripple Handler
            document.addEventListener('click', (e) => {
                const clickable = e.target.closest('.interactive-click, button, a');
                if (!clickable) return;

                const icon = clickable.querySelector('i');
                if (icon && !icon.classList.contains('animate-theme-spin')) {
                    icon.classList.remove('animate-icon-spring');
                    void icon.offsetWidth;
                    icon.classList.add('animate-icon-spring');
                }

                if (clickable.classList.contains('interactive-click')) {
                    const rect = clickable.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    const wave = document.createElement('span');
                    wave.classList.add('ripple-wave');
                    wave.style.width = wave.style.height = `${size}px`;
                    wave.style.left = `${x}px`;
                    wave.style.top = `${y}px`;

                    clickable.appendChild(wave);
                    setTimeout(() => wave.remove(), 550);
                }
            });
        });
    </script>

</body>
</html>
