<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaExamController;
use Illuminate\Support\Facades\Route;

// Public / Auth (Langsung ke Login Siswa / Pengguna sesuai permintaan)
Route::get('/', function () {
    return redirect()->route('login');
})->name('landing');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Siswa Area
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/konfirmasi-data', [SiswaExamController::class, 'showKonfirmasi'])->name('konfirmasi');
    Route::post('/konfirmasi-data', [SiswaExamController::class, 'submitKonfirmasi'])->name('konfirmasi.submit');
    Route::get('/konfirmasi-tes', [SiswaExamController::class, 'showKonfirmasiTes'])->name('konfirmasi-tes');
    Route::post('/mulai-ujian', [SiswaExamController::class, 'startExam'])->name('mulai-ujian');
    Route::get('/pengerjaan', [SiswaExamController::class, 'showPengerjaan'])->name('pengerjaan');
    Route::get('/selesai', [SiswaExamController::class, 'showSelesai'])->name('selesai');
    Route::get('/terkunci', [SiswaExamController::class, 'showTerkunci'])->name('terkunci');
});

// Siswa Backend API Endpoints (Secured by auth & CSRF)
Route::middleware(['auth', 'role:siswa'])->prefix('api/exam')->name('api.exam.')->group(function () {
    Route::post('/save-answer', [SiswaExamController::class, 'saveAnswer'])->name('save-answer');
    Route::post('/toggle-doubtful', [SiswaExamController::class, 'toggleDoubtful'])->name('toggle-doubtful');
    Route::post('/log-violation', [SiswaExamController::class, 'logViolation'])->name('log-violation');
    Route::post('/heartbeat', [SiswaExamController::class, 'heartbeat'])->name('heartbeat');
    Route::post('/finish', [SiswaExamController::class, 'finishExam'])->name('finish');
});

// Admin Area
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/master-data', [AdminController::class, 'masterData'])->name('master-data');
    Route::post('/master-data/subject', [AdminController::class, 'storeSubject'])->name('master-data.subject.store');
    Route::delete('/master-data/subject/{id}', [AdminController::class, 'deleteSubject'])->name('master-data.subject.delete');

    // Word Docx Soal Management (Admin)
    Route::get('/template-soal-docx', [AdminController::class, 'downloadDocxTemplate'])->name('template-soal.docx');
    Route::post('/import-soal-docx', [AdminController::class, 'importDocx'])->name('import-soal.docx');

    // Peserta & Import/Export Excel
    Route::get('/peserta', [AdminController::class, 'peserta'])->name('peserta');
    Route::post('/peserta', [AdminController::class, 'storePeserta'])->name('peserta.store');
    Route::delete('/peserta/{id}', [AdminController::class, 'deletePeserta'])->name('peserta.delete');
    Route::get('/peserta/export-excel', [AdminController::class, 'exportSiswaExcel'])->name('peserta.export-excel');
    Route::get('/peserta/template-csv', [AdminController::class, 'downloadStudentTemplate'])->name('peserta.template-csv');
    Route::post('/peserta/import-csv', [AdminController::class, 'importSiswaCsv'])->name('peserta.import-csv');
    Route::get('/cetak-kartu', [AdminController::class, 'cetakKartuPeserta'])->name('cetak-kartu');

    // Guru
    Route::get('/guru', [AdminController::class, 'guru'])->name('guru');
    Route::post('/guru', [AdminController::class, 'storeGuru'])->name('guru.store');
    Route::delete('/guru/{id}', [AdminController::class, 'deleteGuru'])->name('guru.delete');

    // Jadwal
    Route::get('/jadwal', [AdminController::class, 'jadwalUjian'])->name('jadwal');
    Route::post('/jadwal', [AdminController::class, 'storeJadwal'])->name('jadwal.store');
    Route::post('/jadwal/{id}/status', [AdminController::class, 'updateStatusJadwal'])->name('jadwal.status');

    // Token
    Route::get('/token', [AdminController::class, 'tokenManager'])->name('token');
    Route::post('/token/generate', [AdminController::class, 'generateToken'])->name('token.generate');
    Route::post('/token/{id}/expire', [AdminController::class, 'expireToken'])->name('token.expire');

    // Monitoring Proktor & Aksi Massal
    Route::get('/monitoring', [AdminController::class, 'monitoringPengawasan'])->name('monitoring');
    Route::post('/monitoring/{id}/reset', [AdminController::class, 'resetPeserta'])->name('monitoring.reset');
    Route::post('/monitoring/{id}/unlock', [AdminController::class, 'unlockPeserta'])->name('monitoring.unlock');
    Route::post('/monitoring/{id}/force-finish', [AdminController::class, 'forceFinishPeserta'])->name('monitoring.force-finish');
    Route::post('/monitoring/bulk-action', [AdminController::class, 'bulkMonitoringAction'])->name('monitoring.bulk-action');

    // Rekap & Nilai Excel Export
    Route::get('/rekap-nilai', [AdminController::class, 'rekapNilai'])->name('rekap-nilai');
    Route::get('/rekap-nilai/export-excel', [AdminController::class, 'exportNilaiExcel'])->name('rekap-nilai.export-excel');
    Route::get('/rekap-nilai/template-excel', [AdminController::class, 'downloadGradeTemplate'])->name('rekap-nilai.template-excel');

    // Fitur Kompleks: Analisis Butir Soal & Audit Log
    Route::get('/analisis-soal', [AdminController::class, 'analisisSoal'])->name('analisis-soal');
    Route::get('/audit-log', [AdminController::class, 'auditLog'])->name('audit-log');

    // Pengaturan
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan', [AdminController::class, 'updatePengaturan'])->name('pengaturan.update');
});

// Guru Area
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/bank-soal', [GuruController::class, 'bankSoal'])->name('bank-soal');
    Route::post('/bank-soal', [GuruController::class, 'storeSoal'])->name('bank-soal.store');
    Route::delete('/bank-soal/{id}', [GuruController::class, 'deleteSoal'])->name('bank-soal.delete');

    // Word Docx Soal Management (Guru)
    Route::get('/template-soal-docx', [GuruController::class, 'downloadDocxTemplate'])->name('template-soal.docx');
    Route::post('/import-soal-docx', [GuruController::class, 'importDocx'])->name('import-soal.docx');

    // Nilai & Unduh Excel (Guru)
    Route::get('/rekap-nilai', [GuruController::class, 'rekapNilai'])->name('rekap-nilai');
    Route::get('/rekap-nilai/export-excel', [GuruController::class, 'exportExcel'])->name('rekap-nilai.export-excel');
    Route::get('/rekap-nilai/template-excel', [GuruController::class, 'downloadGradeTemplate'])->name('rekap-nilai.template-excel');
});
