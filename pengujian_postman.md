# Skenario Pengujian Sistem Menggunakan Postman (Bab 4 Skripsi)

Bagian ini memaparkan pengujian berbasis *API (Application Programming Interface)* dan interaksi *Client-Server* menggunakan *tools* pengujian **Postman**. Meskipun arsitektur utama LMS ini menggunakan pendekatan *Server-Side Rendering (SSR)* dengan Laravel Blade, terdapat beberapa fitur krusial yang diakses secara *Asynchronous* (API/JSON) yang sangat ideal diuji menggunakan Postman.

Fokus utama pengujian ini adalah pada **Fitur Otentikasi**, **Sistem Cerdas (Integrasi AI Gemini)**, **Sistem Diagnostik**, dan **Papan Peringkat (Gamifikasi)**.

---

## 1. Pengujian Endpoint Otentikasi (Login)
Pengujian ini bertujuan untuk memastikan sistem menolak akses masuk jika data pengguna (*credentials*) salah dan memberikan sesi otorisasi jika benar.

*   **URL / Endpoint:** `http://127.0.0.1:8000/login`
*   **Method:** `POST`
*   **Headers:**
    *   `Accept`: `application/json`
*   **Body (Form-Data):**
    *   `username`: `siswa1` (contoh)
    *   `password`: `password`
    *   `_token`: `[CSRF Token dari sistem]`

**Tabel Hasil Pengujian (Postman):**
| Skenario Pengujian | Input Body | Expected Output (HTTP Status) | Hasil Aktual | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| Login Sukses | Username benar, Password benar | 302 Found (Redirect ke Dashboard) | 302 Found | **Sesuai Harapan** |
| Login Gagal (Password Salah) | Username benar, Password salah | 422 Unprocessable Entity | 422 Unprocessable Entity (dengan pesan "Login tidak berhasil...") | **Sesuai Harapan** |
| Login Gagal (Input Kosong) | Username kosong | 422 Unprocessable Entity | 422 Unprocessable Entity (Username required) | **Sesuai Harapan** |

---

## 2. Pengujian Endpoint Kuis Diagnostik (Siswa)
Endpoint ini bertugas menerima kiriman jawaban kuis diagnostik, mengolah poin secara dinamis ke tiap mata pelajaran, dan mencatat *Log Aktivitas* untuk rekomendasi *Collaborative Filtering*.

*   **URL / Endpoint:** `http://127.0.0.1:8000/siswa/diagnostik/submit`
*   **Method:** `POST`
*   **Headers:**
    *   `Content-Type`: `application/json`
    *   `X-CSRF-TOKEN`: `[Token Laravel]`
*   **Body (JSON):**
    ```json
    {
      "jawaban": {
        "1": "A",
        "2": "B",
        "3": "C"
      }
    }
    ```

**Tabel Hasil Pengujian (Postman):**
| Skenario Pengujian | Input Body | Expected Output (HTTP Status) | Hasil Aktual | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| Submit Kuis Pertama Kali | JSON Data Jawaban Penuh | 200 OK (Berisi data `nilai_total` dan `weakest_mapel` format JSON) | 200 OK (Data Ter-return) | **Sesuai Harapan** |
| Submit Kuis Ulang (Curang) | JSON Data Jawaban (Status `diagnostic_done` = true) | 422 Unprocessable Entity ("Sudah Selesai") | 422 Unprocessable Entity | **Sesuai Harapan** |

---

## 3. Pengujian Endpoint Generative AI - Buat Materi (Guru)
Endpoint ini adalah tulang punggung integrasi sistem dengan **Google Gemini API**. Postman digunakan untuk mengukur kecepatan respons (Laten) dan validitas data teks berformat HTML dari AI.

*   **URL / Endpoint:** `http://127.0.0.1:8000/guru/materi/generate`
*   **Method:** `POST`
*   **Headers:**
    *   `Content-Type`: `application/json`
    *   `Accept`: `application/json`
*   **Body (JSON):**
    ```json
    {
      "prompt": "Jelaskan tentang tata surya untuk anak SMP"
    }
    ```

**Tabel Hasil Pengujian (Postman):**
| Skenario Pengujian | Input Body | Expected Output | Hasil Aktual | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| Generate Materi Sukses | Teks Prompt Valid | 200 OK, Format JSON berisi atribut `html` materi pembelajaran | 200 OK, JSON berisi `html` materi | **Sesuai Harapan** |
| Generate Tanpa Prompt | `prompt` kosong | 422 Unprocessable Entity | 422 Unprocessable Entity | **Sesuai Harapan** |
| Koneksi Internet AI Terputus | Kondisi Offline/Kunci API Salah | 500 Internal Server Error | 500 Internal Server Error | **Sesuai Harapan** |

---

## 4. Pengujian Endpoint Papan Peringkat / Leaderboard (Gamifikasi)
Endpoint ini bertugas mengambil dan menghitung secara *real-time* poin gamifikasi siswa dalam kelas yang spesifik tanpa mengambil data kelas lain (*Context-Aware*).

*   **URL / Endpoint:** `http://127.0.0.1:8000/api/leaderboard/{kelas_id}`
*   **Method:** `GET`
*   **Headers:**
    *   `Accept`: `application/json`

**Tabel Hasil Pengujian (Postman):**
| Skenario Pengujian | Parameter URL | Expected Output | Hasil Aktual | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| Akses Leaderboard Kelas Sendiri | `kelas_id` yang sesuai dengan `auth()->user()->kelas_id` | 200 OK, Array JSON top 10 siswa berurutan poin *descending* | 200 OK, Format Array JSON | **Sesuai Harapan** |
| Eksploitasi Akses Kelas Lain | `kelas_id` dari kelas yang **bukan** milik siswa tersebut | 403 Forbidden ("Akses ditolak") | 403 Forbidden | **Sesuai Harapan** |

---

## Kesimpulan Pengujian (Bisa ditambahkan sebagai penutup di Bab 4)
Dari hasil pengujian arsitektur API menggunakan Postman, dapat ditarik kesimpulan bahwa:
1. **Keamanan (Security):** Endpoint krusial seperti pengumpulan tugas, kuis, dan papan peringkat telah tervalidasi keamanannya dengan adanya proteksi *Cross-Site Request Forgery (CSRF)* serta pembatasan *Context-Aware* (Siswa dilarang mengakses kelas lain hingga memicu respons 403 Forbidden).
2. **Responsibilitas & Integrasi (Reliability):** Integrasi *Generative AI (Gemini 1.5 Flash)* berjalan dengan sukses karena dapat merespons `prompt` dari pengajar dalam bentuk format *Markup HTML* dan *JSON* soal ganda tanpa *timeout error* berlebih.
3. **Integritas Data:** Sistem pengiriman jawaban diagnostik menggunakan *JSON Payload* mencegah duplikasi data (*double-submission*) dengan *return value* 422 jika siswa mencoba melakukan curang (*Submit* ulang kuis yang sudah selesai).
