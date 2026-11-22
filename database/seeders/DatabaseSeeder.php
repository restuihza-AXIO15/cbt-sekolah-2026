<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\SchoolSetting;
use App\Models\Subject;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        SchoolSetting::create([
            'school_name' => 'SMA NEGERI 1 UNGGULAN',
            'school_address' => 'Jl. Pendidikan Nasional No. 15, Telp: (021) 7891234, Email: cbt@sman1unggulan.sch.id',
            'headmaster_name' => 'Drs. H. Bambang Suryono, M.Pd.',
            'headmaster_nip' => '19710312 199602 1 002',
            'committee_name' => 'Panitia Pelaksana CBT Ujian Sekolah 2026',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'default_duration' => 120, // 120 menit per 1 mapel
        ]);

        // 2. Users: Admin
        $admin = User::create([
            'name' => 'Administrator CBT',
            'username' => 'admin',
            'email' => 'admin@cbt2026.sch.id',
            'password' => Hash::make('admin123'),
            'plain_password' => 'admin123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 3. Subject: Bahasa Inggris
        $subjectEng = Subject::create([
            'code' => 'BING-01',
            'name' => 'Bahasa Inggris',
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'category' => 'Mata Pelajaran Wajib',
        ]);

        $subjectMat = Subject::create([
            'code' => 'MAT-01',
            'name' => 'Matematika Wajib',
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'category' => 'Mata Pelajaran Wajib',
        ]);

        $subjectInd = Subject::create([
            'code' => 'BINDO-01',
            'name' => 'Bahasa Indonesia',
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'category' => 'Mata Pelajaran Wajib',
        ]);

        // 4. Users: Guru (Teacher)
        $guruEng = User::create([
            'name' => 'Andi Wijaya, S.Pd., M.Hum.',
            'username' => 'guru_bing',
            'email' => 'andi@cbt2026.sch.id',
            'password' => Hash::make('guru123'),
            'plain_password' => 'guru123',
            'role' => 'guru',
            'subject_id' => $subjectEng->id,
            'is_active' => true,
        ]);
        $subjectEng->update(['teacher_id' => $guruEng->id]);

        $guruMat = User::create([
            'name' => 'Siti Fatimah, S.Pd., M.Si.',
            'username' => 'guru_mat',
            'email' => 'siti@cbt2026.sch.id',
            'password' => Hash::make('guru123'),
            'plain_password' => 'guru123',
            'role' => 'guru',
            'subject_id' => $subjectMat->id,
            'is_active' => true,
        ]);
        $subjectMat->update(['teacher_id' => $guruMat->id]);

        // 5. Users: Students (Siswa) - matching realistic data from images
        $studentsData = [
            [
                'name' => 'Galih Pramudya',
                'username' => '25110009',
                'nisn' => '0085421901',
                'gender' => 'L',
                'birth_date' => '2008-04-12',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
            [
                'name' => 'Bagas Saputra',
                'username' => '25110007',
                'nisn' => '0085421902',
                'gender' => 'L',
                'birth_date' => '2008-07-21',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
            [
                'name' => 'Annisa Rahma',
                'username' => '25110012',
                'nisn' => '0085421903',
                'gender' => 'P',
                'birth_date' => '2008-02-14',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
            [
                'name' => 'Citra Maharani',
                'username' => '25110008',
                'nisn' => '0085421904',
                'gender' => 'P',
                'birth_date' => '2008-09-05',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
            [
                'name' => 'Ayu Lestari',
                'username' => '25110006',
                'nisn' => '0085421905',
                'gender' => 'P',
                'birth_date' => '2008-11-30',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
            [
                'name' => 'Dimas Arya Pratama',
                'username' => '25110015',
                'nisn' => '0085421906',
                'gender' => 'L',
                'birth_date' => '2008-05-18',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
            [
                'name' => 'Fadhil Muhammad',
                'username' => '25110019',
                'nisn' => '0085421907',
                'gender' => 'L',
                'birth_date' => '2008-03-25',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
            [
                'name' => 'Nabila Putri Santika',
                'username' => '25110022',
                'nisn' => '0085421908',
                'gender' => 'P',
                'birth_date' => '2008-08-09',
                'class_name' => 'XII MIPA 1',
                'room' => 'Lab Komputer 1',
                'session_name' => 'Sesi 1 (07.30 - 09.30)',
                'plain_password' => 'pass123',
            ],
        ];

        $studentModels = [];
        foreach ($studentsData as $st) {
            $studentModels[] = User::create([
                'name' => $st['name'],
                'username' => $st['username'],
                'email' => $st['username'] . '@siswa.cbt.sch.id',
                'password' => Hash::make($st['plain_password']),
                'plain_password' => $st['plain_password'],
                'role' => 'siswa',
                'nisn' => $st['nisn'],
                'gender' => $st['gender'],
                'birth_date' => $st['birth_date'],
                'class_name' => $st['class_name'],
                'room' => $st['room'],
                'session_name' => $st['session_name'],
                'is_active' => true,
            ]);
        }

        // 6. Questions for Bahasa Inggris (Matching image 5 exactly)
        $stimulusText = "King Hung Vuong VI had a beautiful daughter. He did not want her to marry just any prince. So, he made an announcement that he was looking for the right husband for her. Many princes came from faraway lands, but none of them was a good match for the princess.\n\nSon Tinh was the Spirit of the Mountain, and Thuy Tinh was the Spirit of the Waters. One day, they both appeared as young noblemen and asked to marry the princess. They were equally talented, powerful, and respected. The King found it hard to choose, so he decided to give them a test. He said that the one who brought the proper wedding gifts first the next morning would marry his daughter.\n\nThe next day, Son Tinh arrived early with his gifts. The King kept his promise and gave the princess to him. Thuy Tinh was angry about losing. He challenged Son Tinh to fight for the princess. But Son Tinh refused, believing he had already won fairly. Furious, Thuy Tinh used his power to call the rivers and streams to rise. Soon, the land was covered with floods that destroyed crops and homes.\n\nSon Tinh stayed calm in his mountain palace. Whenever the water rose, he made his mountains higher. After many days of fighting, Thuy Tinh grew tired and ordered the waters to retreat. Still, he never accepted his defeat. Every year, he tried again to attack, and this is how monsoons came to Vietnam.";

        $questionsData = [
            [
                'stimulus' => "Teks untuk soal nomor 1 s.d. 5!\n\n" . $stimulusText,
                'question_text' => 'Which of the following outlines shows the correct main points of the story?',
                'option_a' => 'King Hung Vuong VI wanted the best husband for his daughter. Many princes came but none was suitable. Son Tinh and Thuy Tinh both wanted to marry her. The King gave them a test. Thuy Tinh arrived first with the wedding gifts. The princess was given to Thuy Tinh.',
                'option_b' => 'King Hung Vuong VI had a beautiful daughter. He announced he was looking for the right husband. Son Tinh and Thuy Tinh appeared and asked to marry her. The King gave them a test with wedding gifts. Son Tinh arrived early and married the princess. Thuy Tinh was angry and caused floods, but Son Tinh defended against them.',
                'option_c' => 'Son Tinh and Thuy Tinh both wanted to marry the princess. King Hung Vuong VI declared war against the spirits. Thuy Tinh raised the mountains while Son Tinh drowned the kingdom.',
                'option_d' => 'The King chose Thuy Tinh because he was richer than Son Tinh. Son Tinh was disappointed and decided to build higher mountains to avoid the monsoon floods.',
                'option_e' => 'The princess decided to reject both suitors because neither of them could bring the required wedding gifts on time.',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => "Teks untuk soal nomor 1 s.d. 5!\n\n" . $stimulusText,
                'question_text' => 'What was the main reason King Hung Vuong VI decided to give Son Tinh and Thuy Tinh a test?',
                'option_a' => 'He wanted to test their physical fighting strength in battle.',
                'option_b' => 'Both suitors were equally talented, powerful, and respected, making it difficult for him to choose.',
                'option_c' => 'He secretly favored Thuy Tinh over Son Tinh.',
                'option_d' => 'His daughter asked him to organize a competition between the princes.',
                'option_e' => 'He did not want either of them to marry his daughter.',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => "Teks untuk soal nomor 1 s.d. 5!\n\n" . $stimulusText,
                'question_text' => 'How did Son Tinh successfully counter Thuy Tinh\'s massive flood attacks?',
                'option_a' => 'He used giant dams to divert the rushing water into the ocean.',
                'option_b' => 'He stayed calm in his palace and made his mountains higher whenever the water level rose.',
                'option_c' => 'He negotiated peace with Thuy Tinh through King Hung Vuong VI.',
                'option_d' => 'He summoned rainstorms to overwhelm Thuy Tinh\'s rivers.',
                'option_e' => 'He retreated deep into underground caves until the monsoon ended.',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => "Teks untuk soal nomor 1 s.d. 5!\n\n" . $stimulusText,
                'question_text' => 'According to the legend described in the passage, what natural phenomenon is explained by Thuy Tinh\'s recurring attacks?',
                'option_a' => 'Volcanic eruptions in Southeast Asia',
                'option_b' => 'Severe droughts during the summer months',
                'option_c' => 'The annual arrival of monsoons and storms in Vietnam',
                'option_d' => 'Earthquakes along the mountainous boundaries',
                'option_e' => 'Tidal waves in the South China Sea',
                'correct_answer' => 'C',
            ],
            [
                'stimulus' => "Teks untuk soal nomor 1 s.d. 5!\n\n" . $stimulusText,
                'question_text' => 'What moral value can be learned from the character of Son Tinh in the story?',
                'option_a' => 'Quick anger leads to greater achievements.',
                'option_b' => 'Patience, fairness, and calm perseverance are greater than destructive vengeance.',
                'option_c' => 'Breaking promises is acceptable when facing powerful opponents.',
                'option_d' => 'Power is determined solely by the number of soldiers you command.',
                'option_e' => 'One should avoid competitions at all costs.',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => null,
                'question_text' => 'Complete the dialogue with the most appropriate conditional sentence: "If the committee had prepared the audio-visual system beforehand, the opening ceremony ______ smoothly."',
                'option_a' => 'would run',
                'option_b' => 'would have run',
                'option_c' => 'will run',
                'option_d' => 'runs',
                'option_e' => 'has run',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => null,
                'question_text' => 'Choose the sentence that correctly uses the passive voice in academic writing:',
                'option_a' => 'The researchers conducted the experiment last month.',
                'option_b' => 'The experiment was conducted by the research team under strict laboratory conditions.',
                'option_c' => 'Conducting experiments is what researchers enjoy most.',
                'option_d' => 'They have been conducting experiments since morning.',
                'option_e' => 'The laboratory is where the researchers conduct the tests.',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => null,
                'question_text' => 'Identify the synonym of the underlined word: "The student showed <u>meticulous</u> attention to detail in writing her research paper."',
                'option_a' => 'Careless',
                'option_b' => 'Painstaking and precise',
                'option_c' => 'Superficial',
                'option_d' => 'Hastily completed',
                'option_e' => 'Ambiguous',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => null,
                'question_text' => 'Which conjunction best expresses concession in the following context: "______ experiencing severe technical glitches, the online examination concluded successfully on schedule."',
                'option_a' => 'Because of',
                'option_b' => 'Despite',
                'option_c' => 'Therefore',
                'option_d' => 'Consequently',
                'option_e' => 'Moreover',
                'correct_answer' => 'B',
            ],
            [
                'stimulus' => null,
                'question_text' => 'Select the most suitable heading for a formal report evaluating digital learning infrastructure in secondary schools:',
                'option_a' => 'Why Computers Are Cool In Class',
                'option_b' => 'Comprehensive Evaluation of Digital Infrastructure and Readiness in Secondary Education',
                'option_c' => 'School Tech Gossip and Reviews',
                'option_d' => 'Buying Better Laptops for Fun',
                'option_e' => 'Let Us Upgrade Our Gadgets',
                'correct_answer' => 'B',
            ],
        ];

        foreach ($questionsData as $q) {
            Question::create([
                'subject_id' => $subjectEng->id,
                'stimulus' => $q['stimulus'],
                'question_text' => $q['question_text'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'option_e' => $q['option_e'],
                'correct_answer' => $q['correct_answer'],
                'score_weight' => 1.0,
                'created_by' => $guruEng->id,
            ]);
        }

        // 7. Active Exam: Bahasa Inggris (Paket 1) - As seen in Image 3, 4, 5!
        $examActive = Exam::create([
            'subject_id' => $subjectEng->id,
            'title' => 'Bahasa Inggris (Paket 1)',
            'category' => 'UTS',
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'duration_minutes' => 120, // 120 minutes as requested
            'passing_grade' => 60.00,
            'start_time' => Carbon::now()->subMinutes(15),
            'end_time' => Carbon::now()->addHours(3),
            'status' => 'active',
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'max_violations' => 3,
            'created_by' => $admin->id,
        ]);

        // 8. Active Token: 4TVSR6 (as seen in Image 3!) valid for 10 minutes
        Token::create([
            'exam_id' => $examActive->id,
            'token_code' => '4TVSR6',
            'valid_until' => Carbon::now()->addMinutes(10), // valid for 10 minutes
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        // 9. Completed Exam (UTS Pemrograman Mobile / Asesmen Selesai)
        // Matching Image 7 & 8: Galih (100.00), Bagas (91.67), Annisa (91.67), Citra (83.33), Ayu (75.00)
        $examCompleted = Exam::create([
            'subject_id' => $subjectEng->id,
            'title' => 'Asesmen Sumatif Bahasa Inggris (Sesi Sebelumnya)',
            'category' => 'UTS',
            'education_level' => 'SMA/MA/SMK/MAK/Sederajat',
            'duration_minutes' => 90,
            'passing_grade' => 60.00,
            'start_time' => Carbon::now()->subDays(2)->setHour(8)->setMinute(0),
            'end_time' => Carbon::now()->subDays(2)->setHour(10)->setMinute(0),
            'status' => 'completed',
            'shuffle_questions' => false,
            'shuffle_options' => false,
            'max_violations' => 3,
            'created_by' => $admin->id,
        ]);

        $completedResults = [
            ['user' => $studentModels[0], 'score' => 100.00, 'correct' => 10, 'incorrect' => 0, 'unanswered' => 0, 'dur_min' => 67],
            ['user' => $studentModels[1], 'score' => 90.00,  'correct' => 9,  'incorrect' => 1, 'unanswered' => 0, 'dur_min' => 65],
            ['user' => $studentModels[2], 'score' => 90.00,  'correct' => 9,  'incorrect' => 1, 'unanswered' => 0, 'dur_min' => 70],
            ['user' => $studentModels[3], 'score' => 80.00,  'correct' => 8,  'incorrect' => 2, 'unanswered' => 0, 'dur_min' => 66],
            ['user' => $studentModels[4], 'score' => 70.00,  'correct' => 7,  'incorrect' => 3, 'unanswered' => 0, 'dur_min' => 64],
        ];

        foreach ($completedResults as $cr) {
            $started = Carbon::now()->subDays(2)->setHour(8)->setMinute(15);
            $finished = (clone $started)->addMinutes($cr['dur_min']);

            ExamAttempt::create([
                'exam_id' => $examCompleted->id,
                'user_id' => $cr['user']->id,
                'token_used' => 'OLDTOK',
                'question_order' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                'started_at' => $started,
                'duration_minutes' => 90,
                'end_at' => (clone $started)->addMinutes(90),
                'finished_at' => $finished,
                'status' => 'finished',
                'violations_count' => 0,
                'score' => $cr['score'],
                'correct_count' => $cr['correct'],
                'incorrect_count' => $cr['incorrect'],
                'unanswered_count' => $cr['unanswered'],
            ]);
        }
    }
}
