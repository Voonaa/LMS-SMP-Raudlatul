<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\LogAktivitas;
use App\Models\HasilKuis;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\ForumThread;
use App\Models\Like;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class SyntheticDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker     = Faker::create('id_ID');
        $kelasList = Kelas::pluck('id')->toArray();

        if (empty($kelasList)) {
            $this->command->info('Tidak ada data kelas. Jalankan DatabaseSeeder dulu.');
            return;
        }

        // ============================================================
        // BERSIHKAN DATA LAMA
        // ============================================================
        $this->command->info('Menghapus data sintetis lama...');
        User::where('username', 'like', 'siswa_synth_%')->delete();

        // ============================================================
        // 1. BUAT 160 SISWA dengan distribusi keaktifan yang realistis
        //    Porsi: 30% Sangat Aktif | 45% Sedang | 25% Pasif
        // ============================================================
        $students = [];
        $this->command->info('Membuat 160 siswa sintetis...');

        for ($i = 1; $i <= 160; $i++) {
            $students[] = User::create([
                'name'     => $faker->name,
                'username' => 'siswa_synth_' . $i,
                'password' => Hash::make('password123'),
                'role'     => 'siswa',
                'kelas_id' => $faker->randomElement($kelasList),
            ]);
        }

        // ============================================================
        // 2. GENERATE LOG AKTIVITAS dengan pola koheren per-mapel
        //    Setiap siswa punya 1-2 "mata pelajaran favorit" yang dibaca
        //    lebih sering. Ini menciptakan pola yang dikenali oleh CF.
        // ============================================================
        $this->command->info('Membuat log aktivitas berdasarkan pola koheren per-mapel...');

        foreach ($students as $index => $student) {

            // Tentukan tingkat keaktifan
            $pct = rand(1, 100);
            if ($pct <= 30) {
                $readCount  = rand(25, 45);  // Sangat Aktif
                $quizCount  = rand(5, 10);
                $forumCount = rand(3, 7);
            } elseif ($pct <= 75) {
                $readCount  = rand(12, 24);  // Sedang
                $quizCount  = rand(2, 5);
                $forumCount = rand(1, 3);
            } else {
                $readCount  = rand(3, 8);   // Pasif
                $quizCount  = rand(0, 2);
                $forumCount = rand(0, 1);
            }

            // Ambil semua materi dan mata pelajaran yang tersedia untuk kelas ini
            $materiKelas = Materi::where('kelas_id', $student->kelas_id)
                ->get(['id', 'mata_pelajaran_id']);
            $kuisKelas   = Kuis::where('kelas_id', $student->kelas_id)->pluck('id')->toArray();
            $threadKelas = ForumThread::where('kelas_id', $student->kelas_id)->pluck('id')->toArray();

            if ($materiKelas->isEmpty()) continue;

            // Tentukan 1 atau 2 mata pelajaran "favorit" untuk siswa ini
            $allMapelIds = $materiKelas->pluck('mata_pelajaran_id')->unique()->toArray();
            $favMapelCount = min(count($allMapelIds), rand(1, 2));
            shuffle($allMapelIds);
            $favMapelIds = array_slice($allMapelIds, 0, $favMapelCount);

            // Pisahkan materi favorit vs non-favorit
            $materiNonFav = $materiKelas->whereNotIn('mata_pelajaran_id', $favMapelIds)
                ->pluck('id')->toArray();
            $materiFav    = $materiKelas->whereIn('mata_pelajaran_id', $favMapelIds)
                ->pluck('id')->toArray();

            // --- BACA MATERI: 75% dari mapel favorit, 25% dari mapel lain ---
            for ($j = 0; $j < $readCount; $j++) {

                $useFav = (rand(1, 100) <= 75) && !empty($materiFav);
                $pool   = $useFav ? $materiFav : ($materiNonFav ?: $materiFav);

                LogAktivitas::create([
                    'user_id'         => $student->id,
                    'jenis_aktivitas' => 'baca_materi',
                    'item_id'         => $faker->randomElement($pool),
                    // Durasi favorit lebih lama → higher implicit rating
                    'durasi'          => $useFav ? rand(180, 600) : rand(60, 200),
                    'created_at'      => $faker->dateTimeBetween('-2 months', 'now'),
                ]);
            }

            // --- KERJAKAN KUIS ---
            if (!empty($kuisKelas)) {
                // Kuis terkait mapel favorit mendapat skor lebih tinggi
                $kuisFav = Kuis::where('kelas_id', $student->kelas_id)
                    ->whereIn('mata_pelajaran_id', $favMapelIds)
                    ->pluck('id')->toArray();
                $kuisLain = Kuis::where('kelas_id', $student->kelas_id)
                    ->whereNotIn('mata_pelajaran_id', $favMapelIds)
                    ->pluck('id')->toArray();

                for ($q = 0; $q < $quizCount; $q++) {
                    $useFavKuis = (rand(1, 100) <= 70) && !empty($kuisFav);
                    $kuisPool   = $useFavKuis ? $kuisFav : ($kuisLain ?: $kuisFav);
                    $kuisId     = $faker->randomElement($kuisPool);
                    $skor       = $useFavKuis ? rand(78, 100) : rand(55, 85);

                    HasilKuis::create([
                        'user_id'    => $student->id,
                        'kuis_id'    => $kuisId,
                        'nilai'      => $skor,
                        'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                    ]);

                    LogAktivitas::create([
                        'user_id'         => $student->id,
                        'jenis_aktivitas' => 'kerjakan_kuis',
                        'item_id'         => $kuisId,
                        'created_at'      => $faker->dateTimeBetween('-2 months', 'now'),
                    ]);
                }
            }

            // --- LIKE FORUM ---
            if (!empty($threadKelas)) {
                for ($f = 0; $f < $forumCount; $f++) {
                    $threadId = $faker->randomElement($threadKelas);

                    Like::firstOrCreate([
                        'user_id'       => $student->id,
                        'likeable_id'   => $threadId,
                        'likeable_type' => 'App\\Models\\ForumThread',
                    ]);

                    LogAktivitas::create([
                        'user_id'         => $student->id,
                        'jenis_aktivitas' => 'like_forum',
                        'item_id'         => $threadId,
                        'created_at'      => $faker->dateTimeBetween('-2 months', 'now'),
                    ]);
                }
            }
        }

        $this->command->info('Seeding selesai! 160 siswa sintetis telah dibuat dengan pola interaksi koheren.');
    }
}
