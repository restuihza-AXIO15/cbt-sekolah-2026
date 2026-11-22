<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\SchoolSetting;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilan Awal: Simulasi TKA / CBT Ujian Sekolah (Sesuai Gambar 1)
     */
    public function showLanding()
    {
        $setting = SchoolSetting::current();
        $subjects = Subject::orderBy('name')->get();
        $activeExam = Exam::where('status', 'active')->first();

        return view('auth.landing', compact('setting', 'subjects', 'activeExam'));
    }

    /**
     * Tampilan Login Siswa / Pengguna (Sesuai Gambar 2)
     */
    public function showLogin()
    {
        $setting = SchoolSetting::current();
        return view('auth.login', compact('setting'));
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password'], 'is_active' => true])) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isGuru()) {
                return redirect()->intended(route('guru.dashboard'));
            } else {
                return redirect()->intended(route('siswa.konfirmasi'));
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('username');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('logout_success', true)
            ->with('success', 'Sesi Anda telah berhasil diakhiri secara aman. Data pengerjaan tersimpan di server CBT.');
    }
}
