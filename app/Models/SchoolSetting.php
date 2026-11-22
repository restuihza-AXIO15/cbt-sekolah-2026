<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name',
        'app_title',
        'sub_title',
        'school_address',
        'headmaster_name',
        'headmaster_nip',
        'committee_name',
        'academic_year',
        'semester',
        'default_duration',
        'school_logo',
        'logo_kiri',
        'logo_kanan',
    ];

    public static function current(): self
    {
        return self::firstOrCreate([], [
            'school_name' => 'SMKN 15 JAKARTA',
            'app_title' => 'TES KEMAMPUAN AKADEMIK',
            'sub_title' => 'PUSAT ASESMEN PENDIDIKAN & CBT',
            'school_address' => 'Jl. Pendidikan Nasional No. 15, Telp: (021) 7891234, Email: cbt@sman1unggulan.sch.id',
            'headmaster_name' => 'Encep Kusmawan, M.Pd.',
            'headmaster_nip' => '19710312 199602 1 002',
            'committee_name' => 'Panitia Pelaksana CBT Ujian Sekolah 2026',
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'default_duration' => 120,
        ]);
    }

    public function getLogoUrl(): string
    {
        if ($this->school_logo && file_exists(public_path($this->school_logo))) {
            return asset($this->school_logo);
        }
        return asset('assets/tutwuri.svg');
    }
}
