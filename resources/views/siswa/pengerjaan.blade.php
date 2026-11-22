<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengerjaan Ujian - {{ $exam->title }} - PUSMENDIK</title>

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
            background-color: #edf2f7;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        html.dark body {
            background-color: #0b1120;
        }

        /* Prevent Text Copying / Dragging */
        * {
            -webkit-touch-callout: none;
        }

        /* Custom Scrollbar for Stimulus reading */
        .stimulus-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .stimulus-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        html.dark .stimulus-scroll::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .stimulus-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        html.dark .stimulus-scroll::-webkit-scrollbar-thumb {
            background: #475569;
        }

        /* Option Radio Card Selection */
        .option-radio-card.active {
            border-color: #2563eb !important;
            background-color: #eff6ff !important;
            box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.15);
        }
        html.dark .option-radio-card.active {
            border-color: #3b82f6 !important;
            background-color: rgba(30, 58, 138, 0.35) !important;
            box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.25);
        }
        .option-radio-card.active .radio-circle {
            border-color: #2563eb !important;
            background-color: #2563eb !important;
        }
        html.dark .option-radio-card.active .radio-circle {
            border-color: #3b82f6 !important;
            background-color: #3b82f6 !important;
        }
        .option-radio-card.active .radio-circle i {
            display: block;
        }

        /* Smooth Micro-Animations */
        @keyframes paneFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .question-pane:not(.hidden) {
            animation: paneFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .option-radio-card {
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .option-radio-card:hover {
            transform: translateX(4px);
            border-color: #93c5fd;
        }
        html.dark .option-radio-card:hover {
            border-color: #3b82f6;
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

        button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        button:hover:not(:disabled) {
            transform: translateY(-1px);
        }
        button:active:not(:disabled) {
            transform: translateY(0) scale(0.98);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#edf2f7] text-slate-800 antialiased">

    <!-- Anti-Screenshot / Overlay Screen -->
    <div id="antiScreenshotOverlay" class="fixed inset-0 bg-black z-[9999] hidden flex items-center justify-center text-white text-xl font-bold">
        <span><i class="fa-solid fa-triangle-exclamation text-yellow-400 mr-2"></i> Peringatan: Dilarang Mengambil Tangkapan Layar (Screenshot)!</span>
    </div>

    <!-- Top Blue Header Banner (Sesuai Gambar 5) -->
    <header class="bg-[#1e73be] dark:bg-slate-900 text-white py-2.5 px-4 sm:px-8 shadow-md dark:border-b dark:border-slate-800 sticky top-0 z-30 transition-colors duration-300">
        <div class="max-w-[1400px] mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ $setting->getLogoUrl() }}" alt="Logo Sekolah" class="w-9 h-9 object-contain drop-shadow transition-transform hover:scale-105 duration-200">
                <div>
                    <h1 class="text-base font-bold tracking-wide uppercase leading-tight">PUSMENDIK</h1>
                    <p class="text-[11px] text-blue-100 dark:text-slate-400 font-medium">APLIKASI ANBK - {{ $setting->school_name ?? 'CBT' }}</p>
                </div>
            </div>

            <!-- Right Controls: Theme Toggle & User Badge (Sesuai Gambar 5) -->
            <div class="flex items-center space-x-2.5 sm:space-x-3">
                <!-- Dark / Light Mode Toggle Button dengan Animasi Mulus -->
                <button type="button" 
                    onclick="toggleCBTTheme(this)" 
                    id="cbtThemeToggleBtn"
                    title="Ubah Mode Tampilan (Terang / Gelap)"
                    class="interactive-click relative flex items-center justify-center w-8 h-8 rounded-xl bg-white/15 hover:bg-white/25 dark:bg-slate-800 dark:hover:bg-slate-700 text-white dark:text-amber-400 border border-white/20 dark:border-slate-700 shadow-sm backdrop-blur-md transition-all focus:outline-none">
                    <i id="cbtThemeIcon" class="fa-solid fa-moon text-xs"></i>
                </button>

                <!-- User Badge -->
                <div class="flex items-center space-x-2 bg-blue-700/60 dark:bg-slate-800/80 border border-blue-400/30 dark:border-slate-700 text-white px-3 py-1 rounded-full text-xs font-medium">
                    <span>{{ $user->username }} - {{ $user->name }}</span>
                    <div class="w-5 h-5 rounded-full bg-white dark:bg-blue-600 text-[#1e73be] dark:text-white flex items-center justify-center text-[10px]">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Exam Workspace (Sesuai Gambar 5) -->
    <main class="flex-1 max-w-[1400px] w-full mx-auto p-4 sm:p-6 flex flex-col">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-slate-200/80 dark:border-slate-800 flex-1 flex flex-col overflow-hidden transition-colors duration-300">
            
            <!-- Exam Header Sub-Bar (Sesuai Gambar 5) -->
            <div class="p-4 sm:px-6 sm:py-3.5 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-wrap items-center justify-between gap-3 transition-colors duration-300">
                <!-- Left: Question Number & Font Resizer -->
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="currentQuestionLabel">
                        Soal nomor 1
                    </h2>

                    <!-- Font Resizer A A A (Sesuai Gambar 5) -->
                    <div class="flex items-center space-x-1.5 text-xs text-slate-500 dark:text-slate-400 pl-3 border-l border-slate-200 dark:border-slate-700">
                        <span class="text-[11px] mr-1">Ukuran font soal:</span>
                        <button type="button" onclick="setFontSize('sm')" class="interactive-click font-btn px-1.5 py-0.5 rounded text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Kecil">A</button>
                        <button type="button" onclick="setFontSize('base')" class="interactive-click font-btn px-1.5 py-0.5 rounded text-sm font-bold bg-blue-100 dark:bg-blue-950/70 text-[#1e73be] dark:text-sky-400 transition" title="Sedang">A</button>
                        <button type="button" onclick="setFontSize('lg')" class="interactive-click font-btn px-1.5 py-0.5 rounded text-base font-bold hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition" title="Besar">A</button>
                    </div>
                </div>

                <!-- Right: Information, Remaining Time, Daftar Soal -->
                <div class="flex items-center space-x-2.5">
                    <!-- Informasi Soal (Sesuai Gambar 5) -->
                    <button type="button" onclick="showExamInfo()" class="interactive-click px-3 py-1.5 bg-[#1e73be] hover:bg-[#155d9b] text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-1">
                        <i class="fa-solid fa-circle-info text-[11px]"></i>
                        <span>INFORMASI SOAL</span>
                    </button>

                    <!-- Sisa Waktu (Sesuai Gambar 5: Sisa Waktu : 01:14:57) -->
                    <div class="px-3.5 py-1.5 bg-white dark:bg-slate-800 border border-[#1e73be] dark:border-sky-500 text-[#1e73be] dark:text-sky-400 font-bold text-xs rounded-lg shadow-sm flex items-center space-x-1.5 transition-colors duration-300">
                        <i class="fa-regular fa-clock"></i>
                        <span>Sisa Waktu :</span>
                        <span id="countdownTimer" class="font-mono text-sm tracking-wider">--:--:--</span>
                    </div>

                    <!-- Daftar Soal Button (Sesuai Gambar 5: Daftar Soal ▦) -->
                    <button type="button" onclick="toggleDaftarSoal()" class="interactive-click px-3.5 py-1.5 bg-[#1e73be] hover:bg-[#155d9b] text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                        <span>Daftar Soal</span>
                        <i class="fa-solid fa-table-cells-large text-[13px]"></i>
                    </button>
                </div>
            </div>

            <!-- Subtitle Mapel (Sesuai Gambar 5: Bahasa Inggris (Paket 1)) -->
            <div class="px-6 py-2 bg-slate-50/80 dark:bg-slate-850 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-medium transition-colors duration-300">
                <span class="text-[#1e73be] dark:text-sky-400 font-semibold">{{ $exam->title }}</span>
                <span id="answeredCounter" class="bg-slate-200/80 dark:bg-slate-800 px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                    Dijawab: 0 / {{ count($orderedQuestions) }}
                </span>
            </div>
                </span>
            </div>

            <!-- Content Area: Split Two Columns (Sesuai Gambar 5) -->
            <div class="flex-1 p-6 overflow-y-auto" id="questionContainer">
                @foreach($orderedQuestions as $index => $q)
                    @php
                        $savedAns = $answers[$q->id] ?? null;
                        $chosenOpt = $savedAns ? $savedAns->selected_option : null;
                        $isDoubt = $savedAns ? $savedAns->is_doubtful : false;
                    @endphp
                    <div class="question-pane {{ $index === 0 ? '' : 'hidden' }}" id="question-{{ $index }}" 
                         data-qid="{{ $q->id }}" 
                         data-index="{{ $index }}"
                         data-chosen="{{ $chosenOpt }}"
                         data-doubt="{{ $isDoubt ? '1' : '0' }}">

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start h-full">
                            <!-- Left Column: Stimulus / Reading Passage (Sesuai Gambar 5) -->
                            <div class="lg:col-span-6 bg-slate-50/70 dark:bg-slate-800/70 rounded-xl p-5 border border-slate-200 dark:border-slate-700 stimulus-scroll max-h-[58vh] overflow-y-auto transition-colors duration-300">
                                @if($q->stimulus)
                                    <div class="stimulus-text text-slate-700 dark:text-slate-200 leading-relaxed font-normal whitespace-pre-line text-sm question-font-target">
                                        {{ $q->stimulus }}
                                    </div>
                                @else
                                    <div class="text-xs text-slate-400 dark:text-slate-500 italic flex items-center space-x-1.5">
                                        <i class="fa-solid fa-file-lines"></i>
                                        <span>Soal mandiri tanpa stimulus bacaan panjang.</span>
                                    </div>
                                @endif

                                @if($q->question_image)
                                    <div class="mt-4">
                                        <img src="{{ asset($q->question_image) }}" alt="Gambar Soal" class="max-h-60 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                                    </div>
                                @endif

                                @if($q->audio_file)
                                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 rounded-lg">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-1 flex items-center space-x-1">
                                            <i class="fa-solid fa-headphones"></i>
                                            <span>Audio Listening:</span>
                                        </p>
                                        <audio controls class="w-full h-8">
                                            <source src="{{ asset($q->audio_file) }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column: Question Text & Multiple Choice Options (Sesuai Gambar 5) -->
                            <div class="lg:col-span-6 flex flex-col justify-between max-h-[58vh] overflow-y-auto pr-2">
                                <div>
                                    <h3 class="text-slate-900 dark:text-white font-semibold mb-5 leading-relaxed text-sm question-font-target">
                                        {{ $q->question_text }}
                                    </h3>

                                    <!-- Option Radio Buttons: A, B, C, D, E (Sesuai Gambar 5) -->
                                    <div class="space-y-3">
                                        @foreach(['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d, 'E' => $q->option_e] as $optKey => $optVal)
                                            @if($optVal !== null && $optVal !== '')
                                                <div onclick="selectOption({{ $index }}, '{{ $optKey }}')" 
                                                     id="opt-card-{{ $index }}-{{ $optKey }}"
                                                     class="interactive-click option-radio-card {{ $chosenOpt === $optKey ? 'active' : '' }} flex items-start space-x-3.5 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/90 hover:bg-slate-50 dark:hover:bg-slate-750 cursor-pointer transition shadow-sm">
                                                    <!-- Circle Radio (Sesuai Gambar 5) -->
                                                    <div class="radio-circle w-5 h-5 rounded-full border-2 border-slate-400 dark:border-slate-500 flex items-center justify-center shrink-0 mt-0.5">
                                                        <i class="fa-solid fa-check text-white text-[10px] {{ $chosenOpt === $optKey ? '' : 'hidden' }}"></i>
                                                    </div>
                                                    <div class="text-xs font-bold text-slate-600 dark:text-slate-400 shrink-0 mt-0.5">
                                                        {{ $optKey }}.
                                                    </div>
                                                    <div class="text-slate-800 dark:text-slate-200 text-xs sm:text-sm font-normal leading-relaxed question-font-target">
                                                        {{ $optVal }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Bottom Navigation Bar (Sesuai Gambar 5: Soal sebelumnya, Ragu-ragu, Soal berikutnya) -->
            <div class="p-4 sm:px-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between transition-colors duration-300">
                <!-- Tombol Soal Sebelumnya (Coral / Red Sesuai Gambar 5) -->
                <button type="button" id="btnPrev" onclick="navigateQuestion(-1)" 
                    class="interactive-click px-5 py-2.5 bg-[#ef4444] hover:bg-[#dc2626] text-white text-xs sm:text-sm font-semibold rounded-full shadow transition flex items-center space-x-2 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                    <span>Soal sebelumnya</span>
                </button>

                <!-- Tombol Ragu-ragu (Kuning Sesuai Gambar 5) -->
                <button type="button" id="btnDoubt" onclick="toggleCurrentDoubtful()" 
                    class="interactive-click px-5 py-2.5 bg-[#eab308] hover:bg-[#ca8a04] text-white text-xs sm:text-sm font-semibold rounded-full shadow transition flex items-center space-x-2">
                    <input type="checkbox" id="doubtCheckbox" class="pointer-events-none rounded text-yellow-600 focus:ring-0">
                    <span>Ragu-ragu</span>
                </button>

                <!-- Tombol Soal Berikutnya / Selesai (Biru Sesuai Gambar 5) -->
                <div>
                    <button type="button" id="btnNext" onclick="navigateQuestion(1)" 
                        class="interactive-click px-5 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs sm:text-sm font-semibold rounded-full shadow transition flex items-center space-x-2">
                        <span>Soal berikutnya</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>

                    <button type="button" id="btnFinish" onclick="openFinishModal()" 
                        class="interactive-click hidden px-5 py-2.5 bg-[#16a34a] hover:bg-[#15803d] text-white text-xs sm:text-sm font-semibold rounded-full shadow transition flex items-center space-x-2">
                        <i class="fa-solid fa-check-double text-xs"></i>
                        <span>Selesai Tes</span>
                    </button>
                </div>
            </div>

        </div>
    </main>

    <!-- Slide-Out / Drawer Daftar Soal (Sesuai Gambar 5) -->
    <div id="drawerDaftarSoal" class="fixed inset-y-0 right-0 w-80 sm:w-96 bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <div class="p-4 bg-[#1e73be] dark:bg-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2 font-bold text-sm">
                <i class="fa-solid fa-table-cells-large"></i>
                <span>DAFTAR NOMOR SOAL</span>
            </div>
            <button type="button" onclick="toggleDaftarSoal()" class="interactive-click text-white hover:text-slate-200 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-4 bg-slate-50 dark:bg-slate-850 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-xs flex justify-around text-slate-600 dark:text-slate-300 font-medium">
            <div class="flex items-center space-x-1.5">
                <div class="w-4 h-4 rounded bg-[#1e73be] text-white flex items-center justify-center text-[10px] font-bold">1</div>
                <span>Dijawab</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <div class="w-4 h-4 rounded bg-[#eab308] text-white flex items-center justify-center text-[10px] font-bold">1</div>
                <span>Ragu-ragu</span>
            </div>
            <div class="flex items-center space-x-1.5">
                <div class="w-4 h-4 rounded bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px] font-bold">1</div>
                <span>Belum</span>
            </div>
        </div>

        <div class="flex-1 p-5 overflow-y-auto">
            <div class="grid grid-cols-5 gap-2.5">
                @foreach($orderedQuestions as $idx => $q)
                    @php
                        $isAns = isset($answers[$q->id]) && !empty($answers[$q->id]->selected_option);
                        $isDbt = isset($answers[$q->id]) && $answers[$q->id]->is_doubtful;
                    @endphp
                    <button type="button" onclick="jumpToQuestion({{ $idx }})" id="nav-btn-{{ $idx }}"
                        class="interactive-click question-nav-tile h-11 rounded-lg border font-bold text-xs flex flex-col items-center justify-center transition shadow-sm
                        {{ $isDbt ? 'bg-[#eab308] text-white border-yellow-500' : ($isAns ? 'bg-[#1e73be] text-white border-blue-600' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700') }}">
                        <span>{{ $idx + 1 }}</span>
                        <span id="nav-choice-{{ $idx }}" class="text-[9px] font-normal uppercase">
                            {{ $isAns ? $answers[$q->id]->selected_option : '-' }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
            <button type="button" onclick="openFinishModal()" class="interactive-click w-full py-2.5 bg-[#16a34a] hover:bg-[#15803d] text-white text-xs font-bold rounded-lg shadow transition flex items-center justify-center space-x-1.5">
                <i class="fa-solid fa-flag-checkered"></i>
                <span>Konfirmasi Selesai Ujian</span>
            </button>
        </div>
    </div>

    <!-- Backdrop Drawer -->
    <div id="drawerBackdrop" onclick="toggleDaftarSoal()" class="fixed inset-0 bg-black/40 z-40 hidden transition-opacity"></div>

    <!-- Hidden Form for Final Submission -->
    <form id="finishExamForm" action="{{ route('api.exam.finish') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
    </form>

    <script>
        const ATTEMPT_ID = {{ $attempt->id }};
        const TOTAL_QUESTIONS = {{ count($orderedQuestions) }};
        const MAX_VIOLATIONS = {{ $exam->max_violations ?: 3 }};
        let currentIdx = 0;
        let remainingSeconds = {{ $remainingSeconds }};
        let violationsCount = {{ $attempt->violations_count ?: 0 }};
        let isExamFinished = false;

        // CSRF Header
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Font Resizer
        function setFontSize(size) {
            const targets = document.querySelectorAll('.question-font-target');
            targets.forEach(el => {
                el.classList.remove('text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl');
                if (size === 'sm') el.classList.add('text-xs');
                else if (size === 'base') el.classList.add('text-sm');
                else if (size === 'lg') el.classList.add('text-base');
            });
        }

        // Toggle Daftar Soal Drawer
        function toggleDaftarSoal() {
            const drawer = document.getElementById('drawerDaftarSoal');
            const backdrop = document.getElementById('drawerBackdrop');
            if (drawer.classList.contains('translate-x-full')) {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Jump to Question
        function jumpToQuestion(idx) {
            document.getElementById(`question-${currentIdx}`).classList.add('hidden');
            currentIdx = idx;
            document.getElementById(`question-${currentIdx}`).classList.remove('hidden');
            updateUIState();
            toggleDaftarSoal();
        }

        // Navigate Next / Prev
        function navigateQuestion(step) {
            const target = currentIdx + step;
            if (target >= 0 && target < TOTAL_QUESTIONS) {
                document.getElementById(`question-${currentIdx}`).classList.add('hidden');
                currentIdx = target;
                document.getElementById(`question-${currentIdx}`).classList.remove('hidden');
                updateUIState();
            }
        }

        // Update UI Button states & question label
        function updateUIState() {
            document.getElementById('currentQuestionLabel').innerText = `Soal nomor ${currentIdx + 1}`;
            document.getElementById('btnPrev').disabled = (currentIdx === 0);

            const activePane = document.getElementById(`question-${currentIdx}`);
            const isDoubt = activePane.dataset.doubt === '1';
            document.getElementById('doubtCheckbox').checked = isDoubt;

            // Show Finish button on last question
            if (currentIdx === TOTAL_QUESTIONS - 1) {
                document.getElementById('btnNext').classList.add('hidden');
                document.getElementById('btnFinish').classList.remove('hidden');
            } else {
                document.getElementById('btnNext').classList.remove('hidden');
                document.getElementById('btnFinish').classList.add('hidden');
            }

            updateAnsweredCounter();
        }

        // Select Option (A, B, C, D, E) & Auto-Save to Backend API
        function selectOption(qIdx, optionKey) {
            const pane = document.getElementById(`question-${qIdx}`);
            const questionId = pane.dataset.qid;

            // Visual update
            pane.querySelectorAll('.option-radio-card').forEach(c => {
                c.classList.remove('active');
                c.querySelector('.radio-circle i').classList.add('hidden');
            });

            const activeCard = document.getElementById(`opt-card-${qIdx}-${optionKey}`);
            if (activeCard) {
                activeCard.classList.add('active');
                activeCard.querySelector('.radio-circle i').classList.remove('hidden');
            }

            pane.dataset.chosen = optionKey;

            // Update Drawer Tile
            updateDrawerTile(qIdx, optionKey, pane.dataset.doubt === '1');

            // Send to Secured Backend API
            fetch('{{ route("api.exam.save-answer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    attempt_id: ATTEMPT_ID,
                    question_id: questionId,
                    selected_option: optionKey
                })
            }).then(r => r.json()).then(data => {
                if(!data.success) {
                    console.error("Gagal menyimpan jawaban", data.message);
                }
            }).catch(e => console.error("Error saving answer:", e));

            updateAnsweredCounter();
        }

        // Toggle Doubtful (Ragu-ragu)
        function toggleCurrentDoubtful() {
            const pane = document.getElementById(`question-${currentIdx}`);
            const questionId = pane.dataset.qid;
            const currentDoubt = pane.dataset.doubt === '1';
            const newDoubt = !currentDoubt;

            pane.dataset.doubt = newDoubt ? '1' : '0';
            document.getElementById('doubtCheckbox').checked = newDoubt;

            updateDrawerTile(currentIdx, pane.dataset.chosen, newDoubt);

            // Send to Backend API
            fetch('{{ route("api.exam.toggle-doubtful") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    attempt_id: ATTEMPT_ID,
                    question_id: questionId,
                    is_doubtful: newDoubt
                })
            });
        }

        // Update Drawer Nav Tile
        function updateDrawerTile(idx, chosenOpt, isDoubt) {
            const btn = document.getElementById(`nav-btn-${idx}`);
            const choiceLabel = document.getElementById(`nav-choice-${idx}`);

            btn.className = "question-nav-tile h-11 rounded-lg border font-bold text-xs flex flex-col items-center justify-center transition shadow-sm ";
            if (isDoubt) {
                btn.className += "bg-[#eab308] text-white border-yellow-500";
            } else if (chosenOpt && chosenOpt !== '') {
                btn.className += "bg-[#1e73be] text-white border-blue-600";
            } else {
                btn.className += "bg-white text-slate-700 border-slate-300 hover:bg-slate-100";
            }

            choiceLabel.innerText = chosenOpt ? chosenOpt : '-';
        }

        // Update Answered Counter
        function updateAnsweredCounter() {
            let count = 0;
            document.querySelectorAll('.question-pane').forEach(p => {
                if (p.dataset.chosen && p.dataset.chosen !== '') count++;
            });
            document.getElementById('answeredCounter').innerText = `Dijawab: ${count} / ${TOTAL_QUESTIONS}`;
        }

        // Countdown Timer (HH:MM:SS)
        function runCountdown() {
            const timerEl = document.getElementById('countdownTimer');
            if (remainingSeconds <= 0) {
                timerEl.innerText = "00:00:00";
                autoFinishExam("Waktu ujian telah habis!");
                return;
            }

            const h = Math.floor(remainingSeconds / 3600);
            const m = Math.floor((remainingSeconds % 3600) / 60);
            const s = remainingSeconds % 60;
            timerEl.innerText = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            remainingSeconds--;
        }
        setInterval(runCountdown, 1000);
        runCountdown();

        // Heartbeat synchronization with Server
        setInterval(() => {
            if (isExamFinished) return;
            fetch('{{ route("api.exam.heartbeat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ attempt_id: ATTEMPT_ID })
            }).then(r => r.json()).then(data => {
                if(data.is_expired) {
                    autoFinishExam("Waktu ujian telah berakhir di server.");
                } else if(data.is_locked) {
                    window.location.href = "{{ route('siswa.terkunci') }}";
                }
            });
        }, 30000);

        // Confirmation Modal Selesai
        function openFinishModal() {
            let answered = 0;
            let doubtful = 0;
            document.querySelectorAll('.question-pane').forEach(p => {
                if (p.dataset.chosen && p.dataset.chosen !== '') answered++;
                if (p.dataset.doubt === '1') doubtful++;
            });
            let unanswered = TOTAL_QUESTIONS - answered;

            Swal.fire({
                title: 'Konfirmasi Selesai Ujian',
                html: `
                    <div class="text-left text-xs text-slate-600 space-y-2 p-2">
                        <p class="font-medium">Apakah Anda yakin ingin mengakhiri ujian ini?</p>
                        <div class="grid grid-cols-3 gap-2 text-center py-2">
                            <div class="p-2 bg-blue-50 border border-blue-200 rounded">
                                <span class="block text-lg font-bold text-blue-700">${answered}</span>
                                <span>Dijawab</span>
                            </div>
                            <div class="p-2 bg-amber-50 border border-amber-200 rounded">
                                <span class="block text-lg font-bold text-amber-700">${doubtful}</span>
                                <span>Ragu-ragu</span>
                            </div>
                            <div class="p-2 bg-slate-100 border border-slate-200 rounded">
                                <span class="block text-lg font-bold text-slate-700">${unanswered}</span>
                                <span>Belum</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-amber-700 font-semibold">*Periksa kembali seluruh jawaban sebelum menyelesaikan ujian.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Selesaikan Ujian!',
                cancelButtonText: 'Kembali Periksa'
            }).then((result) => {
                if (result.isConfirmed) {
                    isExamFinished = true;
                    document.getElementById('finishExamForm').submit();
                }
            });
        }

        function autoFinishExam(reason) {
            if (isExamFinished) return;
            isExamFinished = true;
            Swal.fire({
                title: 'Ujian Berakhir',
                text: reason,
                icon: 'info',
                timer: 2500,
                showConfirmButton: false
            }).then(() => {
                document.getElementById('finishExamForm').submit();
            });
        }

        function showExamInfo() {
            Swal.fire({
                title: 'Informasi Ujian',
                html: `
                    <div class="text-left text-xs space-y-2 text-slate-700">
                        <p><strong>Mata Ujian:</strong> {{ $exam->title }}</p>
                        <p><strong>Jumlah Soal:</strong> ${TOTAL_QUESTIONS} Butir</p>
                        <p><strong>Alokasi Waktu:</strong> {{ $exam->duration_minutes }} Menit</p>
                        <p><strong>Batas Pelanggaran:</strong> Maksimal ${MAX_VIOLATIONS} Kali</p>
                        <hr>
                        <p class="text-slate-500">Semua jawaban dan sisa waktu tersimpan aman pada server sekolah.</p>
                    </div>
                `,
                confirmButtonColor: '#1e73be'
            });
        }

        // ==========================================
        // FITUR KEAMANAN CBT & ANTI-CURANG KETAT
        // ==========================================

        // 1. Peringatan Berpindah Tab / Window Blur
        function recordViolation(type, details) {
            if (isExamFinished) return;

            fetch('{{ route("api.exam.log-violation") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    attempt_id: ATTEMPT_ID,
                    violation_type: type,
                    details: details
                })
            }).then(r => r.json()).then(data => {
                violationsCount = data.violations_count;
                if (data.is_locked) {
                    isExamFinished = true;
                    Swal.fire({
                        title: 'Ujian Terkunci!',
                        text: `Anda telah melakukan ${violationsCount} kali pelanggaran (batas: ${MAX_VIOLATIONS}). Ujian Anda telah dikunci oleh sistem. Hubungi proktor/admin untuk membuka kunci.`,
                        icon: 'error',
                        allowOutsideClick: false,
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup Ujian'
                    }).then(() => {
                        window.location.href = "{{ route('siswa.terkunci') }}";
                    });
                } else {
                    Swal.fire({
                        title: 'Peringatan Kecurangan!',
                        html: `
                            <p class="text-xs text-red-600 font-semibold mb-2">
                                Anda terdeteksi meninggalkan halaman ujian! (${type})
                            </p>
                            <p class="text-xs text-slate-700">
                                Peringatan ke-<strong>${violationsCount}</strong> dari maksimal <strong>${MAX_VIOLATIONS}</strong> kali.
                            </p>
                            <p class="text-[11px] text-slate-500 mt-2">
                                Jika mencapai ${MAX_VIOLATIONS} kali pelanggaran, ujian Anda akan dikunci otomatis.
                            </p>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Saya Mengerti'
                    });
                }
            });
        }

        let blurDebounce = null;
        window.addEventListener('blur', () => {
            clearTimeout(blurDebounce);
            blurDebounce = setTimeout(() => {
                recordViolation('window_blur', 'Jendela browser kehilangan fokus atau membuka aplikasi lain.');
            }, 600);
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                recordViolation('tab_switch', 'Peserta berpindah tab atau meminimalkan jendela browser.');
            }
        });

        // 2. Anti-Screenshot Detection (PrintScreen Key)
        window.addEventListener('keyup', (e) => {
            if (e.key === 'PrintScreen' || e.keyCode === 44) {
                const overlay = document.getElementById('antiScreenshotOverlay');
                overlay.classList.remove('hidden');
                // Hapus clipboard
                if (navigator.clipboard) {
                    navigator.clipboard.writeText('');
                }
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 1500);
                recordViolation('screenshot_attempt', 'Peserta menekan tombol PrintScreen untuk screenshot.');
            }
        });

        // 3. Disable Context Menu (Right Click)
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Klik kanan dinonaktifkan demi keamanan ujian.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;
        });

        // 4. Disable Copy, Cut, Paste, Drag
        document.addEventListener('copy', (e) => { e.preventDefault(); return false; });
        document.addEventListener('cut', (e) => { e.preventDefault(); return false; });
        document.addEventListener('paste', (e) => { e.preventDefault(); return false; });
        document.addEventListener('dragstart', (e) => { e.preventDefault(); return false; });

        // 5. Block DevTools Shortcuts (F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+S)
        document.addEventListener('keydown', (e) => {
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) ||
                (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S' || e.key === 'p' || e.key === 'P'))
            ) {
                e.preventDefault();
                recordViolation('inspect_source_attempt', 'Peserta mencoba membuka Inspect Element / Source Code.');
                return false;
            }
        });

        // Initial UI State
        updateUIState();

        // CBT Dark/Light Mode Switcher & Professional Icon Micro-Interaction
        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const icons = document.querySelectorAll('#cbtThemeIcon, .theme-toggle-icon');
            icons.forEach(icon => {
                icon.classList.remove('animate-theme-spin');
                void icon.offsetWidth;
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

        document.addEventListener('DOMContentLoaded', updateThemeIcons);

        // Professional spring bounce and ripple effect on clicking interactive icons & buttons
        document.addEventListener('click', function(e) {
            const target = e.target.closest('.interactive-click, button, .clickable-icon, a, .option-radio-card');
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
</body>
</html>
