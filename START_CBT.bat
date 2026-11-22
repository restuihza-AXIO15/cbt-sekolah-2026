@echo off
title CBT UJIAN SEKOLAH 2026 - PUSMENDIK ANBK
color 0b
echo ===============================================================
echo     APLIKASI CBT UJIAN SEKOLAH & SIMULASI TKA 2026
echo     STANDAR PUSMENDIK KEMDIKBUD - SISTEM KEAMANAN TINGGI
echo ===============================================================
echo.
echo [1/2] Menyiapkan server lokal CBT...
cd /d "%~dp0"
echo.
echo Server berjalan di: http://localhost:8000
echo.
echo Akun Demo:
echo - Admin : username=admin / password=admin123
echo - Guru  : username=guru_bing / password=guru123
echo - Siswa : username=25110009 / password=pass123 (Token: 4TVSR6)
echo.
echo [2/2] Membuka browser...
start http://localhost:8000
echo.
echo Tekan Ctrl+C untuk menghentikan server jika selesai.
echo ===============================================================
php artisan serve --port=8000
pause
