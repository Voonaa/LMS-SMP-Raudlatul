<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\SoalKuis;
use App\Models\LogAktivitas;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CFTestingSeeder extends Seeder
{
    public function run()
    {
        // 1. Siapkan Kelas 7A
        $kelas7A = Kelas::firstOrCreate(
            ['nama_kelas' => '7A'],
            ['tingkat' => 7]
        );

        // 2. Siapkan Mapel Matematika
        $mapelMath = MataPelajaran::firstOrCreate(
            ['nama_mapel' => 'Matematika'],
            ['deskripsi' => 'Mata pelajaran berhitung dan logika']
        );

        // 3. Ambil Guru
        $guru = User::where('role', 'guru')->first();
        if (!$guru) {
            $guru = User::create([
                'name' => 'Guru Matematika',
                'username' => '123456789012345678',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'kelas_id' => $kelas7A->id
            ]);
        }

        // 4. Update agus_test agar masuk kelas 7A
        $agus = User::firstOrCreate(
            ['username' => 'agus_test'],
            [
                'name' => 'Agus (Target CF)',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]
        );
        $agus->kelas_id = $kelas7A->id;
        $agus->save();

        // 5. Buat 5 Siswa Umpan di Kelas 7A (Siswa Super Aktif)
        $umpanStudents = [];
        for ($i = 1; $i <= 5; $i++) {
            $umpanStudents[] = User::firstOrCreate(
                ['username' => 'umpan7A_' . $i],
                [
                    'name' => 'Siswa Umpan ' . $i,
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                    'kelas_id' => $kelas7A->id
                ]
            );
        }

        // 6. Buat 5 Materi Seri Aljabar
        $materis = [];
        for ($i = 1; $i <= 5; $i++) {
            $materis[] = Materi::firstOrCreate(
                [
                    'judul' => 'Aljabar Bagian ' . $i,
                    'kelas_id' => $kelas7A->id,
                    'mata_pelajaran_id' => $mapelMath->id
                ],
                [
                    'konten' => 'Isi materi aljabar yang sangat panjang dan butuh durasi baca...',
                    'guru_id' => $guru->id
                ]
            );
        }

        // 7. Buat 5 Kuis Aljabar
        $kuises = [];
        foreach ($materis as $materi) {
            $kuis = Kuis::firstOrCreate(
                [
                    'judul' => 'Kuis ' . $materi->judul,
                    'kelas_id' => $kelas7A->id,
                    'materi_id' => $materi->id,
                ],
                [
                    'deskripsi' => 'Ujian pemahaman ' . $materi->judul,
                    'mata_pelajaran_id' => $mapelMath->id,
                    'guru_id' => $guru->id
                ]
            );
            $kuises[] = $kuis;
            
            // Beri soal dummy jika kosong
            if ($kuis->soal()->count() == 0) {
                SoalKuis::create([
                    'kuis_id' => $kuis->id,
                    'pertanyaan' => 'Berapa nilai x?',
                    'opsi_a' => '1', 'opsi_b' => '2', 'opsi_c' => '3', 'opsi_d' => '4',
                    'jawaban_benar' => 'A'
                ]);
            }
        }

        // 8. Generate Synthetic Interactions untuk Siswa Umpan
        // Mereka membaca semua materi, dan nilai kuisnya 90 (skor +5)
        foreach ($umpanStudents as $siswa) {
            foreach ($materis as $materi) {
                LogAktivitas::firstOrCreate([
                    'user_id' => $siswa->id,
                    'jenis_aktivitas' => 'baca_materi',
                    'item_id' => $materi->id,
                ], [
                    'durasi' => rand(120, 300) // 2-5 menit
                ]);
            }

            foreach ($kuises as $kuis) {
                HasilKuis::firstOrCreate([
                    'user_id' => $siswa->id,
                    'kuis_id' => $kuis->id,
                ], [
                    'nilai' => 90
                ]);
                LogAktivitas::firstOrCreate([
                    'user_id' => $siswa->id,
                    'jenis_aktivitas' => 'kerjakan_kuis',
                    'item_id' => $kuis->id,
                ], [
                    'durasi' => rand(60, 180)
                ]);
            }
        }

        // 9. Generate Interaction untuk agus_test (Target CF)
        // Agus hanya membaca Materi 1 dan Materi 2, nilai kuisnya jelek (tidak dapat poin kuis +5).
        // Sehingga CF harusnya akan merekomendasikan Materi 3, 4, 5 dengan skor similarity tinggi
        // karena Umpan membaca semua.
        for ($i = 0; $i < 2; $i++) {
            LogAktivitas::firstOrCreate([
                'user_id' => $agus->id,
                'jenis_aktivitas' => 'baca_materi',
                'item_id' => $materis[$i]->id,
            ], [
                'durasi' => rand(120, 300)
            ]);
            
            HasilKuis::firstOrCreate([
                'user_id' => $agus->id,
                'kuis_id' => $kuises[$i]->id,
            ], [
                'nilai' => 50 // Tidak dapat skor +5 karena tidak > 80
            ]);
        }

        $this->command->info('CFTestingSeeder berhasil dieksekusi. Data Umpan dan Target siap diuji.');
    }
}
