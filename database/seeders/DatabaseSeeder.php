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
                'username' => '001002003' . $i, // 10 digit NISN dummy
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

        // Dummy Data untuk Algoritma (Materi, Kuis, Log Aktivitas) di Kelas 9A
        $kelas9A = $kelas[2];
        $guruMath = $gurus[0];
        $mapelMath = $mapel[0];

        // Buat 3 Materi Matematika untuk Kelas 9A
        $materi1 = Materi::create([
            'judul' => 'Aljabar Lanjut',
            'konten' => 'Isi materi aljabar lanjut',
            'mata_pelajaran_id' => $mapelMath->id,
            'kelas_id' => $kelas9A->id,
            'guru_id' => $guruMath->id
        ]);
        $materi2 = Materi::create([
            'judul' => 'Geometri Ruang',
            'konten' => 'Isi materi geometri',
            'mata_pelajaran_id' => $mapelMath->id,
            'kelas_id' => $kelas9A->id,
            'guru_id' => $guruMath->id
        ]);
        $materi3 = Materi::create([
            'judul' => 'Statistika Dasar',
            'konten' => 'Isi materi statistika',
            'mata_pelajaran_id' => $mapelMath->id,
            'kelas_id' => $kelas9A->id,
            'guru_id' => $guruMath->id
        ]);

        // Interaksi Dummy (Implicit Feedback) untuk Siswa Lain di Kelas 9A
        // Siswa lain menyukai Aljabar dan Geometri
        foreach ([$siswas[10], $siswas[11], $siswas[12]] as $s) {
            LogAktivitas::create(['user_id' => $s->id, 'jenis_aktivitas' => 'baca_materi', 'item_id' => $materi1->id, 'durasi' => 300]);
            LogAktivitas::create(['user_id' => $s->id, 'jenis_aktivitas' => 'baca_materi', 'item_id' => $materi2->id, 'durasi' => 250]);
        }

        // Interaksi Dummy untuk agus_test
        // Agus test hanya baca materi 1 (Aljabar)
        LogAktivitas::create([
            'user_id' => $agus->id,
            'jenis_aktivitas' => 'baca_materi',
            'item_id' => $materi1->id,
            'durasi' => 320
        ]);

        // Karena Agus punya similarity dengan siswa lain (sama-sama baca materi 1), algoritma nanti seharusnya merekomendasikan materi 2 (Geometri).
    }
}
