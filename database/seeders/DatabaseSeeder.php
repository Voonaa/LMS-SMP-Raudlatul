<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\SoalKuis;
use App\Models\HasilKuis;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\LogAktivitas;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kelas (3 tingkat)
        $kelas = [];
        $kelas[] = Kelas::create(['nama_kelas' => '7A', 'tingkat' => 7]);
        $kelas[] = Kelas::create(['nama_kelas' => '8A', 'tingkat' => 8]);
        $kelas[] = Kelas::create(['nama_kelas' => '9A', 'tingkat' => 9]);

        // 2. Mata Pelajaran (5 Utama)
        $mapel = [];
        $mapelList = ['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris'];
        foreach ($mapelList as $m) {
            $mapel[] = MataPelajaran::create(['nama_mapel' => $m, 'deskripsi' => 'Deskripsi untuk ' . $m]);
        }

        // 3. Admin (1)
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // 4. Guru (5)
        $gurus = [];
        for ($i = 1; $i <= 5; $i++) {
            $gurus[] = User::create([
                'name' => 'Guru ' . $i,
                'username' => '19800101200501100' . $i, // 18 digit NIP dummy
                'password' => Hash::make('password'),
                'role' => 'guru'
            ]);
        }

        // 5. Siswa (15) + agus_test
        $siswas = [];
        // 5 siswa di kelas 7A
        for ($i = 1; $i <= 5; $i++) {
            $siswas[] = User::create([
                'name' => 'Siswa 7A-' . $i,
                'username' => '001002003' . $i,
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'kelas_id' => $kelas[0]->id
            ]);
        }
        // 5 siswa di kelas 8A
        for ($i = 1; $i <= 5; $i++) {
            $siswas[] = User::create([
                'name' => 'Siswa 8A-' . $i,
                'username' => '002002003' . $i,
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'kelas_id' => $kelas[1]->id
            ]);
        }
        // 4 siswa di kelas 9A
        for ($i = 1; $i <= 4; $i++) {
            $siswas[] = User::create([
                'name' => 'Siswa 9A-' . $i,
                'username' => '003002003' . $i,
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'kelas_id' => $kelas[2]->id
            ]);
        }

        // User khusus: agus_test (Kelas 9A)
        $agus = User::create([
            'name' => 'Agus Test',
            'username' => '0090090099',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'kelas_id' => $kelas[2]->id
        ]);
        $siswas[] = $agus;

        // --- DISTRIBUSI MATERI & KUIS UNTUK SEMUA KELAS ---
        foreach ($kelas as $k) {
            foreach ($mapel as $m) {
                // Buat 1 Materi per Mapel per Kelas
                $materi = Materi::create([
                    'judul' => 'Materi ' . $m->nama_mapel . ' Kelas ' . $k->nama_kelas,
                    'konten' => '<p>Ini adalah konten materi ' . $m->nama_mapel . ' untuk kelas ' . $k->nama_kelas . '.</p>',
                    'mata_pelajaran_id' => $m->id,
                    'kelas_id' => $k->id,
                    'guru_id' => $gurus[rand(0, 4)]->id
                ]);

                // Buat 1 Kuis untuk materi tersebut
                $kuis = Kuis::create([
                    'judul' => 'Kuis ' . $m->nama_mapel . ' Kelas ' . $k->nama_kelas,
                    'deskripsi' => 'Evaluasi materi ' . $m->nama_mapel,
                    'materi_id' => $materi->id,
                    'mata_pelajaran_id' => $m->id,
                    'kelas_id' => $k->id,
                    'guru_id' => $materi->guru_id
                ]);

                // Buat 1 Tugas untuk materi tersebut
                \App\Models\Tugas::create([
                    'judul' => 'Tugas ' . $m->nama_mapel . ' Kelas ' . $k->nama_kelas,
                    'deskripsi' => 'Silakan kerjakan ringkasan materi ' . $m->nama_mapel . ' dan kumpulkan dalam format PDF.',
                    'mata_pelajaran_id' => $m->id,
                    'kelas_id' => $k->id,
                    'guru_id' => $materi->guru_id,
                    'tenggat_waktu' => now()->addDays(7)
                ]);

                // Tambahkan 2 soal dummy
                SoalKuis::create([
                    'kuis_id' => $kuis->id,
                    'pertanyaan' => 'Apa ibukota Indonesia?',
                    'opsi_a' => 'Jakarta', 'opsi_b' => 'Surabaya', 'opsi_c' => 'Bandung', 'opsi_d' => 'Medan',
                    'jawaban_benar' => 'A', 'bobot' => 10
                ]);
                SoalKuis::create([
                    'kuis_id' => $kuis->id,
                    'pertanyaan' => '2 + 2 = ?',
                    'opsi_a' => '3', 'opsi_b' => '4', 'opsi_c' => '5', 'opsi_d' => '6',
                    'jawaban_benar' => 'B', 'bobot' => 10
                ]);
            }
        }

        // Interaksi Dummy untuk agus_test agar Collaborative Filtering jalan
        $materiAgus = Materi::where('kelas_id', $agus->kelas_id)->first();
        if ($materiAgus) {
            LogAktivitas::create([
                'user_id' => $agus->id,
                'jenis_aktivitas' => 'baca_materi',
                'item_id' => $materiAgus->id,
                'durasi' => 300
            ]);
        }
    }
}
