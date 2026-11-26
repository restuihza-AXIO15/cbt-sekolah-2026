<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamViolation;
use App\Models\Question;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Models\Token;
use App\Models\User;
use App\Services\DocxQuestionParser;
use App\Services\DocxTemplateGenerator;
use App\Services\ExcelExportService;
use App\Services\StudentCsvParser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Dashboard Admin (Sesuai Gambar 6)
     */
    public function dashboard()
    {
        $setting = SchoolSetting::current();

        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalMapel = Subject::count();
        $totalSoal = Question::count();
        $activeExam = Exam::where('status', 'active')->with('subject')->first();

        $activeToken = Token::where('is_active', true)
            ->where('valid_until', '>', Carbon::now())
            ->latest()
            ->first();

        $siswaSedangUjian = 0;
        $siswaSelesai = 0;

        if ($activeExam) {
            $siswaSedangUjian = ExamAttempt::where('exam_id', $activeExam->id)
                ->where('status', 'ongoing')
                ->count();
            $siswaSelesai = ExamAttempt::where('exam_id', $activeExam->id)
                ->where('status', 'finished')
                ->count();
        }

        $recentExams = Exam::with('subject')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'setting',
            'totalSiswa',
            'totalGuru',
            'totalMapel',
            'totalSoal',
            'activeExam',
            'activeToken',
            'siswaSedangUjian',
            'siswaSelesai',
            'recentExams'
        ));
    }

    /**
     * Data Master (Mata Pelajaran)
     */
    public function masterData()
    {
        $setting = SchoolSetting::current();
        $subjects = Subject::with(['teacher', 'questions', 'exams'])->get();
        $teachers = User::where('role', 'guru')->get();

        return view('admin.master_data', compact('setting', 'subjects', 'teachers'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:subjects,code',
            'name' => 'required|string',
            'category' => 'required|string',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        Subject::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'category' => $request->category,
            'teacher_id' => $request->teacher_id,
        ]);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function deleteSubject($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Unduh Template Soal Word (.docx)
     */
    public function downloadDocxTemplate(DocxTemplateGenerator $generator)
    {
        $filePath = $generator->generate();
        return response()->download($filePath, 'Template_Soal_CBT_2026.docx');
    }

    /**
     * Import Soal Word Langsung ke Mata Pelajaran
     */
    public function importDocx(Request $request, DocxQuestionParser $parser)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'file_docx' => 'required|file|mimes:docx|max:10240',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        $file = $request->file('file_docx');
        $result = $parser->parse($file->getRealPath());

        if (!$result['success'] || $result['count'] === 0) {
            $msg = 'Tidak ada butir soal yang berhasil dibaca dari dokumen.';
            if (!empty($result['errors'])) {
                $msg .= ' Kesalahan: ' . implode(' | ', array_slice($result['errors'], 0, 3));
            }
            return back()->with('error', $msg);
        }

        $imported = 0;
        foreach ($result['questions'] as $q) {
            Question::create([
                'subject_id' => $subject->id,
                'stimulus' => $q['stimulus'],
                'question_text' => $q['question_text'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'option_e' => $q['option_e'] ?? null,
                'correct_answer' => $q['correct_answer'],
                'score_weight' => $q['score_weight'],
                'created_by' => auth()->id(),
            ]);
            $imported++;
        }

        return back()->with('success', "Berhasil mengimpor {$imported} butir soal Word ke mata pelajaran {$subject->name}!");
    }

    /**
     * Data Peserta (Siswa)
     */
    public function peserta(Request $request)
    {
        $setting = SchoolSetting::current();
        $query = User::where('role', 'siswa');

        if ($request->filled('kelas')) {
            $query->where('class_name', $request->kelas);
        }
        if ($request->filled('ruang')) {
            $query->where('room', $request->ruang);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                    ->orWhere('username', 'like', "%$s%")
                    ->orWhere('nisn', 'like', "%$s%");
            });
        }

        $students = $query->orderBy('class_name')->orderBy('name')->paginate(20);
        $classes = User::where('role', 'siswa')->distinct()->pluck('class_name')->filter()->values();
        $rooms = User::where('role', 'siswa')->distinct()->pluck('room')->filter()->values();

        return view('admin.peserta', compact('setting', 'students', 'classes', 'rooms'));
    }

    public function storePeserta(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
            'nisn' => 'nullable|string',
            'class_name' => 'required|string',
            'gender' => 'required|in:L,P',
            'room' => 'nullable|string',
            'session_name' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@cbt.school',
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'role' => 'siswa',
            'nisn' => $request->nisn ?: $request->username,
            'gender' => $request->gender,
            'class_name' => $request->class_name,
            'room' => $request->room ?: 'Lab Komputer 1',
            'session_name' => $request->session_name ?: 'Sesi 1 (07.30 - 09.30)',
            'is_active' => true,
        ]);

        return back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function deletePeserta($id)
    {
        $student = User::where('role', 'siswa')->findOrFail($id);
        $student->delete();
        return back()->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Export Seluruh Siswa ke File Excel
     */
    public function exportSiswaExcel(ExcelExportService $excelService)
    {
        $setting = SchoolSetting::current();
        $students = User::where('role', 'siswa')->orderBy('class_name')->orderBy('name')->get();
        return $excelService->exportStudentList($students, $setting);
    }

    /**
     * Unduh Template CSV / Excel untuk Import Siswa
     */
    public function downloadStudentTemplate(ExcelExportService $excelService)
    {
        return $excelService->downloadStudentImportTemplate();
    }

    /**
     * Import Siswa Massal dari File CSV / Excel
     */
    public function importSiswaCsv(Request $request, StudentCsvParser $csvParser)
    {
        $request->validate([
            'file_siswa' => 'required|file|max:5120',
        ], [
            'file_siswa.required' => 'Pilih file CSV/Excel daftar siswa.',
            'file_siswa.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $file = $request->file('file_siswa');
        $result = $csvParser->import($file->getRealPath());

        if (!$result['success']) {
            $msg = 'Gagal mengimpor siswa.';
            if (!empty($result['errors'])) {
                $msg .= ' ' . implode(' | ', array_slice($result['errors'], 0, 3));
            }
            return back()->with('error', $msg);
        }

        $msg = "Berhasil mengimpor {$result['imported']} siswa ke sistem CBT!";
        if (!empty($result['errors'])) {
            $msg .= " (" . count($result['errors']) . " baris bermasalah diabaikan).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Cetak Kartu Peserta Ujian Resmi
     */
    public function cetakKartuPeserta(Request $request)
    {
        $setting = SchoolSetting::current();
        $query = User::where('role', 'siswa');

        if ($request->filled('student_id')) {
            $query->where('id', $request->student_id);
        }
        if ($request->filled('kelas')) {
            $query->where('class_name', $request->kelas);
        }

        $students = $query->orderBy('class_name')->orderBy('name')->get();

        return view('admin.cetak_kartu', compact('setting', 'students'));
    }

    /**
     * Manajemen Akun Guru
     */
    public function guru()
    {
        $setting = SchoolSetting::current();
        $teachers = User::where('role', 'guru')->with('subject')->get();
        $subjects = Subject::all();

        return view('admin.guru', compact('setting', 'teachers', 'subjects'));
    }

    public function storeGuru(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@cbt.school',
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'role' => 'guru',
            'is_active' => true,
        ]);

        if ($request->filled('subject_id')) {
            Subject::where('id', $request->subject_id)->update(['teacher_id' => $teacher->id]);
        }

        return back()->with('success', 'Akun guru berhasil dibuat.');
    }

    public function deleteGuru($id)
    {
        $teacher = User::where('role', 'guru')->findOrFail($id);
        Subject::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);
        $teacher->delete();
        return back()->with('success', 'Akun guru berhasil dihapus.');
    }

    /**
     * Administrasi Tes & Jadwal Ujian
     */
    public function jadwalUjian()
    {
        $setting = SchoolSetting::current();
        $exams = Exam::with(['subject', 'attempts'])->latest()->get();
        $subjects = Subject::all();

        return view('admin.jadwal_ujian', compact('setting', 'exams', 'subjects'));
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string',
            'category' => 'required|string',
            'duration_minutes' => 'required|integer|min:10|max:300',
            'passing_grade' => 'required|numeric|min:0|max:100',
            'max_violations' => 'required|integer|min:1|max:10',
            'status' => 'required|in:draft,active,completed',
        ]);

        if ($request->status === 'active') {
            Exam::where('status', 'active')->update(['status' => 'completed']);
        }

        Exam::create([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'category' => $request->category,
            'duration_minutes' => $request->duration_minutes,
            'passing_grade' => $request->passing_grade,
            'shuffle_questions' => $request->boolean('shuffle_questions', true),
            'shuffle_options' => $request->boolean('shuffle_options', true),
            'max_violations' => $request->max_violations,
            'status' => $request->status,
            'start_time' => Carbon::now(),
        ]);

        return back()->with('success', 'Jadwal ujian berhasil disimpan.');
    }

    public function updateStatusJadwal(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:draft,active,completed']);
        $exam = Exam::findOrFail($id);

        if ($request->status === 'active') {
            Exam::where('status', 'active')->where('id', '!=', $exam->id)->update(['status' => 'completed']);
        }

        $exam->update(['status' => $request->status]);

        return back()->with('success', 'Status ujian berhasil diubah menjadi: ' . strtoupper($request->status));
    }

    /**
     * Rilis Token Ujian (Admin Generator, Masa Berlaku 10 Menit)
     */
    public function tokenManager()
    {
        $setting = SchoolSetting::current();
        $activeExam = Exam::where('status', 'active')->with('subject')->first();

        $activeToken = Token::where('is_active', true)
            ->where('valid_until', '>', Carbon::now())
            ->latest()
            ->first();

        $tokenHistory = Token::with(['exam.subject', 'creator'])
            ->latest()
            ->take(15)
            ->get();

        return view('admin.token_manager', compact('setting', 'activeExam', 'activeToken', 'tokenHistory'));
    }

    public function generateToken(Request $request)
    {
        $activeExam = Exam::where('status', 'active')->first();
        $code = Token::generateCode();

        Token::where('is_active', true)->update(['is_active' => false]);

        $token = Token::create([
            'exam_id' => $activeExam ? $activeExam->id : null,
            'token_code' => $code,
            'valid_until' => Carbon::now()->addMinutes(10),
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        return back()->with('success', "Token baru {$code} berhasil diterbitkan dengan masa berlaku 10 menit.");
    }

    public function expireToken($id)
    {
        $token = Token::findOrFail($id);
        $token->update(['is_active' => false, 'valid_until' => Carbon::now()]);
        return back()->with('success', 'Token berhasil dinonaktifkan.');
    }

    /**
     * Manajemen Pengawasan / Proctor Realtime Monitoring
     */
    public function monitoringPengawasan(Request $request)
    {
        $setting = SchoolSetting::current();
        $activeExam = Exam::where('status', 'active')->with('subject')->first();

        $query = ExamAttempt::query();
        if ($activeExam) {
            $query->where('exam_id', $activeExam->id);
        }

        if ($request->filled('ruang')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('room', $request->ruang);
            });
        }
        if ($request->filled('sesi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('session_name', $request->sesi);
            });
        }

        $attempts = $query->with(['user', 'violations'])
            ->orderByRaw("CASE WHEN status = 'locked' THEN 1 WHEN status = 'ongoing' THEN 2 ELSE 3 END")
            ->get();

        $availableRooms = User::where('role', 'siswa')->distinct()->pluck('room')->filter()->values();
        $availableSessions = User::where('role', 'siswa')->distinct()->pluck('session_name')->filter()->values();

        return view('admin.monitoring', compact('setting', 'activeExam', 'attempts', 'availableRooms', 'availableSessions'));
    }

    public function resetPeserta($id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        $attempt->delete();
        return back()->with('success', 'Data ujian peserta telah direset. Peserta dapat memulai kembali.');
    }

    public function unlockPeserta($id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        $attempt->update([
            'status' => 'ongoing',
            'violations_count' => 0,
        ]);
        return back()->with('success', 'Kunci ujian peserta telah dibuka kembali.');
    }

    public function forceFinishPeserta($id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        $attempt->calculateResult();
        return back()->with('success', 'Ujian peserta telah dipaksa selesai dan dinilai.');
    }

    /**
     * Aksi Massal Pengawasan (Bulk Actions)
     */
    public function bulkMonitoringAction(Request $request)
    {
        $request->validate([
            'action_type' => 'required|in:unlock_all,finish_all,reset_all',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $query = ExamAttempt::where('exam_id', $request->exam_id);

        if ($request->filled('room')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('room', $request->room);
            });
        }

        $count = 0;
        if ($request->action_type === 'unlock_all') {
            $count = $query->where('status', 'locked')->update(['status' => 'ongoing', 'violations_count' => 0]);
            $msg = "Berhasil membuka kunci {$count} peserta secara massal.";
        } elseif ($request->action_type === 'finish_all') {
            $ongoingAttempts = $query->where('status', 'ongoing')->get();
            foreach ($ongoingAttempts as $att) {
                $att->calculateResult();
                $count++;
            }
            $msg = "Berhasil menyelesaikan & menilai {$count} ujian peserta secara massal.";
        } elseif ($request->action_type === 'reset_all') {
            $count = $query->count();
            $query->delete();
            $msg = "Berhasil mereset {$count} sesi ujian peserta di ruangan/sesi ini.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Rekap dan Laporan Nilai (Sesuai Gambar 7 & 8)
     */
    public function rekapNilai(Request $request)
    {
        $setting = SchoolSetting::current();
        $exams = Exam::with(['subject', 'attempts'])->latest()->get();

        $selectedExamId = $request->get('exam_id', $exams->first()?->id);
        $selectedExam = $exams->firstWhere('id', $selectedExamId);

        $attempts = collect();
        if ($selectedExam) {
            $attempts = ExamAttempt::where('exam_id', $selectedExam->id)
                ->with('user')
                ->where('status', 'finished')
                ->orderByDesc('score')
                ->get();
        }

        return view('admin.rekap_nilai', compact('setting', 'exams', 'selectedExam', 'attempts'));
    }

    /**
     * Unduh Laporan Nilai Langsung Menjadi File Excel (.xls)
     */
    public function exportNilaiExcel(Request $request, ExcelExportService $excelService)
    {
        $setting = SchoolSetting::current();
        $exam = Exam::with(['subject.teacher'])->findOrFail($request->exam_id);

        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with('user')
            ->where('status', 'finished')
            ->orderByDesc('score')
            ->get();

        return $excelService->exportExamGrades($exam, $attempts, $setting);
    }

    /**
     * Unduh Format Template Excel Nilai
     */
    public function downloadGradeTemplate(Request $request, ExcelExportService $excelService)
    {
        $setting = SchoolSetting::current();
        $exam = Exam::with('subject')->findOrFail($request->exam_id);
        return $excelService->downloadGradeTemplate($exam, $setting);
    }

    /**
     * Analisis Butir Soal & Statistik Mendalam (Fitur Kompleks Admin)
     */
    public function analisisSoal(Request $request)
    {
        $setting = SchoolSetting::current();
        $exams = Exam::with('subject')->latest()->get();

        $selectedExamId = $request->get('exam_id', $exams->first()?->id);
        $selectedExam = $exams->firstWhere('id', $selectedExamId);

        $analysis = [];
        $stats = [
            'total_peserta' => 0,
            'rata_rata' => 0,
            'tertinggi' => 0,
            'terendah' => 0,
            'lulus' => 0,
            'remedial' => 0,
            'pass_rate' => 0,
            'ranges' => ['0-59' => 0, '60-74' => 0, '75-84' => 0, '85-100' => 0],
        ];

        if ($selectedExam) {
            $attempts = ExamAttempt::where('exam_id', $selectedExam->id)->where('status', 'finished')->get();
            $totalAttempts = $attempts->count();
            $stats['total_peserta'] = $totalAttempts;

            if ($totalAttempts > 0) {
                $stats['rata_rata'] = round($attempts->avg('score'), 2);
                $stats['tertinggi'] = round($attempts->max('score'), 2);
                $stats['terendah'] = round($attempts->min('score'), 2);
                $passed = $attempts->filter(fn($a) => $a->score >= $selectedExam->passing_grade)->count();
                $stats['lulus'] = $passed;
                $stats['remedial'] = $totalAttempts - $passed;
                $stats['pass_rate'] = round(($passed / $totalAttempts) * 100, 1);

                foreach ($attempts as $at) {
                    if ($at->score < 60) $stats['ranges']['0-59']++;
                    elseif ($at->score < 75) $stats['ranges']['60-74']++;
                    elseif ($at->score < 85) $stats['ranges']['75-84']++;
                    else $stats['ranges']['85-100']++;
                }
            }

            // Analisis per butir soal
            $questions = Question::where('subject_id', $selectedExam->subject_id)->get();
            foreach ($questions as $idx => $q) {
                $answers = ExamAnswer::where('question_id', $q->id)
                    ->whereHas('attempt', fn($att) => $att->where('exam_id', $selectedExam->id)->where('status', 'finished'))
                    ->get();

                $answeredCount = $answers->count();
                $correctCount = $answers->where('is_correct', true)->count();
                $difficulty = $answeredCount > 0 ? round(($correctCount / $answeredCount) * 100, 1) : 0;

                // Kategori Tingkat Kesukaran
                $difficultyCategory = 'Sedang';
                if ($difficulty < 30) $difficultyCategory = 'Sukar';
                elseif ($difficulty > 70) $difficultyCategory = 'Mudah';

                $analysis[] = [
                    'number' => $idx + 1,
                    'id' => $q->id,
                    'text' => $q->question_text,
                    'correct_answer' => $q->correct_answer,
                    'answered_count' => $answeredCount,
                    'correct_count' => $correctCount,
                    'incorrect_count' => $answeredCount - $correctCount,
                    'accuracy_percentage' => $difficulty,
                    'difficulty_category' => $difficultyCategory,
                ];
            }
        }

        return view('admin.analisis_soal', compact('setting', 'exams', 'selectedExam', 'stats', 'analysis'));
    }

    /**
     * Audit Log & Pengawasan Keamanan (Fitur Kompleks Admin)
     */
    public function auditLog(Request $request)
    {
        $setting = SchoolSetting::current();
        $violations = ExamViolation::with(['attempt.user', 'attempt.exam.subject'])
            ->latest()
            ->paginate(25);

        $recentTokens = Token::with(['exam.subject', 'creator'])->latest()->take(20)->get();

        return view('admin.audit_log', compact('setting', 'violations', 'recentTokens'));
    }

    /**
     * Pengaturan Sekolah & CBT
     */
    public function pengaturan()
    {
        $setting = SchoolSetting::current();
        return view('admin.pengaturan', compact('setting'));
    }

    public function updatePengaturan(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string',
            'app_title' => 'required|string',
            'sub_title' => 'required|string',
            'school_address' => 'required|string',
            'headmaster_name' => 'required|string',
            'headmaster_nip' => 'required|string',
            'committee_name' => 'required|string',
            'academic_year' => 'required|string',
            'default_duration' => 'required|integer|min:10',
            'logo_file' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:3072',
        ]);

        $setting = SchoolSetting::current();
        $data = $request->except(['logo_file', 'reset_logo']);

        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $uploadDir = public_path('uploads/branding');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['school_logo'] = 'uploads/branding/' . $filename;
        } elseif ($request->boolean('reset_logo')) {
            $data['school_logo'] = null;
        }

        $setting->update($data);

        return back()->with('success', 'Pengaturan branding sekolah & sistem CBT berhasil diperbarui.');
    }
}
