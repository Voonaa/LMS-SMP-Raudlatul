# BAB IV: IMPLEMENTASI DAN PENGUJIAN SISTEM (DATA WEB) - UPDATED
**Proyek:** Learning Management System (LMS) SMP Islam Raudlatul Hikmah
**Deskripsi:** Dokumen ini memuat seluruh data teknis, arsitektur, struktur database, hak akses, dan fungsionalitas sistem yang telah diimplementasikan dalam aplikasi LMS. Data ini disusun khusus untuk mempermudah penulisan **Bab 4 Skripsi**.

---

## 1. Arsitektur dan Teknologi Sistem (Tech Stack)
Sistem ini dibangun menggunakan arsitektur *Monolithic* yang ringan dan performa tinggi dengan teknologi berikut:
- **Bahasa Pemrograman:** PHP 8.3.16
- **Framework Backend:** Laravel 12.58.0 (Versi terbaru)
- **Framework Frontend/Styling:** Tailwind CSS v4 (Sistem Desain Modern & Islami: Emerald & Gold)
- **Interaktivitas Frontend:** Alpine.js v3.15 (Digunakan untuk Modal, Dropdown, Perpindahan Tab Interaktif, dan aksi asinkron)
- **Database:** MySQL 8.0
- **Kecerdasan Buatan (AI):** API Google Gemini-1.5-Flash (Untuk fitur *Auto-Generate* Materi, Soal Kuis, dan Analisis Diagnostik)

---

## 2. Hak Akses & Aktor Sistem (Role-Based Access Control)
Sistem menerapkan konsep **Context-Aware Security**, di mana data siswa terisolasi secara ketat berdasarkan tingkat dan kelasnya.

1. **Admin (Administrator):**
   - Mengelola master data (Konfigurasi sistem, Import User/Siswa secara massal via CSV).
   - Memantau dasbor analitik global penggunaan sistem.

2. **Guru:**
   - Mengelola modul ajar (Materi, Tugas, Kuis).
   - Menggunakan asisten AI Gemini untuk pembuatan konten edukatif secara otomatis.
   - Melakukan penilaian tugas dan memantau progres belajar siswa.

3. **Siswa:**
   - Mengakses materi dan tugas khusus sesuai kelasnya (`kelas_id`).
   - Mengerjakan **Kuis Diagnostik** untuk menentukan fokus pembelajaran awal.
   - Mengakses **Pusat Pencapaian** untuk memantau lencana (badges), poin, dan peringkat kelas.

---

## 3. Struktur Database (Entity Relationship)
Berikut adalah daftar tabel utama beserta atribut kunci yang telah diperbarui:

### Tabel Master & Otentikasi
*   `users`: Menyimpan data profil, peran (*role*), dan status progres (*diagnostic_done*).
*   `kelas` & `mata_pelajaran`: Menyediakan referensi tingkatan kelas dan kategori kurikulum.

### Tabel Evaluasi & Materi
*   `materi`: Menyimpan konten pembelajaran digital.
*   `kuis`: Header untuk kuis (termasuk *is_diagnostik* untuk kuis awal).
*   `soal_kuis`: Bank soal pilihan ganda. 
    *(Pembaruan: Sekarang memiliki `mata_pelajaran_id` untuk mendukung penilaian diagnostik per-mapel)*.
*   `tugas` & `pengumpulan_tugas`: Mengelola alur penugasan dan pengumpulan file.

### Tabel Gamifikasi & Interaksi
*   `log_aktivitas`: Rekaman jejak digital (baca materi, login, forum) yang dikonversi menjadi poin dan *streak*.
*   `forum_threads` & `forum_replies`: Media diskusi asinkron antar siswa dan guru.
*   `likes`: Sistem *upvote* untuk meningkatkan keterlibatan pengguna di forum.

---

## 4. Fungsionalitas Unggulan (Fitur Terbaru)

### A. Kuis Diagnostik & Rekomendasi AI
Siswa yang baru pertama kali login wajib mengerjakan kuis diagnostik yang mencakup 5 mata pelajaran inti.
- **Output:** Sistem secara otomatis menghitung skor per-mapel.
- **AI Recommendation:** Memberikan saran fokus materi berdasarkan mata pelajaran dengan nilai terendah.

### B. Pusat Pencapaian (Achievement Center)
Halaman visual yang merangkum seluruh elemen gamifikasi dalam satu dasbor interaktif (Tab System Alpine.js):
- **Hero Poin:** Menampilkan total poin dan *Daily Streak* dengan desain premium.
- **Koleksi Lencana (Badges):** Menampilkan lencana yang berhasil dibuka (contoh: "Penjelajah Pemula", "Sang Disiplin"). Lencana yang belum didapat tampil *grayscale* dengan info syarat pencapaian.
- **Leaderboard Real-time:** Menampilkan 10 besar siswa dengan poin tertinggi di kelas tersebut.

### C. Kustomisasi UX
- **Pesan Kesalahan Kustom:** Pesan login standar Laravel diubah menjadi bahasa Indonesia yang lebih santun: *"Login tidak berhasil. Silakan periksa kembali username dan password Anda."*

---

## 5. Strategi Pengujian (Untuk Bab 4)

### A. Pengujian API dengan Postman
Telah disiapkan *Postman Collection* (`LMS_Raudlatul_Postman_Collection.json`) untuk menguji integritas endpoint berikut:
1.  **Otentikasi:** Validasi login dan perlindungan CSRF.
2.  **Diagnostik:** Pengujian pengiriman data jawaban JSON.
3.  **Integrasi AI:** Validasi respon dari Gemini API untuk pembuatan materi otomatis.
4.  **Keamanan Peringkat:** Memastikan data kelas lain tidak bocor ke siswa kelas berbeda (Error 403).

### B. Pengujian Data Forum
Telah disimulasikan data percakapan pada forum diskusi (Math, IPA, English) untuk membuktikan fitur interaksi sosial berjalan dengan baik di tingkat basis data dan antarmuka.

---

## 6. Rangkuman Nilai Tambah (Skripsi)
1.  **Automated Content Creation:** Efisiensi guru meningkat dengan bantuan Generative AI.
2.  **Data Isolation (Privacy):** Penerapan keamanan data siswa yang ketat antar kelas (*Context-Aware*).
3.  **Retention Strategy:** Penggunaan *Behavioral Gamification* (Badges & Streaks) untuk meningkatkan *User Engagement*.

---
*(Gunakan data dalam dokumen ini sebagai basis narasi teknis pada laporan Bab 4 Anda. Seluruh file pendukung seperti `pengujian_postman.md` dan `dataWeb.md` tersedia di direktori proyek.)*
