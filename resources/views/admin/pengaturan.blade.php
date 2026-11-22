@extends('layouts.admin')

@section('title', 'Pengaturan Sekolah & CBT - Admin')

@section('content')
<div class="space-y-6 max-w-4xl animate-fade-in">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Pengaturan Sekolah & Sistem CBT</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Konfigurasi identitas satuan pendidikan, logo sekolah, kustomisasi teks sidebar dashboard, dan preferensi ujian.
        </p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 shadow-sm p-6 sm:p-8 transition-colors duration-300">
        <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- 1. Kustomisasi Branding & Sidebar Admin (Sesuai Permintaan User) -->
            <div class="space-y-4 p-5 bg-gradient-to-r from-blue-50/70 to-indigo-50/70 dark:from-blue-950/40 dark:to-indigo-950/40 rounded-xl border border-blue-200 dark:border-blue-900/60">
                <h3 class="font-bold text-sm text-blue-900 dark:text-sky-300 border-b border-blue-200/80 dark:border-blue-800/60 pb-2 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-paintbrush text-[#1e73be] dark:text-sky-400"></i>
                        <span>Kustomisasi Teks Sidebar & Logo Sekolah</span>
                    </div>
                    <span class="text-[10px] bg-blue-100 dark:bg-blue-900/70 text-blue-800 dark:text-blue-200 px-2.5 py-0.5 rounded-full font-semibold">
                        Tampilan Dashboard & Kartu
                    </span>
                </h3>

                <!-- Logo Sekolah Upload & Preview -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                    <div class="md:col-span-3 flex flex-col items-center p-3 bg-white dark:bg-slate-900 rounded-xl border border-blue-200 dark:border-slate-700 shadow-sm">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-2">Logo Aktif Saat Ini:</span>
                        <img id="logoPreview" src="{{ $setting->getLogoUrl() }}" alt="Logo Sekolah" class="w-20 h-20 object-contain drop-shadow p-1">
                    </div>

                    <div class="md:col-span-9 space-y-2">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300">
                            Ganti Logo Sekolah <span class="text-slate-400 dark:text-slate-500 font-normal">(Format: PNG, JPG, JPEG, SVG, WEBP - Maks 3MB)</span>
                        </label>
                        <input type="file" name="logo_file" accept="image/*" onchange="previewImage(event)"
                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 dark:file:bg-blue-950 file:text-blue-700 dark:file:text-sky-400 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-[#1e73be]">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            *Logo ini akan otomatis tampil pada <strong>Sidebar Dashboard Admin, Header Siswa, Halaman Login,</strong> dan <strong>Kop Kartu Peserta Ujian</strong>.
                        </p>
                        @if($setting->school_logo)
                            <label class="flex items-center space-x-2 text-rose-600 dark:text-rose-400 font-medium pt-1 cursor-pointer">
                                <input type="checkbox" name="reset_logo" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                <span class="text-[11px]">Kembalikan ke Logo Standar Kemdikbud (Tut Wuri Handayani)</span>
                            </label>
                        @endif
                    </div>
                </div>

                <!-- Kustomisasi Teks Sidebar Kiri -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Teks Utama Sidebar Kiri <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="app_title" required value="{{ old('app_title', $setting->app_title ?? 'TES KEMAMPUAN AKADEMIK') }}"
                            placeholder="Contoh: TES KEMAMPUAN AKADEMIK / UJIAN SEKOLAH CBT"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-[#1e73be]">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500">*Teks tebal di bawah logo pada sidebar kiri admin</span>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Sub-Teks Sidebar Kiri <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sub_title" required value="{{ old('sub_title', $setting->sub_title ?? 'PUSAT ASESMEN PENDIDIKAN & CBT') }}"
                            placeholder="Contoh: PUSAT ASESMEN PENDIDIKAN & CBT / SMKN 15 JAKARTA"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500">*Teks baris kedua di sidebar kiri admin</span>
                    </div>
                </div>
            </div>

            <!-- 2. Identitas Satuan Pendidikan -->
            <div class="space-y-4">
                <h3 class="font-bold text-sm text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2 flex items-center space-x-2">
                    <i class="fa-solid fa-school text-[#1e73be] dark:text-sky-400"></i>
                    <span>Identitas Satuan Pendidikan</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Satuan Pendidikan / Sekolah</label>
                        <input type="text" name="school_name" required value="{{ old('school_name', $setting->school_name) }}"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-[#1e73be]">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Panitia Ujian CBT</label>
                        <input type="text" name="committee_name" required value="{{ old('committee_name', $setting->committee_name) }}"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap & Kontak Sekolah</label>
                    <textarea name="school_address" rows="2" required 
                        class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">{{ old('school_address', $setting->school_address) }}</textarea>
                </div>
            </div>

            <!-- 3. Pejabat Penandatangan -->
            <div class="space-y-4 pt-2">
                <h3 class="font-bold text-sm text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2 flex items-center space-x-2">
                    <i class="fa-solid fa-signature text-[#1e73be] dark:text-sky-400"></i>
                    <span>Kepala Sekolah (Untuk Tanda Tangan Kartu Peserta)</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kepala Sekolah & Gelar</label>
                        <input type="text" name="headmaster_name" required value="{{ old('headmaster_name', $setting->headmaster_name) }}"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIP Kepala Sekolah</label>
                        <input type="text" name="headmaster_nip" required value="{{ old('headmaster_nip', $setting->headmaster_nip) }}"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg font-mono text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                </div>
            </div>

            <!-- 4. Preferensi Ujian & Akademik -->
            <div class="space-y-4 pt-2">
                <h3 class="font-bold text-sm text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-2 flex items-center space-x-2">
                    <i class="fa-solid fa-sliders text-[#1e73be] dark:text-sky-400"></i>
                    <span>Preferensi CBT & Tahun Ajaran</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Ajaran</label>
                        <input type="text" name="academic_year" required value="{{ old('academic_year', $setting->academic_year) }}"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg font-semibold text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Semester</label>
                        <select name="semester" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#1e73be]">
                            <option value="Ganjil" {{ $setting->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ $setting->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Durasi Default Per Mapel (Menit)</label>
                        <input type="number" name="default_duration" required min="10" max="300" value="{{ old('default_duration', $setting->default_duration ?: 120) }}"
                            class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg font-bold text-blue-700 dark:text-sky-400 focus:ring-2 focus:ring-[#1e73be]">
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                <button type="submit" 
                    class="interactive-click px-6 py-2.5 bg-[#1e73be] hover:bg-[#155d9b] text-white font-bold rounded-lg shadow-sm transition-all duration-200 flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
