<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\SchoolSetting;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Dashboard Guru
     */
    public function dashboard()
    {
        $user = Auth::user();
        $setting = SchoolSetting::current();

        // Mapel yang diampu guru ini (atau semua jika belum diset khusus)
        $subject = $user->subject ?: Subject::first();
        $totalSoal = $subject ? Question::where('subject_id', $subject->id)->count() : 0;

        // Ujian yang terkait mapel ini
        $exams = $subject ? Exam::where('subject_id', $subject->id)->latest()->get() : collect();
        $completedExamsCount = $exams->where('status', 'completed')->count();

        return view('guru.dashboard', compact('user', 'setting', 'subject', 'totalSoal', 'exams', 'completedExamsCount'));
    }

    /**
     * Bank Soal (Guru Menambahkan dan Mengelola Soal)
     */
    public function bankSoal(Request $request)
    {
        $user = Auth::user();
        $setting = SchoolSetting::current();
        $subject = $user->subject ?: Subject::first();

        if (!$subject) {
            return redirect()->route('guru.dashboard')->with('error', 'Anda belum ditugaskan pada mata pelajaran apapun.');
        }

        $questions = Question::where('subject_id', $subject->id)->latest()->paginate(15);

        return view('guru.bank_soal', compact('user', 'setting', 'subject', 'questions'));
    }

    /**
     * Simpan Soal Baru
     */
    public function storeSoal(Request $request)
    {
        $user = Auth::user();
        $subject = $user->subject ?: Subject::first();

        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'nullable|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
            'score_weight' => 'required|numeric|min:0.1',
            'stimulus' => 'nullable|string',
        ]);

        Question::create([
            'subject_id' => $subject->id,
            'stimulus' => $request->stimulus,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'option_e' => $request->option_e,
            'correct_answer' => $request->correct_answer,
            'score_weight' => $request->score_weight,
            'created_by' => $user->id,
        ]);

        return back()->with('success', 'Butir soal berhasil ditambahkan ke Bank Soal.');
    }

    /**
     * Hapus Soal
     */
    public function deleteSoal($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        return back()->with('success', 'Butir soal berhasil dihapus.');
    }

    /**
     * Rekap Nilai untuk Guru
     * HANYA BISA DIAMBIL / DIAKSES JIKA ASESMEN TELAH SELESAI (COMPLETED)
     */
    public function rekapNilai(Request $request)
    {
        $user = Auth::user();
        $setting = SchoolSetting::current();
        $subject = $user->subject ?: Subject::first();

        // Ambil semua ujian yang terkait mapel ini
        $exams = $subject ? Exam::where('subject_id', $subject->id)->latest()->get() : collect();

        $selectedExamId = $request->get('exam_id', $exams->first()?->id);
        $selectedExam = $exams->firstWhere('id', $selectedExamId);

        $isCompleted = $selectedExam && $selectedExam->status === 'completed';

        $attempts = collect();
        if ($isCompleted) {
            $attempts = ExamAttempt::where('exam_id', $selectedExam->id)
                ->with('user')
                ->where('status', 'finished')
                ->orderByDesc('score')
                ->get();
        }

        return view('guru.rekap_nilai', compact('user', 'setting', 'subject', 'exams', 'selectedExam', 'isCompleted', 'attempts'));
    }
}
