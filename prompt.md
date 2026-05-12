# Dokumentasi & Alur Kerja: LMS SMP Islam Raudlatul Hikmah (Skripsi)

**Dokumen ini adalah "Source of Truth" untuk agen AI.** 
Tugas AI adalah membaca dokumen ini sebelum mengoding untuk memahami konteks, batasan, detail fungsionalitas setiap role, dan algoritma sistem. Setiap kali satu tahap selesai, AI WAJIB mengubah tanda `[ ]` menjadi `[x]` dan melakukan *push* ke repositori GitHub.

## 📌 Informasi Proyek
*   **Nama Sistem:** LMS SMP Islam Raudlatul Hikmah
*   **Target Pengguna:** 150 pengguna (138 Siswa, 10 Guru, 2 Admin) [1].
*   **Tech Stack Utama:** Laravel 12 (PHP 8.3), MySQL, Tailwind CSS v4, Alpine.js v3.15 [2, 3]. 
*   **LARANGAN KERAS:** Jangan gunakan React, Vue, atau Livewire [4]. Pertahankan arsitektur Monolitik yang seringan mungkin. DILARANG KERAS membuat fitur *Direct Message* (DM) atau Chat Privat [5].
*   **Repositori Git:** `https://github.com/Voonaa/LMS-SMP-Raudlatul.git`

## ⚙️ Aturan Inti Sistem (Core Rules)
1.  **Context-Aware Data (Sangat Krusial):** Semua tabel operasional (materi, soal_kuis, forum_threads, user) **WAJIB** terisolasi berdasarkan `kelas_id` [6]. Siswa (misal Kelas 7A) mutlak tidak boleh melihat data dari kelas 8 atau 9 [7].
2.  **Kredensial Login:** Tidak ada *register* publik. Admin login dengan username `admin`, Guru dengan NIP (18 digit tanpa spasi), Siswa dengan NISN (10 digit tanpa spasi) [8, 9]. Sistem wajib menggunakan fungsi `trim()` saat login dan password di-hash dengan `Bcrypt` [10, 11].
3.  **Gamifikasi Adil:** Menggunakan sistem *Relative Leaderboard* (Papan Peringkat Kelas), di mana siswa diurutkan murni HANYA berdasarkan teman sekelasnya [12, 13]. Terdapat 8 Badges. Mapel Matematika mendapat bobot khusus dengan *multiplier* poin 1.5x [14, 15].

---

## 👥 Detail Fungsionalitas & CRUD per Role (Hak Akses)

### 1. Fungsionalitas ADMIN (Master Data)
Admin adalah pemegang kendali struktur sistem dan tidak mengikuti aturan *Context-Aware* (bisa melihat semua data).
*   **Dashboard:** Menampilkan statistik global sistem dan pengujian AI.
*   **CRUD Pengguna:** Membuat akun Siswa (dengan NISN) dan Guru (dengan NIP) secara manual atau via fitur **Bulk Import Excel** [16]. Password default harus dibuat dan dienkripsi.
*   **CRUD Kelas & Relasi (Ploting):** Admin membuat data kelas (misal: 7A, 8B). Admin WAJIB memplot Siswa ke dalam `kelas_id` tertentu, dan menugaskan Guru ke `kelas_id` dan mata pelajaran tertentu [17].
*   **Konfigurasi Sistem:** Mengatur tahun ajaran dan parameter bobot poin mapel.
*   **Endpoint Pengujian Skripsi Bab 4:** Akses ke halaman rahasia `/admin/testing/mae` yang menampilkan UI Card untuk nilai **MAE (target ≤ 0.8), Precision@10 (target 0.75), dan Recall@10** dari pengujian algoritma menggunakan data sintetis [18].

### 2. Fungsionalitas GURU (Content Creator)
Guru HANYA bisa mengelola data sesuai dengan kelas dan mata pelajaran yang telah diplot oleh Admin untuknya [17].
*   **Dashboard:** Ringkasan statistik siswa di kelasnya dan peringatan dini siswa berisiko [19].
*   **CRUD Materi:** Mengelola teks materi HTML. Memiliki fitur **[✨ Generate via AI Gemini]** untuk merumuskan draf materi secara otomatis [20, 21].
*   **CRUD Kuis:** Mengelola soal pilihan ganda. Memiliki 3 opsi pembuatan soal: Manual, **[📄 Bulk Import Excel]**, dan **[✨ Generate via AI Gemini]** (menggunakan *Structured JSON* dari materi yang ada) [22-24].
*   **CRUD Tugas (Fitur Pelengkap):** Memberikan instruksi tugas dan menilai dokumen unggahan siswa.
*   **Forum & Laporan:** Mengawasi forum asinkron dan mencetak laporan progres siswa PDF (*dompdf*).

