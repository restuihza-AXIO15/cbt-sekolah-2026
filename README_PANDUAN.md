# PANDUAN LENGKAP APLIKASI CBT 2026 (STANDAR ANBK PUSMENDIK)

Aplikasi Computer Based Test (CBT) Berbasis Laravel 12 yang dirancang khusus untuk ujian sekolah, penilaian tengah semester (UTS), penilaian akhir semester (UAS), dan simulasi TKA/ANBK dengan tata letak visual presisi tinggi dan keamanan berlapis.

---

## 🚀 Cara Menjalankan Aplikasi

1. **Cara Cepat (1 Klik):**
   - Buka folder `E:\DOKUM\UJIAN (15)\UJI COBA\CBT 2026`
   - Klik dua kali file **`START_CBT.bat`**
   - Browser akan otomatis terbuka di `http://localhost:8000`

2. **Cara Manual (Terminal/Command Prompt):**
   ```bash
   cd "E:\DOKUM\UJIAN (15)\UJI COBA\CBT 2026"
   php artisan serve --port=8000
   ```

---

## 🔑 Akun Pengujian Sistem (Demo Data)

| Peran | Username | Password | Keterangan & Hak Akses |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Akses penuh: master data, rilis token (10m), proctoring, cetak kartu, atur waktu (120m), reset ujian. |
| **Guru (Bahasa Inggris)** | `guru_bing` | `guru123` | **Hanya bisa**: menambah/mengelola butir soal di Bank Soal & mengambil/melihat rekap nilai saat asesmen selesai. |
| **Guru (Matematika)** | `guru_mat` | `guru123` | Guru mata pelajaran Matematika. |
| **Siswa 1** | `25110009` | `pass123` | Galih Pramudya (XII MIPA 1) - Ujian Baru. |
| **Siswa 2** | `25110007` | `pass123` | Bagas Saputra (XII MIPA 1). |
| **Siswa 3** | `25110012` | `pass123` | Annisa Rahma (XII MIPA 1). |

*Token Aktif Bawaan:* **`4TVSR6`** *(Dapat diperbarui/dirilis ulang oleh Admin melalui menu Token)*

---

## 🎨 Penyesuaian & Pembaruan Terkini (Sesuai Permintaan)

1. **Kustomisasi Teks Sidebar Kiri Dashboard Admin:**
   - Tulisan "TES KEMAMPUAN AKADEMIK" dan sub-judul di bawah logo kini **dapat diubah secara fleksibel** melalui menu **Pengaturan Akun & Sekolah** (`/admin/pengaturan`).
   - Judul dan sub-judul otomatis tersinkronisasi ke sidebar admin.

2. **Ganti Logo Sekolah:**
   - Menu **Pengaturan** kini menyediakan formulir **Upload Logo Sekolah** (PNG, JPG, SVG, WebP).
   - Logo yang diunggah otomatis tampil pada:
     - Sidebar Dashboard Admin
     - Header Siswa & Guru
     - Halaman Login
     - Kop Kartu Peserta Ujian
   - Tersedia opsi untuk mengembalikan ke logo standar Kemdikbud sewaktu-waktu.

3. **Alur Langsung ke Login (Halaman Simulasi TKA Dihilangkan):**
   - Rute utama (`/`) kini **langsung menuju ke halaman Login Siswa** tanpa melalui tahapan pemilihan jenjang/simulasi yang tidak diperlukan.

4. **Token Tidak Ditampilkan di Layar Siswa:**
   - Kotak token pada layar konfirmasi siswa telah dihilangkan total. Siswa **tidak dapat melihat token di layar monitornya**.
   - Token resmi dikeluarkan oleh Admin/Proktor dan diumumkan secara langsung di ruang ujian saat ujian dimulai.

5. **Input Manual Nama, Tanggal Lahir, dan Token:**
   - Kolom **Nama Peserta** wajib diketik secara manual (kosong pada awalnya).
   - Dropdown **Tanggal Lahir** (Hari, Bulan, Tahun) harus dipilih secara manual (tidak ada opsi terisi otomatis).
   - Kolom **Token** wajib diketikkan manual 6 karakter dari pengawas (tombol autofill dihapus).
   - Sistem backend memvalidasi kecocokan ketikan siswa dengan data profil terdaftar untuk mencegah salah akun.

6. **Animasi Mulus (Smooth Transitions):**
   - Ditambahkan animasi `fade-in-up` yang halus saat memuat kartu dan halaman.
   - Efek hover mikro pada tombol (`hover:-translate-y-0.5`) dan pilihan ganda interaktif.
   - Transisi pergeseran butir soal yang lembut saat berpindah nomor ujian.

1. **Halaman Awal Siswa (Gambar 1):**
   - Desain kartu terpusat dengan ikon toga biru bulat Pusmendik.
   - Pilihan Jenjang Pendidikan (`SMA/MA/SMK/MAK/Sederajat`), Jenis Mapel (`Mata Pelajaran Wajib`), dan Dropdown Mapel.
   - Tombol `▶ Mulai Simulasi` menuju login siswa.

2. **Halaman Login Siswa (Gambar 2):**
   - Header biru khas Pusmendik dengan lambang Tut Wuri Handayani.
   - Modal login putih rapi dengan input username (ikon user) dan password (ikon gembok + fitur intip mata `eye reveal`).

