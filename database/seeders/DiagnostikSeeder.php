<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kuis;
use App\Models\SoalKuis;
use App\Models\User;

class DiagnostikSeeder extends Seeder
{
    /**
     * Buat 1 Kuis Diagnostik dengan 15 soal (3 per mata pelajaran).
     * Kuis ini bersifat lintas kelas (kelas_id = null, is_diagnostik = true).
     */
    public function run(): void
    {
        // Hapus kuis diagnostik lama jika ada
        Kuis::where('is_diagnostik', true)->delete();

        // Ambil guru pertama sebagai pemilik kuis diagnostik
        $guru = User::where('role', 'guru')->first();
        if (!$guru) {
            $this->command->warn('Tidak ada guru. Skip DiagnostikSeeder.');
            return;
        }

        // ── Buat kuis ──
        $kuis = Kuis::create([
            'judul'           => 'Kuis Diagnostik Kemampuan Awal',
            'deskripsi'       => 'Kuis ini digunakan untuk mengukur kemampuan awal siswa di semua mata pelajaran inti. Hasil kuis akan digunakan untuk merekomendasikan materi yang paling sesuai untukmu.',
            'materi_id'       => null,
            'mata_pelajaran_id' => 1, // Matematika (utama, isi bisa beragam)
            'kelas_id'        => null,  // Lintas kelas
            'guru_id'         => $guru->id,
            'is_diagnostik'   => true,
        ]);

        // ── Soal: 3 per mapel × 5 mapel = 15 soal ──
        $soalData = [

            // === 1. MATEMATIKA (mata_pelajaran_id = 1) ===
            [
                'pertanyaan'   => 'Hasil dari 3² + 4² adalah ...',
                'opsi_a'       => '20', 'opsi_b' => '25', 'opsi_c' => '30', 'opsi_d' => '49',
                'jawaban_benar' => 'B', 'bobot' => 20,
                'mata_pelajaran_id' => 1,
            ],
            [
                'pertanyaan'   => 'Jika x + 5 = 12, maka nilai x adalah ...',
                'opsi_a'       => '5', 'opsi_b' => '6', 'opsi_c' => '7', 'opsi_d' => '8',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 1,
            ],
            [
                'pertanyaan'   => 'Luas segitiga dengan alas 8 cm dan tinggi 6 cm adalah ...',
                'opsi_a'       => '24 cm²', 'opsi_b' => '48 cm²', 'opsi_c' => '14 cm²', 'opsi_d' => '12 cm²',
                'jawaban_benar' => 'A', 'bobot' => 20,
                'mata_pelajaran_id' => 1,
            ],

            // === 2. IPA (mata_pelajaran_id = 2) ===
            [
                'pertanyaan'   => 'Organel sel yang berfungsi sebagai pusat pengendali sel adalah ...',
                'opsi_a'       => 'Ribosom', 'opsi_b' => 'Mitokondria', 'opsi_c' => 'Nukleus', 'opsi_d' => 'Vakuola',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 2,
            ],
            [
                'pertanyaan'   => 'Perubahan es menjadi air disebut ...',
                'opsi_a'       => 'Membeku', 'opsi_b' => 'Menyublim', 'opsi_c' => 'Menguap', 'opsi_d' => 'Mencair',
                'jawaban_benar' => 'D', 'bobot' => 20,
                'mata_pelajaran_id' => 2,
            ],
            [
                'pertanyaan'   => 'Benda yang dapat ditarik oleh magnet disebut ...',
                'opsi_a'       => 'Isolator', 'opsi_b' => 'Konduktor', 'opsi_c' => 'Feromagnetik', 'opsi_d' => 'Diamagnetik',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 2,
            ],

            // === 3. IPS (mata_pelajaran_id = 3) ===
            [
                'pertanyaan'   => 'Proklamasi Kemerdekaan Indonesia dibacakan pada tanggal ...',
                'opsi_a'       => '17 Agustus 1944', 'opsi_b' => '17 Agustus 1945', 'opsi_c' => '18 Agustus 1945', 'opsi_d' => '1 Juni 1945',
                'jawaban_benar' => 'B', 'bobot' => 20,
                'mata_pelajaran_id' => 3,
            ],
            [
                'pertanyaan'   => 'Mata uang yang digunakan di Jepang adalah ...',
                'opsi_a'       => 'Yuan', 'opsi_b' => 'Won', 'opsi_c' => 'Yen', 'opsi_d' => 'Ringgit',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 3,
            ],
            [
                'pertanyaan'   => 'Kegiatan ekonomi yang menghasilkan barang disebut ...',
                'opsi_a'       => 'Distribusi', 'opsi_b' => 'Konsumsi', 'opsi_c' => 'Produksi', 'opsi_d' => 'Ekspor',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 3,
            ],

            // === 4. BAHASA INDONESIA (mata_pelajaran_id = 4) ===
            [
                'pertanyaan'   => 'Kalimat yang menyatakan perintah disebut kalimat ...',
                'opsi_a'       => 'Berita', 'opsi_b' => 'Tanya', 'opsi_c' => 'Perintah', 'opsi_d' => 'Seru',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 4,
            ],
            [
                'pertanyaan'   => 'Sinonim kata "gembira" adalah ...',
                'opsi_a'       => 'Sedih', 'opsi_b' => 'Senang', 'opsi_c' => 'Marah', 'opsi_d' => 'Takut',
                'jawaban_benar' => 'B', 'bobot' => 20,
                'mata_pelajaran_id' => 4,
            ],
            [
                'pertanyaan'   => 'Karangan yang menceritakan kehidupan nyata seseorang disebut ...',
                'opsi_a'       => 'Novel', 'opsi_b' => 'Puisi', 'opsi_c' => 'Biografi', 'opsi_d' => 'Cerpen',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 4,
            ],

            // === 5. BAHASA INGGRIS (mata_pelajaran_id = 5) ===
            [
                'pertanyaan'   => 'What is the plural form of "child"?',
                'opsi_a'       => 'Childs', 'opsi_b' => 'Childes', 'opsi_c' => 'Children', 'opsi_d' => 'Childrens',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 5,
            ],
            [
                'pertanyaan'   => 'Choose the correct verb: "She ___ to school every day."',
                'opsi_a'       => 'go', 'opsi_b' => 'goes', 'opsi_c' => 'going', 'opsi_d' => 'gone',
                'jawaban_benar' => 'B', 'bobot' => 20,
                'mata_pelajaran_id' => 5,
            ],
            [
                'pertanyaan'   => 'What does "beautiful" mean in Indonesian?',
                'opsi_a'       => 'Pintar', 'opsi_b' => 'Kuat', 'opsi_c' => 'Cantik/Indah', 'opsi_d' => 'Baik',
                'jawaban_benar' => 'C', 'bobot' => 20,
                'mata_pelajaran_id' => 5,
            ],
        ];

        foreach ($soalData as $soal) {
            SoalKuis::create([
                'kuis_id'       => $kuis->id,
                'pertanyaan'    => $soal['pertanyaan'],
                'opsi_a'        => $soal['opsi_a'],
                'opsi_b'        => $soal['opsi_b'],
                'opsi_c'        => $soal['opsi_c'],
                'opsi_d'        => $soal['opsi_d'],
                'jawaban_benar' => $soal['jawaban_benar'],
                'bobot'         => $soal['bobot'],
                // Simpan mata_pelajaran_id di soal supaya bisa dihitung per-mapel
                // Kita akan pakai kolom tambahan di soal jika ada, atau simpan di metadata
            ]);
        }

        $this->command->info("✅ Kuis Diagnostik dibuat: ID {$kuis->id}, 15 soal.");
    }
}
