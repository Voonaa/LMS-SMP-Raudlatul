# Dokumentasi & Alur Kerja: LMS SMP Islam Raudlatul Hikmah (Skripsi)

**Dokumen ini adalah "Source of Truth" untuk agen AI.** 
Tugas AI adalah membaca dokumen ini sebelum mengoding untuk memahami konteks, batasan, dan fitur sistem. Setiap kali satu tahap selesai, AI WAJIB mengubah tanda `[ ]` menjadi `[x]` dan melakukan *push* ke repositori GitHub.

## 📌 Informasi Proyek
*   **Nama Sistem:** LMS SMP Islam Raudlatul Hikmah
*   **Target Pengguna:** 150 pengguna (138 Siswa, 10 Guru, 2 Admin)
*   **Tech Stack:** Laravel 12, MySQL, Tailwind CSS v4, Alpine.js v3.15. 
*   **LARANGAN KERAS:** Jangan gunakan React, Vue, atau Livewire. Pertahankan arsitektur seringan mungkin.
*   **Repositori Git:** `https://github.com/Voonaa/LMS-SMP-Raudlatul.git`

## ⚙️ Aturan Inti Sistem (Core Rules)
1.  **Context-Aware Data:** Semua tabel (materi, soal_kuis, forum, user) **WAJIB** terisolasi berdasarkan `kelas_id`. Siswa kelas 7 mutlak tidak boleh melihat data kelas 8/9.
2.  **Kredensial Login:** Tidak ada *register* publik. Admin login dengan "admin", Guru dengan NIP (18 digit), Siswa dengan NISN (10 digit). Dilarang ada spasi di *username* (gunakan `trim()`). Password wajib di-hash menggunakan `Bcrypt`.
3.  **Gamifikasi Adil:** *Leaderboard* diurutkan murni hanya berdasarkan teman sekelas (*Relative Leaderboard*). Poin didapat dari kuis, baca materi, dan aktivitas forum. Mapel Matematika mendapat bobot poin 1.5x.
4.  **Kecerdasan Buatan (AI):** 
    *   **Fitur Guru:** Menggunakan Gemini API (mode *Structured JSON*) untuk membuat kuis otomatis dan rancangan materi.
    *   **Fitur Siswa:** Algoritma rekomendasi materi menggunakan **Item-Based Collaborative Filtering** (Cosine Similarity) yang dipicu oleh *Implicit Feedback* (durasi baca, hasil kuis, like/reply forum). Dilarang menggunakan SVD karena terlalu berat untuk arsitektur ini.

---

## 🚀 Alur Kerja (Vibe Coding Pipeline)

*Instruksi untuk AI: Jika Anda telah menyelesaikan sebuah tahap, beri tanda `[x]` pada tahap tersebut dan jalankan perintah `git add .`, `git commit -m "Menyelesaikan Tahap [Nama Tahap]"`, lalu `git push origin main`.*

### Tahap 0: Persiapan Repositori
- [x] Inisialisasi Git di dalam folder lokal.
- [x] Tautkan remote repository: `git remote add origin https://github.com/Voonaa/LMS-SMP-Raudlatul.git`
- [x] *Commit* pertama dan *push* ke `main`.

### Tahap 1: Frontend Blueprint (Visual Statis)
- [x] Buat kerangka *layout* menggunakan HTML murni, Tailwind CSS v4, dan Alpine.js.
- [x] Desain Halaman Login (Card, aksen hijau zamrud/emas).
- [x] Desain Dashboard Siswa (Widget AI 'Rekomendasi Materi', Widget 'Peringkat Kelas', Grid Mapel).
- [x] Desain Dashboard Guru (Tabel kelola materi, tombol 'Generate via AI Gemini').
- [x] Desain Forum Diskusi (Gaya *thread* asinkron dengan tombol *Like* dan *Reply*). 
- [x] *Git Commit & Push*.

### Tahap 2: Backend Brain (Inisialisasi Laravel & Database)
- [x] Instalasi proyek Laravel 12 murni dan konfigurasi `.env` (MySQL lokal).
- [x] Buat Migration untuk: `kelas`, `users`, `mata_pelajaran`, `materi`, `kuis`, `soal_kuis`, `forum_threads`, `log_aktivitas`, dll. Pastikan relasi *Context-Aware* (`kelas_id`) ketat.
- [x] Buat Sistem Login kustom (trim username, tanpa fitur register publik).
- [x] Buat DatabaseSeeder dengan 5 Mapel Utama, 1 Admin, 5 Guru, 15 Siswa, dan user khusus `agus_test` beserta *dummy interaksi* untuk kebutuhan pengujian algoritma.
- [x] *Git Commit & Push*.

### Tahap 3: Mesin AI & Gamifikasi (Core Logic)
- [ ] Implementasikan `GeminiService.php` dengan output *Structured JSON* untuk fitur Guru.
- [ ] Implementasikan `CollaborativeFilteringService.php` (Item-Based CF dengan *Cosine Similarity*) membaca data *Implicit Feedback*.
- [ ] Implementasikan `LeaderboardController` untuk menampilkan peringkat relatif berdasarkan `kelas_id`.
- [ ] *Git Commit & Push*.

### Tahap 4: The Stitching (Integrasi UI dan Backend)
- [ ] Ubah file HTML statis dari Tahap 1 menjadi struktur `Blade` Laravel.
- [ ] Hubungkan form kelola kuis/materi Guru dengan Controller AI Gemini (buat *loading state* via Alpine.js).
- [ ] Hubungkan widget Dashboard Siswa dengan hasil rekomendasi algoritma CF dan poin Gamifikasi.
- [ ] Hubungkan Forum Diskusi agar setiap klik *Like/Reply* memicu pencatatan ke tabel `log_aktivitas`.
- [ ] *Git Commit & Push*.

### Tahap 5: Senjata Rahasia Bab 4 (Endpoint Pengujian)
- [ ] Buat Controller rahasia di `/admin/testing/mae`.
- [ ] Buat UI halaman yang menampilkan 3 metrik utama: **MAE**, **Precision@10**, dan **Recall@10** dari hasil rekomendasi siswa `agus_test` dibandingkan dengan *dummy* faktanya.
- [ ] *Git Commit & Push*.
