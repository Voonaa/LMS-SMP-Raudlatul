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
                . "Format JSON array of objects: [ { \"pertanyaan\": \"...\", \"opsi_a\": \"...\", \"opsi_b\": \"...\", \"opsi_c\": \"...\", \"opsi_d\": \"...\", \"jawaban_benar\": \"A/B/C/D\" } ]";

        return $this->callGemini($prompt);
    }

    public function generateRancanganMateri($topik, $kelas)
    {
        $prompt = "Buatkan rancangan materi pembelajaran terstruktur untuk anak SMP kelas {$kelas} dengan topik '{$topik}'.\n"
                . "Format JSON object: { \"judul\": \"...\", \"konten_html\": \"... (isi materi lengkap dengan tag HTML p, b, ul, li)\" }";

        return $this->callGemini($prompt);
    }

    protected function callGemini($prompt)
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";
            
            $response = Http::withoutVerifying()->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $text = $result['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Cleanup potential markdown artifacts even with JSON mode
                    $text = trim($text);
                    if (strpos($text, '```json') === 0) {
                        $text = substr($text, 7);
                    }
                    if (substr($text, -3) === '```') {
                        $text = substr($text, 0, -3);
                    }
                    $text = trim($text);

                    $decoded = json_decode($text, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }

                    Log::error('Gemini JSON Decode Error: ' . json_last_error_msg(), ['text' => $text]);
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
