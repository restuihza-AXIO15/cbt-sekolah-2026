<?php

namespace App\Services;

use ZipArchive;

class DocxTemplateGenerator
{
    /**
     * Generate template file .docx resmi untuk import soal CBT
     *
     * @return string Path absolut file .docx yang dibuat
     */
    public function generate(): string
    {
        $dir = storage_path('app/templates');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $filePath = $dir . '/Template_Soal_CBT_2026.docx';

        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file template docx.');
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' .
            '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>' .
            '</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. word/_rels/document.xml.rels
        $docRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"/>';
        $zip->addFromString('word/_rels/document.xml.rels', $docRels);

        // 4. word/document.xml
        $documentXml = $this->buildDocumentXml();
        $zip->addFromString('word/document.xml', $documentXml);

        $zip->close();

        return $filePath;
    }

    /**
     * Membuat XML dokumen Word dengan isi petunjuk & contoh soal
     */
    protected function buildDocumentXml(): string
    {
        $paragraphs = [
            'FORMAT TEMPLATE IMPORT SOAL CBT UJIAN SEKOLAH 2026',
            'PUSAT ASESMEN PENDIDIKAN & SISTEM CBT TERINTEGRASI',
            '==============================================================',
            'PETUNJUK PENULISAN SOAL:',
            '1. Tuliskan nomor soal diikuti titik dan teks pertanyaan (Contoh: 1. Apa yang dimaksud...)',
            '2. Tuliskan pilihan jawaban A, B, C, D, dan E masing-masing di baris baru.',
            '3. Tuliskan kunci jawaban dengan format: KUNCI: [Huruf Opsi] (Contoh: KUNCI: A)',
            '4. Tuliskan bobot nilai dengan format: BOBOT: 1.0 (Opsional, bawaan adalah 1.0)',
            '5. Jika soal memiliki wacana/stimulus untuk beberapa nomor, tuliskan: STIMULUS: [Teks bacaan] sebelum nomor soal.',
            '==============================================================',
            '',
            '1. Manakah dari komponen berikut yang berfungsi sebagai otak utama dalam sebuah sistem komputer untuk memproses instruksi aritmatika dan logika?',
            'A. Random Access Memory (RAM)',
            'B. Central Processing Unit (CPU)',
            'C. Hard Disk Drive (HDD)',
            'D. Power Supply Unit (PSU)',
            'E. Graphics Processing Unit (GPU)',
            'KUNCI: B',
            'BOBOT: 1.0',
            '',
            'STIMULUS: Bacalah teks wacana berikut ini dengan cermat untuk menjawab soal nomor 2 dan nomor 3!',
            'Perkembangan teknologi jaringan nirkabel generasi kelima (5G) membawa dampak revolusioner bagi berbagai sektor industri, mulai dari kesehatan, manufaktur, hingga transportasi otonom. Dibandingkan pendahulunya (4G), teknologi 5G menawarkan latensi yang sangat rendah hingga di bawah 1 milidetik, kecepatan transmisi data hingga puluhan gigabit per detik, serta kemampuan menghubungkan jutaan perangkat Internet of Things (IoT) secara simultan dalam area yang luas.',
            '',
            '2. Berdasarkan teks stimulus di atas, keunggulan utama teknologi 5G yang memungkinkan komunikasi data real-time pada kendaraan otonom adalah...',
            'A. Harga perangkat keras yang jauh lebih murah',
            'B. Penggunaan antena pemancar konvensional',
            'C. Latensi sangat rendah hingga di bawah 1 milidetik',
            'D. Jangkauan gelombang elektromagnetik tanpa batas',
            'E. Kapasitas baterai perangkat yang lebih hemat',
            'KUNCI: C',
            'BOBOT: 1.5',
            '',
            '3. Apa arti istilah "IoT" yang disebutkan dalam teks bacaan di atas?',
            'A. Interconnection of Transmission',
            'B. Internet of Telecommunications',
            'C. International online Technology',
            'D. Internet of Things',
            'E. Internal operation Tool',
            'KUNCI: D',
            'BOBOT: 1.0',
            '',
            '4. Perangkat lunak (software) yang bertindak sebagai jembatan perantara antara perangkat keras komputer dengan pengguna akhir (user) disebut...',
            'A. Sistem Operasi (Operating System)',
            'B. Perangkat Lunak Antivirus',
            'C. Aplikasi Pengolah Kata',
            'D. Web Browser',
            'E. Compiler Bahasa C++',
            'KUNCI: A',
            'BOBOT: 1.0',
            '',
            '5. Salah satu tindakan preventif yang paling efektif untuk mencegah pencurian data kredensial akun dalam sistem ujian online berbasis komputer adalah...',
            'A. Menggunakan tanggal lahir sebagai kata sandi login',
            'B. Menonaktifkan sistem token berkala',
            'C. Mengaktifkan autentikasi berlapis dan token berkala setiap 10 menit',
            'D. Membagikan username dan password kepada peserta lain',
            'E. Menyimpan password di file teks yang mudah diakses',
            'KUNCI: C',
            'BOBOT: 1.0',
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">';
        $xml .= '<w:body>';

        foreach ($paragraphs as $p) {
            $safeText = htmlspecialchars($p, ENT_XML1, 'UTF-8');
            $xml .= '<w:p><w:r><w:t>' . $safeText . '</w:t></w:r></w:p>';
        }

        $xml .= '</w:body></w:document>';

        return $xml;
    }
}
