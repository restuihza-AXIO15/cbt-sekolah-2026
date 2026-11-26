<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentCsvParser
{
    /**
     * Import data siswa secara massal dari file CSV
     *
     * @param string $filePath
     * @return array ['success' => bool, 'imported' => int, 'errors' => array]
     */
    public function import(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'imported' => 0, 'errors' => ['File tidak ditemukan.']];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ['success' => false, 'imported' => 0, 'errors' => ['Gagal membaca file.']];
        }

        // Baca baris pertama untuk deteksi delimiter (, atau ;)
        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';

        // Lewati BOM UTF-8 jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, $delimiter);
        if (!$header) {
            fclose($handle);
            return ['success' => false, 'imported' => 0, 'errors' => ['File CSV kosong atau tidak memiliki header kolom.']];
        }

        // Normalisasi header ke lowercase
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $imported = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNum++;

            // Abaikan baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            $data = @array_combine($header, $row);
            if (!$data) {
                $errors[] = "Baris #$rowNum: Jumlah kolom tidak sesuai dengan header.";
                continue;
            }

            $username = trim($data['username'] ?? $data['nisn'] ?? '');
            $name = trim($data['name'] ?? $data['nama'] ?? '');
            $password = trim($data['password'] ?? 'pass123');

            if (empty($username) || empty($name)) {
                $errors[] = "Baris #$rowNum: Username dan Nama wajib diisi.";
                continue;
            }

            try {
                User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $name,
                        'email' => $username . '@cbt.school',
                        'password' => Hash::make($password),
                        'plain_password' => $password,
                        'role' => 'siswa',
                        'nisn' => trim($data['nisn'] ?? $username),
                        'gender' => strtoupper(substr(trim($data['gender'] ?? 'L'), 0, 1)),
                        'class_name' => trim($data['class_name'] ?? $data['kelas'] ?? 'XII MIPA 1'),
                        'room' => trim($data['room'] ?? $data['ruang'] ?? 'Lab Komputer 1'),
                        'session_name' => trim($data['session_name'] ?? $data['sesi'] ?? 'Sesi 1 (07.30 - 09.30)'),
                        'is_active' => true,
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris #$rowNum ($name): " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'success' => $imported > 0,
            'imported' => $imported,
            'errors' => $errors,
        ];
    }
}
