<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            // Flag kuis diagnostik — tidak terikat kelas tertentu
            $table->boolean('is_diagnostik')->default(false)->after('guru_id');
            // Buat kelas_id nullable agar kuis diagnostik bisa lintas kelas
            $table->foreignId('kelas_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kuis', function (Blueprint $table) {
            $table->dropColumn('is_diagnostik');
            $table->foreignId('kelas_id')->nullable(false)->change();
        });
    }
};