### 3. Fungsionalitas SISWA (End-User)
Siswa HANYA bisa mengakses data (materi, kuis, leaderboard, forum) yang memiliki `kelas_id` yang sama dengan miliknya [17].
*   **Dashboard Gamifikasi & AI:** 
    *   Widget "✨ Rekomendasi Materi Untukmu" (Top-10) hasil kalkulasi algoritma [25].
    *   Widget "Peringkat Kelas Anda" (Relative Leaderboard) [12].
    *   Widget "Daily Streak 🔥" (bertambah jika siswa membaca materi/kuis hari itu).
*   **Pembelajaran:** Membaca materi, mengerjakan "Kuis Diagnostik" (untuk pengguna baru agar mencegah *cold-start*) [25], dan mengerjakan kuis pembelajaran.
*   **Tugas (Fitur Pelengkap):** Mengunggah file PDF/Gambar sebagai jawaban tugas [26].
*   **Forum Diskusi (Data Implisit AI):** Membaca thread, menggunakan tombol **Upvote/Like** dan **Reply** secara asinkron [27].

---

## 🧠 "Mesin Tak Terlihat" di Backend (Core Logic)
1.  **Interaction Data Logger:** Tabel `log_aktivitas` mencatat setiap aksi siswa secara latar belakang (*Implicit Feedback*). Bobot: baca materi (+1), like forum (+2), balas forum (+3), nilai kuis > 80 (+5) [28].
2.  **Item-Based Collaborative Filtering:** *Service class* sistem TIDAK MENGGUNAKAN SVD/Hybrid, melainkan fokus pada **Item-Based CF dengan Cosine Similarity** [29]. Algoritma wajib membaca dataset *Implicit Feedback* dari tabel log, dikalkulasi **HANYA** untuk interaksi di dalam `kelas_id` yang sama [30].

---

## 🚀 Alur Kerja (Vibe Coding Pipeline)

*Instruksi untuk AI: Beri tanda `[x]` jika tahap selesai dan push ke GitHub secara berkala.*

### Tahap 1: Frontend Blueprint (UI Statis)
- [ ] Rancang UI statis dengan Tailwind CSS v4 & Alpine.js di dalam file HTML terpisah berdasarkan *Stitch*.
- [ ] Buat layout *mobile-friendly* untuk Login, Dashboard Siswa, Dashboard Guru, dan Forum Diskusi.

### Tahap 2: Backend Brain (Inisialisasi & Core)
- [ ] Inisialisasi Laravel 12 & MySQL.
- [ ] Buat *Migration* berarsitektur Context-Aware (wajib ada `kelas_id`).
- [ ] Buat *Seeder* untuk 5 Mapel, 1 Admin, 5 Guru, 30 Siswa, dan Skenario Pengujian Sintetis (`agus_test`).
- [ ] Buat `CollaborativeFilteringService` (Cosine Similarity) & `GeminiService` (JSON Mode).

### Tahap 3: The Stitching (Integrasi UI & Backend)
- [ ] Konversi HTML Tahap 1 menjadi *Blade Templates*.
- [ ] Hubungkan UI dengan Controller (Tampilkan Rekomendasi AI, Leaderboard Relatif, Integrasi Tombol Generate AI Guru, dan interaksi Like/Reply Forum).
- [ ] Bangun halaman Evaluasi `/admin/testing/mae` menampilkan Card MAE, Precision, dan Recall.

### Tahap 4: Pelengkapan CRUD & Fitur Pelengkap (Standar LMS)
- [ ] Lengkapi antarmuka operasional: Halaman Baca Materi, Form Mengerjakan Kuis, dan Halaman Kelola Akun Admin.
- [ ] Buat fitur pelengkap: Pengumpulan Tugas (*Upload* File) dan Logika Absensi *Daily Streak* 🔥.

### Tahap 5: Pemolesan Akhir & Bug Fixing
- [ ] Pastikan sistem bebas dari *dead code* fitur DM/Chat.
- [ ] Validasi *avatar profile* dan perbaiki navigasi yang belum aktif.