3. **Konfirmasi Data Peserta (Gambar 3):**
   - Kotak kiri: `Token : 4TVSR6` dengan tombol `[Refresh]` dan countdown hitung mundur masa berlaku 10 menit.
   - Kartu kanan: Biodata otomatis (Username, Nama, Jenis Kelamin, Mata Ujian) + Verifikasi ketik Nama Peserta, Tanggal Lahir (Hari, Bulan, Tahun), dan Input Token.

4. **Konfirmasi Tes (Gambar 4):**
   - Kartu ringkasan tes: Nama Tes (`Bahasa Inggris (Paket 1)`), Status Tes (`Tes Baru`), Waktu Tes, dan Alokasi Waktu (`120 Menit` sesuai permintaan).
   - Tombol `Mulai` berlatar biru rounded.

5. **Lembar Pengerjaan Ujian CBT (Gambar 5):**
   - Bar atas: Label `Soal nomor X`, pembesar font interaktif `A A A` (Kecil, Sedang, Besar).
   - Tombol `[INFORMASI SOAL]`, pill `[Sisa Waktu : HH:MM:SS]`, dan tombol `[Daftar Soal ▦]`.
   - Layout 2 kolom: Kiri teks stimulus/bacaan (Legenda King Hung Vuong VI & Son Tinh) dengan scroll halus, kanan butir pertanyaan & pilihan ganda A, B, C, D, E berdesain radio button profesional.
   - Navigasi bawah: `[◄ Soal sebelumnya]` (merah koral), `[☐ Ragu-ragu]` (kuning), dan `[Soal berikutnya ►]` / `[Selesai Tes]` (biru/hijau).
   - Drawer `Daftar Soal`: Grid nomor interaktif dengan indikator warna (Biru = Dijawab, Kuning = Ragu-ragu, Putih = Belum).

6. **Dashboard Admin (Gambar 6):**
   - Sidebar tema Pusat Asesmen Pendidikan / ANBK dengan menu lengkap, jalan pintas atas `[Dashboard] [Pengaturan] [Keluar]`.
   - Banner Posko Pengumuman Akademik resmi.

7. **Detail & Rekap Nilai (Gambar 7 & 8):**
   - Kartu ringkasan ujian: Mata Pelajaran, Jenjang, Badge Kategori `[UTS]`, Tanggal (120 menit), KKM (60.00 / 75.00), Total Peserta.
   - Tabel `Daftar Nilai Peserta` lengkap: No, NIM/NISN, Nama, Status (`Selesai` badge hijau), Nilai, Benar, Salah, Tidak Dijawab, Durasi (`67 menit 0 detik`), Keterangan (`LULUS` / `REMIDIAL`).

---

## 🛡️ Fitur Keamanan Anti-Curang (Backend Driven)

1. **Deteksi Berpindah Tab / Window Blur:**
   - Event `visibilitychange` & `blur` mendeteksi siswa yang meminimalkan browser atau membuka tab/aplikasi lain.
   - Muncul modal peringatan pelanggaran.
   - Dicatat langsung ke backend API `POST /api/exam/log-violation`.
   - Jika mencapai batas 3x strike, **ujian siswa langsung dikunci otomatis** dan diarahkan ke halaman terkunci. Admin dapat membuka kunci melalui menu *Manajemen Pengawasan*.
2. **Pencegahan Screenshot (Tangkapan Layar):**
   - Mendeteksi penekanan tombol `PrintScreen`.
   - Clipboard otomatis dikosongkan (`clipboard.writeText('')`) dan layar ditutupi sejenak dengan overlay hitam peringatan.
3. **Blokir Salin / Tempel Teks (Copy/Paste):**
   - CSS `user-select: none;` mencegah penyorotan teks soal.
   - Event `copy`, `cut`, `paste`, dan `dragstart` diblokir total.
4. **Blokir Inspect Element & Source Code:**
   - Klik kanan (`contextmenu`) dinonaktifkan.
   - Shortcut keyboard `F12`, `Ctrl+Shift+I`, `Ctrl+Shift+J`, `Ctrl+Shift+C`, `Ctrl+U`, `Ctrl+S`, `Ctrl+P` dicegah.
5. **Backend-Driven Architecture (Kunci Jawaban Aman di Server):**
   - Kunci jawaban tidak pernah dikirim ke frontend siswa.
   - Penyimpanan jawaban dilakukan secara instan via API `POST /api/exam/save-answer` yang diproteksi token CSRF dan sesi autentikasi.
   - Validasi sisa waktu dilakukan di sisi server (waktu server tidak bisa dimanipulasi dengan mengubah jam di laptop siswa).
   - Pengacakan nomor soal dan opsi jawaban per peserta.

---

## 🖨️ Fitur Cetak Kartu Peserta Ujian

- Dapat diakses di menu **Data Peserta -> Cetak Kartu Peserta** (`/admin/cetak-kartu`).
- Tata letak kartu standar A4 (2 kartu per baris, 4 kartu per lembar).
- Dilengkapi Kop Resmi Sekolah, Logo Tut Wuri, Logo Sekolah, Foto 3x4 placeholder, Barcode, NISN, Nama, Kelas, Ruang, Sesi, Username, dan Password siswa, serta kolom tanda tangan Kepala Sekolah.
- Siap cetak langsung ke printer atau ekspor ke PDF.
