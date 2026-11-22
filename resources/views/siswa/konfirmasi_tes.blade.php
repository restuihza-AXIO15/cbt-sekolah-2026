@extends('layouts.pusmendik')

@section('title', 'Konfirmasi Tes - PUSMENDIK')

@section('content')
<div class="w-full max-w-md bg-white dark:bg-slate-900/95 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 p-8 my-auto transform transition-all animate-fade-in-up">
    <!-- Title (Sesuai Gambar 4) -->
    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 tracking-tight">Konfirmasi Tes</h3>

    <div class="space-y-4">
        <!-- Nama Tes (Sesuai Gambar 4) -->
        <div>
            <label class="block text-xs text-slate-400 dark:text-slate-500 font-medium mb-0.5">Nama Tes</label>
            <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 pb-2 border-b border-slate-100 dark:border-slate-800">
                {{ $activeExam->title }}
            </div>
        </div>

        <!-- Status Tes (Sesuai Gambar 4) -->
        <div>
            <label class="block text-xs text-slate-400 dark:text-slate-500 font-medium mb-0.5">Status Tes</label>
            <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 pb-2 border-b border-slate-100 dark:border-slate-800">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300">
                    {{ $statusTes }}
                </span>
            </div>
        </div>

        <!-- Waktu Tes (Sesuai Gambar 4) -->
        <div>
            <label class="block text-xs text-slate-400 dark:text-slate-500 font-medium mb-0.5">Waktu Tes</label>
            <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 pb-2 border-b border-slate-100 dark:border-slate-800 font-mono">
                {{ now()->format('d/m/Y, H.i') }}
            </div>
        </div>

        <!-- Alokasi Waktu Tes (Sesuai Gambar 4) -->
        <div>
            <label class="block text-xs text-slate-400 dark:text-slate-500 font-medium mb-0.5">Alokasi Waktu Tes</label>
            <div class="text-sm font-bold text-[#1e73be] dark:text-sky-400 pb-2 border-b border-slate-100 dark:border-slate-800">
                {{ $activeExam->duration_minutes }} Menit
            </div>
        </div>
    </div>

    <!-- Peringatan Aturan Keamanan Anti-Curang -->
    <div class="mt-6 p-3.5 bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 rounded-xl text-xs text-amber-800 dark:text-amber-200 space-y-1">
        <div class="font-bold flex items-center space-x-1.5 text-amber-900 dark:text-amber-300">
            <i class="fa-solid fa-shield-halved text-amber-600 dark:text-amber-400"></i>
            <span>Tata Tertib & Keamanan CBT:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-amber-800 dark:text-amber-300">
            <li>Dilarang berpindah tab browser atau meminimalkan jendela ujian.</li>
            <li>Dilarang mengambil tangkapan layar (screenshot), menyalin, atau inspect element.</li>
            <li>Pelanggaran sebanyak 3x akan <strong>mengunci ujian secara otomatis</strong>.</li>
            <li>Jawaban Anda otomatis tersimpan di server secara real-time.</li>
        </ul>
    </div>

    <!-- Button Mulai (Sesuai Gambar 4) -->
    <form action="{{ route('siswa.mulai-ujian') }}" method="POST" class="mt-6" onsubmit="requestFullScreenIfPossible()">
        @csrf
        <button type="submit" 
            class="interactive-click w-full py-3 px-4 bg-[#1e73be] hover:bg-[#155d9b] text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition flex items-center justify-center space-x-2">
            <i class="fa-solid fa-play text-xs"></i>
            <span>Mulai</span>
        </button>
    </form>
</div>

@push('scripts')
<script>
    function requestFullScreenIfPossible() {
        const docElm = document.documentElement;
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen().catch(err => {
                console.log("Fullscreen request was prevented by user/browser.");
            });
        }
    }
</script>
@endpush
@endsection
