<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('app_title')->default('TES KEMAMPUAN AKADEMIK')->after('school_name');
            $table->string('sub_title')->default('PUSAT ASESMEN PENDIDIKAN & CBT')->after('app_title');
            $table->string('school_logo')->nullable()->after('default_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['app_title', 'sub_title', 'school_logo']);
        });
    }
};
