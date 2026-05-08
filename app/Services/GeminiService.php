<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function generateKuis($materiText, $jumlahSoal = 5)
    {
        $prompt = "Buatkan kuis pilihan ganda berjumlah {$jumlahSoal} soal berdasarkan materi berikut:\n\n{$materiText}\n\n"
                . "Keluarkan output HANYA dalam format JSON dengan struktur array objek persis seperti ini, tanpa markdown block (```json): "
                . "[ { \"pertanyaan\": \"...\", \"opsi_a\": \"...\", \"opsi_b\": \"...\", \"opsi_c\": \"...\", \"opsi_d\": \"...\", \"jawaban_benar\": \"A/B/C/D\" } ]";

        return $this->callGemini($prompt);
    }

    public function generateRancanganMateri($topik, $kelas)
    {
        $prompt = "Buatkan rancangan materi pembelajaran terstruktur untuk anak SMP kelas {$kelas} dengan topik '{$topik}'.\n"
                . "Keluarkan output HANYA dalam format JSON dengan struktur persis seperti ini, tanpa markdown block: "
                . "{ \"judul\": \"...\", \"konten_html\": \"...\" }";

        return $this->callGemini($prompt);
    }

    protected function callGemini($prompt)
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$this->apiKey}";
            
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $text = $result['candidates'][0]['content']['parts'][0]['text'];
                    // Bersihkan backtick jika gemini masih membandel memberikan markdown code block
                    $text = str_replace(['```json', '```'], '', $text);
                    return json_decode(trim($text), true);
                }
            }
            
            Log::error('Gemini API Error', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return null;
        }
    }
}
