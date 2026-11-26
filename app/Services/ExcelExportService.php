<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\SchoolSetting;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    /**
     * Export rekap nilai lengkap ujian ke format Excel (.xls) berformat resmi
     */
    public function exportExamGrades(Exam $exam, Collection $attempts, SchoolSetting $setting): StreamedResponse
    {
        $filename = 'Rekap_Nilai_' . str_replace([' ', '/', '\\'], '_', $exam->title) . '_' . date('Ymd_His') . '.xls';

        $totalStudents = $attempts->count();
        $passedCount = $attempts->filter(fn($a) => $a->score >= $exam->passing_grade)->count();
        $remedialCount = $totalStudents - $passedCount;
        $passRate = $totalStudents > 0 ? round(($passedCount / $totalStudents) * 100, 1) : 0;
        $avgScore = $totalStudents > 0 ? round($attempts->avg('score'), 2) : 0;
        $maxScore = $totalStudents > 0 ? round($attempts->max('score'), 2) : 0;
        $minScore = $totalStudents > 0 ? round($attempts->min('score'), 2) : 0;

        return response()->streamDownload(function () use ($exam, $attempts, $setting, $totalStudents, $passedCount, $remedialCount, $passRate, $avgScore, $maxScore, $minScore) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="utf-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Rekap Nilai</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
                .title { font-size: 15pt; font-weight: bold; text-align: center; }
                .subtitle { font-size: 12pt; font-weight: bold; text-align: center; color: #1e3a8a; }
                .meta-table { margin-bottom: 15px; font-size: 10pt; }
                .meta-table td { padding: 3px 6px; }
                .data-table { border-collapse: collapse; width: 100%; font-size: 10pt; }
                .data-table th { background-color: #1e73be; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000; padding: 6px; }
                .data-table td { border: 1px solid #d1d5db; padding: 5px; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .font-bold { font-weight: bold; }
                .status-lulus { background-color: #d1fae5; color: #065f46; font-weight: bold; text-align: center; }
                .status-remedial { background-color: #fee2e2; color: #991b1b; font-weight: bold; text-align: center; }
                .stat-box { background-color: #f8fafc; border: 1px solid #cbd5e1; font-size: 10pt; }
                .stat-box td { padding: 4px 8px; }
                .sign-box { margin-top: 30px; font-size: 11pt; }
            </style>';
            echo '</head><body>';

            // KOP LAPORAN RESMI
            echo '<div class="title">' . htmlspecialchars(strtoupper($setting->school_name ?? 'SMA NEGERI 1 UNGGULAN')) . '</div>';
            echo '<div class="subtitle">REKAPITULASI DAFTAR NILAI ASESMEN & UJIAN BERBASIS KOMPUTER (CBT)</div>';
            echo '<div style="text-align: center; font-size: 10pt; margin-bottom: 20px;">Tahun Ajaran ' . htmlspecialchars($setting->academic_year ?? '2026/2027') . ' - Semester ' . htmlspecialchars($setting->semester ?? 'Ganjil') . '</div>';

            // METADATA UJIAN
            echo '<table class="meta-table">';
            echo '<tr><td><strong>Nama Paket Ujian</strong></td><td>: ' . htmlspecialchars($exam->title) . ' (' . htmlspecialchars($exam->category) . ')</td><td><strong>Alokasi Waktu</strong></td><td>: ' . $exam->duration_minutes . ' Menit</td></tr>';
            echo '<tr><td><strong>Mata Pelajaran</strong></td><td>: ' . htmlspecialchars($exam->subject->name ?? '-') . ' (' . htmlspecialchars($exam->subject->code ?? '-') . ')</td><td><strong>Standar KKM</strong></td><td>: <strong>' . number_format($exam->passing_grade, 2) . '</strong></td></tr>';
            echo '<tr><td><strong>Guru Pengampu</strong></td><td>: ' . htmlspecialchars($exam->subject->teacher->name ?? 'Tim Pengajar') . '</td><td><strong>Tanggal Unduh</strong></td><td>: ' . date('d F Y, H:i') . ' WIB</td></tr>';
            echo '</table>';

            // TABEL NILAI SISWA
            echo '<table class="data-table">';
            echo '<thead><tr>';
            echo '<th style="width: 40px;">No</th>';
            echo '<th style="width: 110px;">NISN / No Peserta</th>';
            echo '<th style="width: 100px;">Username</th>';
            echo '<th style="width: 220px;">Nama Lengkap Siswa</th>';
            echo '<th style="width: 50px;">L/P</th>';
            echo '<th style="width: 100px;">Kelas</th>';
            echo '<th style="width: 120px;">Ruang / Sesi</th>';
            echo '<th style="width: 60px;">Benar</th>';
            echo '<th style="width: 60px;">Salah</th>';
            echo '<th style="width: 60px;">Kosong</th>';
            echo '<th style="width: 80px;">Skor Akhir</th>';
            echo '<th style="width: 90px;">Keterangan</th>';
            echo '<th style="width: 110px;">Durasi Pengerjaan</th>';
            echo '<th style="width: 130px;">Waktu Selesai</th>';
            echo '</tr></thead>';
            echo '<tbody>';

            foreach ($attempts as $idx => $at) {
                $isPass = $at->score >= $exam->passing_grade;
                $statusClass = $isPass ? 'status-lulus' : 'status-remedial';
                $statusText = $isPass ? 'LULUS' : 'REMEDIAL';

                echo '<tr>';
                echo '<td class="text-center">' . ($idx + 1) . '</td>';
                echo '<td class="text-center" style="mso-number-format:\'@\';">' . htmlspecialchars($at->user->nisn ?? $at->user->username) . '</td>';
                echo '<td class="text-center" style="mso-number-format:\'@\';">' . htmlspecialchars($at->user->username) . '</td>';
                echo '<td><strong>' . htmlspecialchars($at->user->name) . '</strong></td>';
                echo '<td class="text-center">' . htmlspecialchars($at->user->gender ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($at->user->class_name ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars(($at->user->room ?? 'Lab 1') . ' / ' . ($at->user->session_name ?? 'Sesi 1')) . '</td>';
                echo '<td class="text-center font-bold" style="color: #059669;">' . $at->correct_count . '</td>';
                echo '<td class="text-center font-bold" style="color: #dc2626;">' . $at->incorrect_count . '</td>';
                echo '<td class="text-center" style="color: #64748b;">' . $at->unanswered_count . '</td>';
                echo '<td class="text-center font-bold" style="font-size: 11pt; color: #1e3a8a;">' . number_format($at->score, 2) . '</td>';
                echo '<td class="' . $statusClass . '">' . $statusText . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($at->durationFormatted()) . '</td>';
                echo '<td class="text-center">' . ($at->finished_at ? $at->finished_at->format('d/m/Y H:i') : '-') . '</td>';
                echo '</tr>';
            }

            if ($attempts->isEmpty()) {
                echo '<tr><td colspan="14" class="text-center" style="padding: 20px; color: #64748b;">Belum ada peserta yang menyelesaikan ujian ini.</td></tr>';
            }

            echo '</tbody></table>';

            // STATISTIK RINGKASAN
            echo '<br>';
            echo '<table class="stat-box" style="width: 450px;">';
            echo '<tr><th colspan="2" style="background-color: #334155; color: #fff; padding: 5px; text-align: left;">RINGKASAN STATISTIK UJIAN</th></tr>';
            echo '<tr><td>Total Peserta Ujian</td><td class="font-bold text-right">' . $totalStudents . ' Siswa</td></tr>';
            echo '<tr><td>Peserta Lulus (≥ KKM ' . number_format($exam->passing_grade, 2) . ')</td><td class="font-bold text-right" style="color: #059669;">' . $passedCount . ' Siswa (' . $passRate . '%)</td></tr>';
            echo '<tr><td>Peserta Remedial (&lt; KKM)</td><td class="font-bold text-right" style="color: #dc2626;">' . $remedialCount . ' Siswa</td></tr>';
            echo '<tr><td>Nilai Rata-Rata (Mean)</td><td class="font-bold text-right">' . number_format($avgScore, 2) . '</td></tr>';
            echo '<tr><td>Nilai Tertinggi (Max)</td><td class="font-bold text-right" style="color: #1e73be;">' . number_format($maxScore, 2) . '</td></tr>';
            echo '<tr><td>Nilai Terendah (Min)</td><td class="font-bold text-right">' . number_format($minScore, 2) . '</td></tr>';
            echo '</table>';

            // LEMBAR TANDA TANGAN
            echo '<br><br>';
            echo '<table class="sign-box" style="width: 100%; border: none;">';
            echo '<tr>';
            echo '<td style="width: 50%; text-align: center; border: none;">';
            echo 'Mengetahui,<br>Kepala Sekolah<br><br><br><br><br>';
            echo '<strong><u>' . htmlspecialchars($setting->headmaster_name ?? 'Drs. H. Bambang Suryono, M.Pd.') . '</u></strong><br>';
            echo 'NIP. ' . htmlspecialchars($setting->headmaster_nip ?? '19710312 199602 1 002');
            echo '</td>';
            echo '<td style="width: 50%; text-align: center; border: none;">';
            echo 'Jakarta, ' . date('d F Y') . '<br>Guru Mata Pelajaran<br><br><br><br><br>';
            echo '<strong><u>' . htmlspecialchars($exam->subject->teacher->name ?? 'Tim Guru Pengampu') . '</u></strong><br>';
            echo 'NIP. ' . htmlspecialchars($exam->subject->teacher->nip ?? '-');
            echo '</td>';
            echo '</tr>';
            echo '</table>';

            echo '</body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Download format template Excel kosong untuk rekap nilai
     */
    public function downloadGradeTemplate(Exam $exam, SchoolSetting $setting): StreamedResponse
    {
        $filename = 'Template_Nilai_' . str_replace([' ', '/', '\\'], '_', $exam->title) . '.xls';

        return response()->streamDownload(function () use ($exam, $setting) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="utf-8">';
            echo '<style>
                body { font-family: Calibri, Arial, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #0284c7; color: #ffffff; border: 1px solid #000; padding: 6px; }
                td { border: 1px solid #cbd5e1; padding: 5px; }
            </style></head><body>';
            echo '<h2>TEMPLATE FORMAT PENILAIAN - ' . htmlspecialchars($setting->school_name ?? 'SEKOLAH') . '</h2>';
            echo '<p>Mata Pelajaran: ' . htmlspecialchars($exam->subject->name ?? '-') . ' | KKM: ' . $exam->passing_grade . '</p>';
            echo '<table><thead><tr>';
            echo '<th>No</th><th>NISN</th><th>Nama Siswa</th><th>Kelas</th><th>Jumlah Benar</th><th>Jumlah Salah</th><th>Nilai Akhir</th><th>Status (LULUS/REMEDIAL)</th><th>Catatan Guru</th>';
            echo '</tr></thead><tbody>';
            for ($i = 1; $i <= 10; $i++) {
                echo '<tr>';
                echo '<td style="text-align: center;">' . $i . '</td>';
                echo '<td style="mso-number-format:\'@\';">008123456' . $i . '</td>';
                echo '<td>Siswa Contoh ' . $i . '</td>';
                echo '<td>XII MIPA 1</td>';
                echo '<td></td><td></td><td></td><td></td><td></td>';
                echo '</tr>';
            }
            echo '</tbody></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export seluruh data siswa ke format Excel (.xls)
     */
    public function exportStudentList(Collection $students, SchoolSetting $setting): StreamedResponse
    {
        $filename = 'Data_Peserta_CBT_' . date('Ymd_His') . '.xls';

        return response()->streamDownload(function () use ($students, $setting) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="utf-8">';
            echo '<style>
                body { font-family: Calibri, Arial, sans-serif; font-size: 10pt; }
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #1e73be; color: #ffffff; border: 1px solid #000; padding: 6px; font-weight: bold; }
                td { border: 1px solid #cbd5e1; padding: 5px; }
                .text-center { text-align: center; }
            </style></head><body>';
            echo '<h3>DAFTAR PESERTA UJIAN CBT - ' . htmlspecialchars($setting->school_name ?? 'SEKOLAH') . '</h3>';
            echo '<p>Total Terdaftar: ' . $students->count() . ' Siswa &bull; Tanggal Cetak: ' . date('d/m/Y H:i') . '</p>';
            echo '<table><thead><tr>';
            echo '<th>No</th><th>NISN</th><th>Username Login</th><th>Password Login</th><th>Nama Lengkap</th><th>L/P</th><th>Kelas</th><th>Ruang</th><th>Sesi</th><th>Status</th>';
            echo '</tr></thead><tbody>';

            foreach ($students as $idx => $st) {
                echo '<tr>';
                echo '<td class="text-center">' . ($idx + 1) . '</td>';
                echo '<td class="text-center" style="mso-number-format:\'@\';">' . htmlspecialchars($st->nisn ?? '-') . '</td>';
                echo '<td class="text-center" style="mso-number-format:\'@\';"><strong>' . htmlspecialchars($st->username) . '</strong></td>';
                echo '<td class="text-center" style="mso-number-format:\'@\';">' . htmlspecialchars($st->plain_password ?? 'pass123') . '</td>';
                echo '<td>' . htmlspecialchars($st->name) . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($st->gender ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($st->class_name ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($st->room ?? 'Lab 1') . '</td>';
                echo '<td>' . htmlspecialchars($st->session_name ?? 'Sesi 1') . '</td>';
                echo '<td class="text-center">' . ($st->is_active ? 'AKTIF' : 'NONAKTIF') . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Download format template CSV / Excel untuk import peserta massal
     */
    public function downloadStudentImportTemplate(): StreamedResponse
    {
        $filename = 'Template_Import_Siswa_CBT.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            // Menambahkan BOM UTF-8 agar Excel mengenali format UTF-8 dengan baik
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom
            fputcsv($handle, ['username', 'password', 'name', 'nisn', 'gender', 'class_name', 'room', 'session_name'], ';');

            // Baris contoh
            fputcsv($handle, ['25110050', 'pass123', 'Ahmad Dani Pratama', '0085421910', 'L', 'XII MIPA 1', 'Lab Komputer 1', 'Sesi 1 (07.30 - 09.30)'], ';');
            fputcsv($handle, ['25110051', 'pass123', 'Siti Nur Aisyah', '0085421911', 'P', 'XII MIPA 1', 'Lab Komputer 1', 'Sesi 1 (07.30 - 09.30)'], ';');
            fputcsv($handle, ['25110052', 'pass123', 'Budi Santoso', '0085421912', 'L', 'XII MIPA 2', 'Lab Komputer 2', 'Sesi 2 (10.00 - 12.00)'], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
