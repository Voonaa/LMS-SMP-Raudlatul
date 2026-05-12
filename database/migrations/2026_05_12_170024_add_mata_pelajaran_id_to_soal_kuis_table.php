<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal_kuis', function (Blueprint $table) {
            // Untuk kuis diagnostik: setiap soal bisa punya mapel berbeda
            $table->foreignId('mata_pelajaran_id')
                ->nullable()
                ->after('kuis_id')
                ->constrained('mata_pelajaran')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('soal_kuis', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\MataPelajaran::class);
            $table->dropColumn('mata_pelajaran_id');
        });
    }
};
