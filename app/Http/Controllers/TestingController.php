<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\LogAktivitas;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TestingController extends Controller
{
    public function mae()
    {
        // Pastikan hanya admin yang bisa akses
        if (!Gate::allows('admin')) {
            abort(403);
        }

        // --- Simulasi Perhitungan MAE Sederhana untuk Item-Based CF ---
        // Kita butuh (1) Actual Rating/Score (2) Predicted Rating/Score
        // Karena ini testing, kita ambil sample user 'agus_test'
        
        $userTest = User::where('username', 'agus_test')->first();
        if (!$userTest) {
            return "User agus_test tidak ditemukan. Harap jalankan Seeder.";
        }

        // 1. Ambil Interaksi (Actual Score)
        $interactions = LogAktivitas::select('user_id', 'item_id', 'jenis_aktivitas', 'durasi')
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();

        $itemUserMatrix = [];
        $actualScores = [];

        foreach ($interactions as $log) {
            $itemId = $log->item_id;
            $uId = $log->user_id;
            
            $score = 1; 
            if ($log->durasi > 0) {
                $score += min(5, ceil($log->durasi / 60)); 
            }

            if (!isset($itemUserMatrix[$itemId])) {
                $itemUserMatrix[$itemId] = [];
            }
            if (!isset($itemUserMatrix[$itemId][$uId])) {
                $itemUserMatrix[$itemId][$uId] = 0;
            }
            $itemUserMatrix[$itemId][$uId] += $score;

            if ($uId == $userTest->id) {
                $actualScores[$itemId] = $itemUserMatrix[$itemId][$uId];
            }
        }

        // Jika tidak ada interaksi dari agus_test
        if (empty($actualScores)) {
            $mae = 0;
            $n = 0;
            return view('admin.testing.mae', compact('mae', 'n', 'userTest'));
        }

        // 2. Cosine Similarity Item-Item
        $itemSimilarities = [];
        $allItemIds = array_keys($itemUserMatrix);
        
        foreach ($allItemIds as $itemA) {
            foreach ($allItemIds as $itemB) {
                if ($itemA == $itemB) continue;
                if (isset($itemSimilarities[$itemA][$itemB])) continue;

                $similarity = $this->cosineSimilarity($itemUserMatrix[$itemA], $itemUserMatrix[$itemB]);
                $itemSimilarities[$itemA][$itemB] = $similarity;
                $itemSimilarities[$itemB][$itemA] = $similarity; 
            }
        }

        // 3. Prediksi Score (K-Fold atau Leave-One-Out)
        // Kita akan prediksi score item yang SUDAH berinteraksi dengan agus_test (menggunakan score lainnya)
        // untuk menghitung error.
        
        $predictedScores = [];
        
        foreach ($actualScores as $targetItemId => $actualScore) {
            $numerator = 0;
            $denominator = 0;

            foreach ($actualScores as $seenItemId => $seenScore) {
                if ($seenItemId == $targetItemId) continue; // Leave one out

                $sim = $itemSimilarities[$targetItemId][$seenItemId] ?? 0;
                if ($sim > 0) {
                    $numerator += $sim * $seenScore;
                    $denominator += $sim;
                }
            }

            if ($denominator > 0) {
                $predictedScores[$targetItemId] = $numerator / $denominator;
            } else {
                $predictedScores[$targetItemId] = 0; // Default jika tidak ada similarity
            }
        }

        // 4. Hitung MAE
        $absoluteErrors = [];
        foreach ($actualScores as $itemId => $actual) {
            $predicted = $predictedScores[$itemId] ?? 0;
            $absoluteErrors[] = abs($actual - $predicted);
        }

        $n = count($absoluteErrors);
        $mae = $n > 0 ? array_sum($absoluteErrors) / $n : 0;

        return view('admin.testing.mae', compact('mae', 'n', 'userTest', 'actualScores', 'predictedScores'));
    }

    private function cosineSimilarity($itemA, $itemB)
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;
        $userIds = array_unique(array_merge(array_keys($itemA), array_keys($itemB)));
        foreach ($userIds as $userId) {
            $a = $itemA[$userId] ?? 0;
            $b = $itemB[$userId] ?? 0;
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }
        if ($normA == 0 || $normB == 0) return 0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
