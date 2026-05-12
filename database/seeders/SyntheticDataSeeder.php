<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\HasilKuis;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\ForumThread;
use App\Models\Like;
use App\Models\PointGamifikasi;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class SyntheticDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $kelasList = Kelas::pluck('id')->toArray();
        $materiList = Materi::pluck('id')->toArray();
        $kuisList = Kuis::pluck('id')->toArray();
        $threadList = ForumThread::pluck('id')->toArray();

        if(empty($kelasList)) {
            $this->command->info('Tidak ada data kelas. Jalankan DatabaseSeeder dulu.');
            return;
        }

        // Hapus data lama agar bisa di-seed berulang kali
        $this->command->info('Menghapus data sintetis lama...');
        User::where('username', 'like', 'siswa_synth_%')->delete();

        // 1. Buat 138 Siswa Acak
        $students = [];
        $this->command->info('Membuat 138 siswa...');
        for ($i = 1; $i <= 138; $i++) {
            $students[] = User::create([
                'name' => $faker->name,
                'username' => 'siswa_synth_' . $i,
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'kelas_id' => $faker->randomElement($kelasList),
            ]);
        }

        // 2. Tentukan Profil Keaktifan
        // 20% Sangat Aktif, 50% Sedang, 30% Pasif
        $this->command->info('Membuat log aktivitas dan hasil kuis...');
        
        foreach ($students as $index => $student) {
            // Tentukan tingkat keaktifan
            $rand = rand(1, 100);
            if ($rand <= 20) {
                $activityCount = rand(30, 50); // Sangat Aktif
            } elseif ($rand <= 70) {
                $activityCount = rand(10, 25); // Sedang
            } else {
                $activityCount = rand(1, 5);   // Pasif
            }

            // --- CONTEXT-AWARE SEEDING: Filter item berdasarkan kelas siswa ---
            $materiKelas = Materi::where('kelas_id', $student->kelas_id)->pluck('id')->toArray();
            $kuisKelas   = Kuis::where('kelas_id', $student->kelas_id)->pluck('id')->toArray();
            $threadKelas = ForumThread::where('kelas_id', $student->kelas_id)->pluck('id')->toArray();

            for ($j = 0; $j < $activityCount; $j++) {
                $actionType = $faker->randomElement(['baca_materi', 'kerjakan_kuis', 'like_forum']);

                if ($actionType === 'baca_materi' && !empty($materiKelas)) {
                    LogAktivitas::create([
                        'user_id' => $student->id,
                        'jenis_aktivitas' => 'baca_materi',
                        'item_id' => $faker->randomElement($materiKelas),
                        'durasi' => rand(100, 500),
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now')
                    ]);
                } elseif ($actionType === 'kerjakan_kuis' && !empty($kuisKelas)) {
                    $kuisId = $faker->randomElement($kuisKelas);
                    $skor = rand(60, 100);
                    
                    LogAktivitas::create([
                        'user_id' => $student->id,
                        'jenis_aktivitas' => 'kerjakan_kuis',
                        'item_id' => $kuisId,
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now')
                    ]);

                    HasilKuis::create([
                        'user_id' => $student->id,
                        'kuis_id' => $kuisId,
                        'nilai' => $skor,
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now')
                    ]);
                } elseif ($actionType === 'like_forum' && !empty($threadKelas)) {
                    $threadId = $faker->randomElement($threadKelas);
                    
                    Like::firstOrCreate([
                        'user_id' => $student->id,
                        'likeable_id' => $threadId,
                        'likeable_type' => 'App\Models\ForumThread'
                    ]);

                    LogAktivitas::create([
                        'user_id' => $student->id,
                        'jenis_aktivitas' => 'like_forum',
                        'item_id' => $threadId,
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now')
                    ]);
                }
            }
        }
        
        $this->command->info('Seeding data sintetis selesai!');
    }
}
