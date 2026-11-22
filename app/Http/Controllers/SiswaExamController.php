<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamViolation;
use App\Models\Question;
use App\Models\SchoolSetting;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaExamController extends Controller
{
    /**
     * Konfirmasi Data Peserta (Sesuai Gambar 3)
     */
    public function showKonfirmasi()
    {
        $user = Auth::user();
        $setting = SchoolSetting::current();

        // Ambil ujian aktif
        $activeExam = Exam::where('status', 'active')->with('subject')->first();

        // Token aktif yang dibuat admin (masa berlaku 10 menit)
        $activeToken = Token::where('is_active', true)
            ->where('valid_until', '>', Carbon::now())
            ->latest()
            ->first();

        // Cek jika siswa sudah punya attempt yang selesai
        if ($activeExam) {
            $attempt = ExamAttempt::where('exam_id', $activeExam->id)
                ->where('user_id', $user->id)
                ->first();

            if ($attempt && $attempt->isFinished()) {
                return redirect()->route('siswa.selesai');
            }
        }

        return view('siswa.konfirmasi_data', compact('user', 'setting', 'activeExam', 'activeToken'));
    }

    /**
     * Proses Validasi Konfirmasi Data & Token (Sesuai Gambar 3)
     */
    public function submitKonfirmasi(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_peserta_input' => 'required|string',
            'birth_day' => 'required|numeric|min:1|max:31',
            'birth_month' => 'required|numeric|min:1|max:12',
            'birth_year' => 'required|numeric|min:1990|max:2030',
            'token' => 'required|string',
        ], [
            'nama_peserta_input.required' => 'Nama peserta wajib diketikkan.',
            'token.required' => 'Token wajib diisi.',
        ]);

        $activeExam = Exam::where('status', 'active')->first();
        if (!$activeExam) {
            return back()->with('error', 'Tidak ada jadwal ujian yang sedang aktif saat ini. Hubungi proktor/admin sekolah.')->withInput();
        }

        // 1. Verifikasi Nama Peserta yang diinput manual agar tidak terjadi kesalahan
        if (strcasecmp(trim($request->nama_peserta_input), trim($user->name)) !== 0) {
            return back()->with('error', 'Nama peserta yang Anda ketikkan tidak sesuai dengan nama terdaftar (' . $user->name . '). Silakan ketik nama lengkap Anda dengan benar.')->withInput();
        }

        // 2. Verifikasi Tanggal Lahir yang dipilih manual
        if ($user->birth_date) {
            $inputDate = sprintf('%04d-%02d-%02d', $request->birth_year, $request->birth_month, $request->birth_day);
            if ($inputDate !== $user->birth_date->format('Y-m-d')) {
                return back()->with('error', 'Tanggal lahir yang Anda masukkan tidak sesuai dengan data peserta. Harap periksa tanggal, bulan, dan tahun lahir Anda.')->withInput();
            }
        }

        // 3. Verifikasi Token yang diinput manual (Diberikan oleh Admin/Pengawas)
        $tokenInput = strtoupper(trim($request->token));
        $validToken = Token::where('token_code', $tokenInput)
            ->where('is_active', true)
            ->where('valid_until', '>', Carbon::now())
            ->first();

        if (!$validToken) {
            return back()->with('error', 'Token tidak valid atau masa berlaku (10 menit) telah habis. Silakan tanyakan token aktif terbaru kepada pengawas ujian.')->withInput();
        }

        // Simpan token ke sesi pengerjaan
        session([
            'verified_exam_id' => $activeExam->id,
            'verified_token' => $tokenInput,
            'confirmed_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->route('siswa.konfirmasi-tes');
    }

    /**
     * Konfirmasi Tes (Sesuai Gambar 4)
     */
    public function showKonfirmasiTes()
    {
        $user = Auth::user();
        $setting = SchoolSetting::current();

        $examId = session('verified_exam_id');
        $activeExam = Exam::with('subject')->find($examId);

        if (!$activeExam || !$activeExam->isActive()) {
            return redirect()->route('siswa.konfirmasi')->with('error', 'Silakan konfirmasi token terlebih dahulu.');
        }

        // Cek status tes peserta
        $attempt = ExamAttempt::where('exam_id', $activeExam->id)
            ->where('user_id', $user->id)
            ->first();

        $statusTes = $attempt ? 'Sedang Dikerjakan' : 'Tes Baru';

        return view('siswa.konfirmasi_tes', compact('user', 'setting', 'activeExam', 'statusTes'));
    }

    /**
     * Memulai Ujian / Masuk Lembar Soal (Inisialisasi Server-Side Timer & Shuffling)
     */
    public function startExam()
    {
        $user = Auth::user();
        $examId = session('verified_exam_id');
        $exam = Exam::with(['subject.questions'])->find($examId);

        if (!$exam || !$exam->isActive()) {
            return redirect()->route('siswa.konfirmasi')->with('error', 'Ujian tidak aktif atau sesi telah berakhir.');
        }

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if ($attempt) {
            if ($attempt->isFinished()) {
                return redirect()->route('siswa.selesai');
            }
            if ($attempt->isLocked()) {
                return redirect()->route('siswa.terkunci');
            }
            if ($attempt->isTimeExpired()) {
                $attempt->calculateResult();
                return redirect()->route('siswa.selesai');
            }
        } else {
            // Ambil semua soal untuk mapel ini
            $questionIds = Question::where('subject_id', $exam->subject_id)->pluck('id')->toArray();

            if (empty($questionIds)) {
                return redirect()->route('siswa.konfirmasi')->with('error', 'Belum ada butir soal pada mata pelajaran ini.');
            }

            // Acak urutan soal jika opsi acak aktif
            if ($exam->shuffle_questions) {
                shuffle($questionIds);
            }

            $duration = $exam->duration_minutes ?: 120;
            $now = Carbon::now();
            $endAt = (clone $now)->addMinutes($duration);

            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'token_used' => session('verified_token'),
                'question_order' => $questionIds,
                'started_at' => $now,
                'duration_minutes' => $duration,
                'end_at' => $endAt,
                'status' => 'ongoing',
                'violations_count' => 0,
                'last_heartbeat' => $now,
            ]);
        }

        return redirect()->route('siswa.pengerjaan');
    }

    /**
     * Tampilan Lembar Ujian CBT (Sesuai Gambar 5)
     */
    public function showPengerjaan(Request $request)
    {
        $user = Auth::user();
        $activeExam = Exam::where('status', 'active')->first();

        if (!$activeExam) {
            return redirect()->route('siswa.konfirmasi')->with('error', 'Tidak ada ujian yang sedang aktif.');
        }

        $attempt = ExamAttempt::where('exam_id', $activeExam->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt) {
            return redirect()->route('siswa.konfirmasi');
        }

        if ($attempt->isFinished()) {
            return redirect()->route('siswa.selesai');
        }

        if ($attempt->isLocked()) {
            return redirect()->route('siswa.terkunci');
        }

        if ($attempt->isTimeExpired()) {
            $attempt->calculateResult();
            return redirect()->route('siswa.selesai');
        }

        $setting = SchoolSetting::current();
        $exam = $activeExam->load('subject');

        // Ambil daftar soal berurutan sesuai question_order siswa
        $order = $attempt->question_order ?: [];
        $questions = Question::whereIn('id', $order)->get()->keyBy('id');

        // Susun urutan persis sesuai question_order
        $orderedQuestions = [];
        foreach ($order as $qId) {
            if (isset($questions[$qId])) {
                $orderedQuestions[] = $questions[$qId];
            }
        }

        // Ambil jawaban yang sudah disimpan sebelumnya
        $answers = ExamAnswer::where('attempt_id', $attempt->id)->get()->keyBy('question_id');

        // Hitung sisa waktu server-side dalam detik
        $remainingSeconds = $attempt->remainingSeconds();

        return view('siswa.pengerjaan', compact(
            'user',
            'setting',
            'exam',
            'attempt',
            'orderedQuestions',
            'answers',
            'remainingSeconds'
        ));
    }

    /**
     * API Backend: Simpan Jawaban Soal (CSRF & Session Protected)
     */
    public function saveAnswer(Request $request)
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'selected_option' => 'nullable|string|in:A,B,C,D,E',
        ]);

        $user = Auth::user();
        $attempt = ExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt || $attempt->isFinished() || $attempt->isLocked() || $attempt->isTimeExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi ujian telah berakhir atau terkunci.',
            ], 403);
        }

        $question = Question::find($request->question_id);
        $isCorrect = strtoupper($request->selected_option) === strtoupper($question->correct_answer);

        $answer = ExamAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'selected_option' => $request->selected_option,
                'is_correct' => $isCorrect,
            ]
        );

        $attempt->update(['last_heartbeat' => Carbon::now()]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan.',
            'selected_option' => $answer->selected_option,
            'is_doubtful' => $answer->is_doubtful,
        ]);
    }

    /**
     * API Backend: Toggle Status Ragu-Ragu (Sesuai Tombol Kuning Gambar 5)
     */
    public function toggleDoubtful(Request $request)
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'is_doubtful' => 'required|boolean',
        ]);

        $user = Auth::user();
        $attempt = ExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt || $attempt->isFinished() || $attempt->isLocked()) {
            return response()->json(['success' => false], 403);
        }

        $answer = ExamAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'is_doubtful' => $request->is_doubtful,
            ]
        );

        return response()->json([
            'success' => true,
            'is_doubtful' => $answer->is_doubtful,
        ]);
    }

    /**
     * API Backend: Catat Pelanggaran Anti-Curang (Tab switch, Blur, Screenshot, Devtools)
     */
    public function logViolation(Request $request)
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'violation_type' => 'required|string',
            'details' => 'nullable|string',
        ]);

        $user = Auth::user();
        $attempt = ExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt || $attempt->isFinished()) {
            return response()->json(['success' => false], 403);
        }

        // Catat di tabel riwayat pelanggaran
        ExamViolation::create([
            'attempt_id' => $attempt->id,
            'user_id' => $user->id,
            'violation_type' => $request->violation_type,
            'details' => $request->details ?: 'Terdeteksi beralih jendela/tab atau aktivitas mencurigakan.',
        ]);

        $newCount = $attempt->violations_count + 1;
        $maxViolations = $attempt->exam ? $attempt->exam->max_violations : 3;

        $isLocked = false;
        if ($newCount >= $maxViolations) {
            $attempt->update([
                'violations_count' => $newCount,
                'status' => 'locked',
            ]);
            $isLocked = true;
        } else {
            $attempt->update([
                'violations_count' => $newCount,
            ]);
        }

        return response()->json([
            'success' => true,
            'violations_count' => $newCount,
            'max_violations' => $maxViolations,
            'is_locked' => $isLocked,
        ]);
    }

    /**
     * API Backend: Heartbeat & Sinkronisasi Sisa Waktu
     */
    public function heartbeat(Request $request)
    {
        $user = Auth::user();
        $attempt = ExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt) {
            return response()->json(['success' => false], 404);
        }

        $attempt->update(['last_heartbeat' => Carbon::now()]);

        $remaining = $attempt->remainingSeconds();
        $isExpired = $attempt->isTimeExpired();

        if ($isExpired && !$attempt->isFinished()) {
            $attempt->calculateResult();
        }

        return response()->json([
            'success' => true,
            'remaining_seconds' => $remaining,
            'is_expired' => $isExpired,
            'is_locked' => $attempt->isLocked(),
        ]);
    }

    /**
     * Selesaikan Ujian & Hitung Nilai Secara Aman di Server
     */
    public function finishExam(Request $request)
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
        ]);

        $user = Auth::user();
        $attempt = ExamAttempt::where('id', $request->attempt_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt) {
            return redirect()->route('siswa.konfirmasi');
        }

        // Kalkulasi nilai secara aman di server
        $attempt->calculateResult();

        return redirect()->route('siswa.selesai');
    }

    /**
     * Tampilan Bukti Selesai Ujian
     */
    public function showSelesai()
    {
        $user = Auth::user();
        $setting = SchoolSetting::current();
        $attempt = ExamAttempt::where('user_id', $user->id)
            ->with(['exam.subject'])
            ->latest()
            ->first();

        return view('siswa.selesai', compact('user', 'setting', 'attempt'));
    }

    /**
     * Tampilan Ujian Terkunci (Jika Melanggar Batas Tab Switch)
     */
    public function showTerkunci()
    {
        $user = Auth::user();
        $setting = SchoolSetting::current();
        $attempt = ExamAttempt::where('user_id', $user->id)
            ->with(['exam.subject'])
            ->latest()
            ->first();

        return view('siswa.terkunci', compact('user', 'setting', 'attempt'));
    }
}
