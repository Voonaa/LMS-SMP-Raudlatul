<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\User;
use App\Models\MataPelajaran;

class ForumDataSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama jika ingin benar-benar bersih untuk SS
        // ForumReply::truncate();
        // ForumThread::truncate();

        $guru1 = User::where('role', 'guru')->first();
        $guru2 = User::where('role', 'guru')->skip(1)->first();
        $siswa1 = User::where('role', 'siswa')->first();
        $siswa2 = User::where('role', 'siswa')->skip(1)->first();

        // 1. Thread Matematika
        $thread1 = ForumThread::create([
            'user_id' => $guru1->id,
            'mata_pelajaran_id' => 1, // Matematika
            'kelas_id' => 1, // 7A
            'judul' => 'Trik Cepat Menghitung Perkalian Dua Digit',
            'konten' => 'Halo semuanya! Hari ini bapak ingin membagikan tips cepat menghitung perkalian 11 dengan angka dua digit. Cukup sisipkan jumlah kedua digit tersebut di tengah-tengahnya. Contoh: 25 x 11 = 2 (2+5) 5 = 275. Ada yang ingin mencoba dengan angka lain?',
        ]);

        ForumReply::create([
            'thread_id' => $thread1->id,
            'user_id' => $siswa1->id,
            'konten' => 'Wah keren pak! Kalau 45 x 11 berarti 495 ya pak? Ternyata matematika bisa seru juga.',
        ]);

        ForumReply::create([
            'thread_id' => $thread1->id,
            'user_id' => $guru1->id,
            'konten' => 'Tepat sekali! Teruslah berlatih ya.',
        ]);

        // 2. Thread IPA
        $thread2 = ForumThread::create([
            'user_id' => $siswa2->id,
            'mata_pelajaran_id' => 2, // IPA
            'kelas_id' => 1, // 7A
            'judul' => 'Kenapa Langit Berwarna Biru saat Siang Hari?',
            'konten' => 'Teman-teman dan bapak/ibu guru, saya mau tanya. Kenapa ya kalau siang hari langit kita warnanya biru? Tapi kalau sore hari bisa berubah jadi kemerahan atau oranye?',
        ]);

        ForumReply::create([
            'thread_id' => $thread2->id,
            'user_id' => $guru2->id,
            'konten' => 'Pertanyaan bagus! Itu terjadi karena fenomena Rayleigh Scattering. Cahaya matahari terdiri dari berbagai warna, dan atmosfer bumi menghamburkan cahaya biru lebih kuat daripada warna lainnya karena panjang gelombangnya yang pendek.',
        ]);

        // 3. Thread Bahasa Inggris
        $thread3 = ForumThread::create([
            'user_id' => $guru2->id,
            'mata_pelajaran_id' => 5, // Bahasa Inggris
            'kelas_id' => 1, // 7A
            'judul' => 'Daily English Expression: Greetings',
            'konten' => 'Hi everyone! In English, we don\'t just say "How are you?". We can also use "How is it going?" or "What\'s up?". Can you guys share other ways to greet someone in English?',
        ]);

        ForumReply::create([
            'thread_id' => $thread3->id,
            'user_id' => $siswa1->id,
            'konten' => 'I usually use "How have you been?" for friends I haven\'t seen in a long time.',
        ]);

        ForumReply::create([
            'thread_id' => $thread3->id,
            'user_id' => $siswa2->id,
            'konten' => 'Is it okay to use "What\'s crackin\'?" in formal situation, ma\'am?',
        ]);

        ForumReply::create([
            'thread_id' => $thread3->id,
            'user_id' => $guru2->id,
            'konten' => 'Good question! "What\'s crackin\'?" is very informal, so avoid using it in formal situations or with older people.',
        ]);
    }
}
