<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Models\Token;
use App\Models\User;
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
     * Data Peserta (Siswa)
     */
    public function peserta(Request $request)
    {
        $setting = SchoolSetting::current();
        $query = User::where('role', 'siswa');

        if ($request->has('kelas') && $request->kelas != '') {
            $query->where('class_name', $request->kelas);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20);
        $classes = User::where('role', 'siswa')->distinct()->pluck('class_name')->filter();

        return view('admin.peserta', compact('setting', 'students', 'classes'));
    }

    public function storePeserta(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'nisn' => 'nullable|string',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'class_name' => 'required|string',
            'room' => 'nullable|string',
            'session_name' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@siswa.cbt.sch.id',
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'role' => 'siswa',
            'nisn' => $request->nisn,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'class_name' => $request->class_name,
            'room' => $request->room ?: 'Lab Komputer 1',
            'session_name' => $request->session_name ?: 'Sesi 1 (07.30 - 09.30)',
            'is_active' => true,
        ]);

        return back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function deletePeserta($id)
    {
        $user = User::where('role', 'siswa')->findOrFail($id);
        $user->delete();
        return back()->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Cetak Kartu Peserta Ujian (KTA Peserta)
     */
    public function cetakKartuPeserta(Request $request)
    {
        $setting = SchoolSetting::current();
        $query = User::where('role', 'siswa');

        if ($request->has('kelas') && $request->kelas != '') {
            $query->where('class_name', $request->kelas);
        }

        if ($request->has('student_id') && $request->student_id != '') {
            $query->where('id', $request->student_id);
        }

        $students = $query->orderBy('class_name')->orderBy('name')->get();

        return view('admin.cetak_kartu', compact('setting', 'students'));
    }

    /**
     * Manajemen Guru
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
            'password' => 'required|string|min:4',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@guru.cbt.sch.id',
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'role' => 'guru',
            'subject_id' => $request->subject_id,
            'is_active' => true,
        ]);

        if ($request->subject_id) {
            Subject::where('id', $request->subject_id)->update(['teacher_id' => $teacher->id]);
        }

        return back()->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function deleteGuru($id)
    {
        $user = User::where('role', 'guru')->findOrFail($id);
        $user->delete();
        return back()->with('success', 'Data guru berhasil dihapus.');
    }

    /**
     * Jadwal Ujian & Alokasi Waktu (Default 120 Menit, Dapat Diatur Admin)
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
            'duration_minutes' => 'required|integer|min:10|max:300', // Default 120 Menit
            'passing_grade' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:draft,active,completed',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'max_violations' => 'required|integer|min:1|max:10',
        ]);

        // Jika status active, nonaktifkan ujian lain agar token fokus
        if ($request->status === 'active') {
            Exam::where('status', 'active')->update(['status' => 'draft']);
        }

        Exam::create([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'category' => $request->category,
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'duration_minutes' => $request->duration_minutes,
            'passing_grade' => $request->passing_grade,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addMinutes($request->duration_minutes),
            'status' => $request->status,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'max_violations' => $request->max_violations,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Jadwal ujian berhasil dibuat dengan alokasi waktu ' . $request->duration_minutes . ' menit.');
    }

    public function updateStatusJadwal(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,active,completed',
        ]);

        if ($request->status === 'active') {
            Exam::where('status', 'active')->where('id', '!=', $id)->update(['status' => 'draft']);
        }

        $exam = Exam::findOrFail($id);
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

        // Nonaktifkan token lama yang masih aktif
        Token::where('is_active', true)->update(['is_active' => false]);

        // Buat token baru dengan masa berlaku 10 menit
        $token = Token::create([
            'exam_id' => $activeExam ? $activeExam->id : null,
            'token_code' => $code,
            'valid_until' => Carbon::now()->addMinutes(10), // 10 menit masa berlaku
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

        $attempts = collect();
        if ($activeExam) {
            $attempts = ExamAttempt::where('exam_id', $activeExam->id)
                ->with(['user', 'violations'])
                ->orderByRaw("CASE WHEN status = 'locked' THEN 1 WHEN status = 'ongoing' THEN 2 ELSE 3 END")
                ->get();
        }

        return view('admin.monitoring', compact('setting', 'activeExam', 'attempts'));
    }

    public function resetPeserta($id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        $attempt->delete(); // Hapus attempt agar peserta dapat login ulang dari awal
        return back()->with('success', 'Data ujian peserta telah direset. Peserta dapat memulai kembali.');
    }

    public function unlockPeserta($id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        $attempt->update([
            'status' => 'ongoing',
            'violations_count' => 0, // Reset peringatan agar dapat lanjut
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

        // Upload logo sekolah baru jika ada
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
