<?php

namespace App\Services;

use ZipArchive;

class DocxQuestionParser
{
    /**
     * Parse file Word (.docx) menjadi array butir soal CBT
     *
     * @param string $filePath Path ke file .docx yang diunggah
     * @return array ['success' => bool, 'questions' => array, 'errors' => array, 'count' => int]
     */
    public function parse(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'questions' => [],
                'errors' => ['File Word tidak ditemukan.'],
                'count' => 0,
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [
                'success' => false,
                'questions' => [],
                'errors' => ['Gagal membuka file .docx. Pastikan file bukan format .doc lama atau korup.'],
                'count' => 0,
            ];
        }

        // Baca word/document.xml
        $xmlContent = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xmlContent) {
            return [
                'success' => false,
                'questions' => [],
                'errors' => ['Konten teks dalam dokumen Word tidak ditemukan (word/document.xml kosong).'],
                'count' => 0,
            ];
        }

        // Ekstrak teks per paragraf
        $paragraphs = $this->extractParagraphsFromXml($xmlContent);

        if (empty($paragraphs)) {
            return [
                'success' => false,
                'questions' => [],
                'errors' => ['Tidak ada paragraf teks yang dapat dibaca dalam dokumen.'],
                'count' => 0,
            ];
        }

        return $this->parseParagraphsToQuestions($paragraphs);
    }

    /**
     * Ekstrak teks dari tag <w:p> pada document.xml
     */
    protected function extractParagraphsFromXml(string $xmlContent): array
    {
        $paragraphs = [];

        // Hapus namespace agar parsing regex / xml lebih mudah
        $cleanXml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$3', $xmlContent);

        // Ambil setiap <p>...</p>
        if (preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $cleanXml, $pMatches)) {
            foreach ($pMatches[1] as $pXml) {
                // Ambil semua teks dalam <t>...</t>
                if (preg_match_all('/<t\b[^>]*>(.*?)<\/t>/is', $pXml, $tMatches)) {
                    $text = implode('', $tMatches[1]);
                    // Decode entitas html/xml (&amp;, &lt;, &gt;, &quot;, &#39;)
                    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    $trimmed = trim($text);
                    if ($trimmed !== '') {
                        $paragraphs[] = $trimmed;
                    }
                }
            }
        }

        return $paragraphs;
    }

    /**
     * Parsing array paragraf menjadi format butir soal CBT
     */
    protected function parseParagraphsToQuestions(array $paragraphs): array
    {
        $questions = [];
        $errors = [];

        $currentStimulus = null;
        $currentQuestion = null;
        $currentNumber = 0;

        foreach ($paragraphs as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            // 1. Deteksi Stimulus / Teks Bacaan
            // Contoh: STIMULUS: Bacalah teks berikut... atau [BACAAN] ...
            if (preg_match('/^(?:STIMULUS|BACAAN|TEKS BACAAN|WACANA)\s*[:：\-]\s*(.*)$/iu', $trimmed, $m)) {
                $currentStimulus = trim($m[1]);
                continue;
            }

            // 2. Deteksi Permulaan Soal Baru
            // Contoh: 1. Apa itu ..., 1) Apa itu ..., Soal 1: ..., No. 1 ...
            if (preg_match('/^(?:(?:Soal|No\.?)\s*)?(\d+)[\.\)]\s*(.*)$/iu', $trimmed, $m)) {
                // Simpan soal sebelumnya jika sudah lengkap
                if ($currentQuestion && !empty($currentQuestion['question_text'])) {
                    $validation = $this->validateAndNormalizeQuestion($currentQuestion);
                    if ($validation['valid']) {
                        $questions[] = $validation['question'];
                    } elseif (!($validation['is_instruction'] ?? false)) {
                        $errors[] = "Soal No. " . ($currentQuestion['detected_number'] ?? '?') . ": " . implode(', ', $validation['errors']);
                    }
                }

                $currentNumber = (int)$m[1];
                $currentQuestion = [
                    'detected_number' => $currentNumber,
                    'stimulus' => $currentStimulus,
                    'question_text' => trim($m[2]),
                    'option_a' => '',
                    'option_b' => '',
                    'option_c' => '',
                    'option_d' => '',
                    'option_e' => '',
                    'correct_answer' => '',
                    'score_weight' => 1.0,
                ];
                continue;
            }

            // Jika belum ada soal yang dimulai, cek apakah ini kelanjutan stimulus
            if (!$currentQuestion) {
                if ($currentStimulus !== null) {
                    $currentStimulus .= "\n" . $trimmed;
                }
                continue;
            }

            // 3. Deteksi Pilihan Opsi A, B, C, D, E
            if (preg_match('/^[A-Ea-e][\.\)]\s*(.*)$/u', $trimmed, $m)) {
                $letter = strtoupper(substr($trimmed, 0, 1));
                $optionKey = 'option_' . strtolower($letter);
                $currentQuestion[$optionKey] = trim($m[1]);
                continue;
            }

            // 4. Deteksi Kunci Jawaban
            // Contoh: KUNCI: A, KUNCI JAWABAN: B, JAWABAN: C, Kunci = D, ANSWER: E
            if (preg_match('/^(?:KUNCI(?:\s*JAWABAN)?|JAWABAN|ANSWER|KUNCI)\s*[:：=]\s*([A-Ea-e])/iu', $trimmed, $m)) {
                $currentQuestion['correct_answer'] = strtoupper($m[1]);
                continue;
            }

            // 5. Deteksi Bobot Soal (Opsional)
            // Contoh: BOBOT: 1.5, POIN: 2
            if (preg_match('/^(?:BOBOT|POIN|SCORE|NILAI)\s*[:：=]\s*([\d\.]+)/iu', $trimmed, $m)) {
                $currentQuestion['score_weight'] = (float)$m[1];
                continue;
            }

            // 6. Jika baris ini bukan opsi/kunci/nomor baru, berarti kelanjutan dari pertanyaan atau opsi terakhir
            if (empty($currentQuestion['option_a'])) {
                // Masih bagian dari teks pertanyaan (multiline)
                $currentQuestion['question_text'] .= "\n" . $trimmed;
            } else {
                // Cari opsi terakhir yang sedang diisi dan sambungkan
                foreach (['e', 'd', 'c', 'b', 'a'] as $opt) {
                    if (!empty($currentQuestion['option_' . $opt])) {
                        $currentQuestion['option_' . $opt] .= " " . $trimmed;
                        break;
                    }
                }
            }
        }

        // Simpan soal terakhir jika ada
        if ($currentQuestion && !empty($currentQuestion['question_text'])) {
            $validation = $this->validateAndNormalizeQuestion($currentQuestion);
            if ($validation['valid']) {
                $questions[] = $validation['question'];
            } elseif (!($validation['is_instruction'] ?? false)) {
                $errors[] = "Soal No. " . ($currentQuestion['detected_number'] ?? '?') . ": " . implode(', ', $validation['errors']);
            }
        }

        return [
            'success' => count($questions) > 0,
            'questions' => $questions,
            'errors' => $errors,
            'count' => count($questions),
        ];
    }

    /**
     * Validasi kelengkapan soal (pertanyaan, opsi minimal A-D, kunci jawaban)
     */
    protected function validateAndNormalizeQuestion(array $q): array
    {
        // Jika sama sekali tidak ada opsi A-D, ini kemungkinan baris instruksi/judul bernomor di awal dokumen
        if (empty(trim($q['option_a'])) && empty(trim($q['option_b'])) && empty(trim($q['option_c'])) && empty(trim($q['option_d']))) {
            return [
                'valid' => false,
                'is_instruction' => true,
                'errors' => [],
                'question' => null,
            ];
        }

        $errors = [];

        if (empty(trim($q['question_text']))) {
            $errors[] = 'Teks pertanyaan kosong.';
        }
        if (empty(trim($q['option_a']))) {
            $errors[] = 'Pilihan A belum diisi.';
        }
        if (empty(trim($q['option_b']))) {
            $errors[] = 'Pilihan B belum diisi.';
        }
        if (empty(trim($q['option_c']))) {
            $errors[] = 'Pilihan C belum diisi.';
        }
        if (empty(trim($q['option_d']))) {
            $errors[] = 'Pilihan D belum diisi.';
        }
        if (empty($q['correct_answer']) || !in_array($q['correct_answer'], ['A', 'B', 'C', 'D', 'E'])) {
            $errors[] = 'Kunci jawaban belum ditentukan atau tidak valid (harus A, B, C, D, atau E).';
        }

        return [
            'valid' => empty($errors),
            'is_instruction' => false,
            'errors' => $errors,
            'question' => [
                'stimulus' => $q['stimulus'] ?? null,
                'question_text' => trim($q['question_text']),
                'option_a' => trim($q['option_a']),
                'option_b' => trim($q['option_b']),
                'option_c' => trim($q['option_c']),
                'option_d' => trim($q['option_d']),
                'option_e' => !empty($q['option_e']) ? trim($q['option_e']) : null,
                'correct_answer' => $q['correct_answer'],
                'score_weight' => $q['score_weight'] > 0 ? $q['score_weight'] : 1.0,
            ],
        ];
    }
}
