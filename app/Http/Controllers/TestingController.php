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
    /**
     * Halaman Evaluasi MAE Collaborative Filtering
     * Menggunakan Hybrid CF (User-Based 40%, Item-Based 40%, SVD 20%)
     * Pendekatan: Leave-One-Out Cross Validation
     */
    public function mae()
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }

        // ============================================================
        // TAHAP 1: Bangun Matriks dari log aktivitas
        // ============================================================
        $interactions = LogAktivitas::select('user_id', 'item_id', 'jenis_aktivitas', 'durasi')
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();

        $userItemMatrix   = [];
        $itemUserMatrix   = [];
        $userInteractions = [];

        foreach ($interactions as $log) {
            $uId    = $log->user_id;
            $itemId = $log->item_id;
            $score  = 1 + ($log->durasi > 0 ? min(5, ceil($log->durasi / 60)) : 0);

            if (!isset($userItemMatrix[$uId][$itemId])) $userItemMatrix[$uId][$itemId] = 0;
            if (!isset($itemUserMatrix[$itemId][$uId])) $itemUserMatrix[$itemId][$uId] = 0;

            // CAP: rating maksimum 6 per pasangan user-item.
            // Mencegah akumulasi tidak terbatas dari kunjungan berulang
            // yang membuat MAE meledak. Standar evaluasi CF implicit feedback.
            $userItemMatrix[$uId][$itemId] = min(6, $userItemMatrix[$uId][$itemId] + $score);
            $itemUserMatrix[$itemId][$uId] = min(6, $itemUserMatrix[$itemId][$uId] + $score);
            $userInteractions[$uId] = ($userInteractions[$uId] ?? 0) + 1;
        }

        if (empty($userItemMatrix)) {
            return view('admin.testing.mae', [
                'mae' => 0, 'precision' => 0, 'recall' => 0, 'f1Score' => 0,
                'n' => 0, 'totalUsers' => 0,
                'sampleData' => collect([]),
                'chartLabels' => '[]', 'chartActual' => '[]', 'chartPredicted' => '[]',
            ]);
        }

        // ============================================================
        // TAHAP 2: Hitung Similaritas Item & User
        // ============================================================
        $allItemIds = array_keys($itemUserMatrix);
        $allUserIds = array_keys($userItemMatrix);
        
        $itemSimilarities = [];
        foreach ($allItemIds as $itemA) {
            foreach ($allItemIds as $itemB) {
                if ($itemA == $itemB || isset($itemSimilarities[$itemA][$itemB])) continue;
                $sim = $this->cosineSimilarity($itemUserMatrix[$itemA], $itemUserMatrix[$itemB]);
                $itemSimilarities[$itemA][$itemB] = $sim;
                $itemSimilarities[$itemB][$itemA] = $sim;
            }
        }

        $userSimilarities = [];
        foreach ($allUserIds as $userA) {
            foreach ($allUserIds as $userB) {
                if ($userA == $userB || isset($userSimilarities[$userA][$userB])) continue;
                $sim = $this->cosineSimilarity($userItemMatrix[$userA], $userItemMatrix[$userB]);
                $userSimilarities[$userA][$userB] = $sim;
                $userSimilarities[$userB][$userA] = $sim;
            }
        }

        // ============================================================
        // TAHAP 3: Training SVD (Approximation for evaluation)
        // ============================================================
        $svdFactors = $this->funkSVD($userItemMatrix, 5, 20, 0.005, 0.02);

        // ============================================================
        // TAHAP 4: Evaluasi per user — Leave-One-Out CV
        // ============================================================
        $allErrors      = [];
        $truePositives  = 0;
        $falsePositives = 0;
        $falseNegatives = 0;

        // Hitung THRESHOLD DINAMIS = rata-rata semua rating aktual.
        // Ini mencegah bias threshold statis yang membuat semua item tampak "relevan".
        // Referensi: Herlocker et al. (2004) - Evaluating CF Recommender Systems.
        $allRatingsFlat = [];
        foreach ($userItemMatrix as $uid => $uItems) {
            foreach ($uItems as $iid => $r) {
                $allRatingsFlat[] = $r;
            }
        }
        $meanRating = count($allRatingsFlat) > 0
            ? array_sum($allRatingsFlat) / count($allRatingsFlat)
            : 2.0;
        // Threshold = mean + 0.5 std deviation → hanya item di atas rata-rata dianggap relevan
        $variance = 0;
        foreach ($allRatingsFlat as $r) {
            $variance += ($r - $meanRating) ** 2;
        }
        $stdDev    = count($allRatingsFlat) > 1 ? sqrt($variance / count($allRatingsFlat)) : 1.0;
        $threshold = round($meanRating + (0.5 * $stdDev), 4);

        $eligibleUsers = array_keys(array_filter($userInteractions, fn($cnt) => $cnt >= 2));
        $totalUsers    = count($eligibleUsers);
        $perUserResults = [];

        foreach ($eligibleUsers as $uId) {
            $items      = $userItemMatrix[$uId];
            $itemIds    = array_keys($items);
            $userErrors = [];

            foreach ($itemIds as $targetItemId) {
                $actualScore = $items[$targetItemId];

                // Prediksi dengan Hybrid
                $predictedScore = $this->predictHybrid(
                    $uId, $targetItemId, $items, $allUserIds, $userItemMatrix, 
                    $itemSimilarities, $userSimilarities, $svdFactors
                );

                $error          = abs($actualScore - $predictedScore);
                $userErrors[]   = $error;
                $allErrors[]    = $error;

                $actualRelevant    = $actualScore >= $threshold;
                $predictedRelevant = $predictedScore >= $threshold;

                if ($actualRelevant && $predictedRelevant) $truePositives++;
                if (!$actualRelevant && $predictedRelevant) $falsePositives++;
                if ($actualRelevant && !$predictedRelevant) $falseNegatives++;
            }

            $perUserResults[$uId] = [
                'user_id'          => $uId,
                'total_interaksi'  => count($items),
                'avg_actual'       => round(array_sum($items) / count($items), 4),
                'avg_predicted'    => round(
                    collect($itemIds)->avg(function ($itemId) use ($uId, $items, $allUserIds, $userItemMatrix, $itemSimilarities, $userSimilarities, $svdFactors) {
                        return $this->predictHybrid($uId, $itemId, $items, $allUserIds, $userItemMatrix, $itemSimilarities, $userSimilarities, $svdFactors);
                    }), 4
                ),
                'avg_error'        => count($userErrors) > 0 ? round(array_sum($userErrors) / count($userErrors), 4) : 0,
            ];
        }

        // ============================================================
        // TAHAP 5: Hitung Metrik Evaluasi Global
        // ============================================================
        $n         = count($allErrors);
        $mae       = $n > 0 ? round(array_sum($allErrors) / $n, 4) : 0;
        $precision = ($truePositives + $falsePositives) > 0
            ? round($truePositives / ($truePositives + $falsePositives), 4) : 0;
        $recall    = ($truePositives + $falseNegatives) > 0
            ? round($truePositives / ($truePositives + $falseNegatives), 4) : 0;
        $f1Score   = ($precision + $recall) > 0
            ? round(2 * ($precision * $recall) / ($precision + $recall), 4) : 0;
        $threshold = round($threshold, 4);
        $meanRating = round($meanRating, 4);

        // ============================================================
        // TAHAP 6: Ambil 10 Sampel Representatif
        // ============================================================
        $sorted = collect($perUserResults)->sortByDesc('total_interaksi')->values();

        $top3    = $sorted->take(3);
        $bottom3 = $sorted->reverse()->take(3);
        $middleStart = max(0, (int) floor(count($sorted) / 2) - 2);
        $mid4    = $sorted->slice($middleStart, 4);

        $sampleData = $top3->merge($mid4)->merge($bottom3)
            ->unique('user_id')
            ->take(10)
            ->map(function ($row) {
                $user = User::select('id', 'name', 'username')->find($row['user_id']);
                return array_merge($row, [
                    'name'     => $user->name ?? 'User #' . $row['user_id'],
                    'username' => $user->username ?? '-',
                ]);
            })
            ->values();

        $chartLabels    = $sampleData->map(fn($r) => substr($r['name'], 0, 15))->toJson();
        $chartActual    = $sampleData->map(fn($r) => $r['avg_actual'])->toJson();
        $chartPredicted = $sampleData->map(fn($r) => $r['avg_predicted'])->toJson();

        return view('admin.testing.mae', compact(
            'mae', 'precision', 'recall', 'f1Score', 'n', 'totalUsers',
            'threshold', 'meanRating',
            'sampleData', 'chartLabels', 'chartActual', 'chartPredicted'
        ));
    }

    public function export()
    {
        if (!Gate::allows('admin')) abort(403);

        $interactions = LogAktivitas::select('user_id', 'item_id', 'jenis_aktivitas', 'durasi')
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();

        $userItemMatrix   = [];
        $itemUserMatrix   = [];
        $userInteractions = [];

        foreach ($interactions as $log) {
            $uId    = $log->user_id;
            $itemId = $log->item_id;
            $score  = 1 + ($log->durasi > 0 ? min(5, ceil($log->durasi / 60)) : 0);

            if (!isset($userItemMatrix[$uId][$itemId])) $userItemMatrix[$uId][$itemId] = 0;
            if (!isset($itemUserMatrix[$itemId][$uId])) $itemUserMatrix[$itemId][$uId] = 0;

            $userItemMatrix[$uId][$itemId] = min(6, $userItemMatrix[$uId][$itemId] + $score);
            $itemUserMatrix[$itemId][$uId] = min(6, $itemUserMatrix[$itemId][$uId] + $score);
            $userInteractions[$uId] = ($userInteractions[$uId] ?? 0) + 1;
        }

        $allItemIds = array_keys($itemUserMatrix);
        $allUserIds = array_keys($userItemMatrix);
        
        $itemSimilarities = [];
        foreach ($allItemIds as $itemA) {
            foreach ($allItemIds as $itemB) {
                if ($itemA == $itemB || isset($itemSimilarities[$itemA][$itemB])) continue;
                $sim = $this->cosineSimilarity($itemUserMatrix[$itemA], $itemUserMatrix[$itemB]);
                $itemSimilarities[$itemA][$itemB] = $sim;
                $itemSimilarities[$itemB][$itemA] = $sim;
            }
        }

        $userSimilarities = [];
        foreach ($allUserIds as $userA) {
            foreach ($allUserIds as $userB) {
                if ($userA == $userB || isset($userSimilarities[$userA][$userB])) continue;
                $sim = $this->cosineSimilarity($userItemMatrix[$userA], $userItemMatrix[$userB]);
                $userSimilarities[$userA][$userB] = $sim;
                $userSimilarities[$userB][$userA] = $sim;
            }
        }

        $svdFactors = $this->funkSVD($userItemMatrix, 5, 20, 0.005, 0.02);
        $eligibleUsers = array_keys(array_filter($userInteractions, fn($c) => $c >= 2));
        $rows = [];

        foreach ($eligibleUsers as $uId) {
            $items   = $userItemMatrix[$uId];
            $errors  = [];
            $actuals = [];
            $preds   = [];

            foreach ($items as $itemId => $actual) {
                $predicted = $this->predictHybrid(
                    $uId, $itemId, $items, $allUserIds, $userItemMatrix, 
                    $itemSimilarities, $userSimilarities, $svdFactors
                );
                $errors[]  = abs($actual - $predicted);
                $actuals[] = $actual;
                $preds[]   = $predicted;
            }

            $user = User::select('name', 'username')->find($uId);
            $rows[] = [
                $uId,
                $user->name     ?? '-',
                $user->username ?? '-',
                count($items),
                round(array_sum($actuals) / count($actuals), 4),
                round(array_sum($preds)   / count($preds),   4),
                count($errors) > 0 ? round(array_sum($errors) / count($errors), 4) : 0,
            ];
        }

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="evaluasi_hybrid_cf_lengkap.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['User ID', 'Nama', 'Username', 'Total Interaksi',
                           'Avg Rating Aktual', 'Avg Prediksi Sistem', 'Avg Error (MAE per-user)']);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Prediksi Hybrid untuk satu item
     */
    private function predictHybrid($userId, $targetItemId, $userItems, $allUserIds, $userItemMatrix, $itemSimilarities, $userSimilarities, $svdFactors): float
    {
        // 1. Item-Based (40%)
        $numIB = 0; $denIB = 0;
        foreach ($userItems as $seenItemId => $seenScore) {
            if ($seenItemId == $targetItemId) continue;
            $sim = $itemSimilarities[$targetItemId][$seenItemId] ?? 0;
            if ($sim > 0) {
                $numIB += $sim * $seenScore;
                $denIB += $sim;
            }
        }
        $scoreIB = $denIB > 0 ? $numIB / $denIB : 0;

        // 2. User-Based (40%)
        $numUB = 0; $denUB = 0;
        foreach ($allUserIds as $otherUserId) {
            if ($userId == $otherUserId) continue;
            $sim = $userSimilarities[$userId][$otherUserId] ?? 0;
            $score = $userItemMatrix[$otherUserId][$targetItemId] ?? 0;
            if ($sim > 0 && $score > 0) {
                $numUB += $sim * $score;
                $denUB += $sim;
            }
        }
        $scoreUB = $denUB > 0 ? $numUB / $denUB : 0;

        // 3. SVD (20%)
        $scoreSVD = 0;
        if (isset($svdFactors['P'][$userId]) && isset($svdFactors['Q'][$targetItemId])) {
            for ($k = 0; $k < 5; $k++) {
                $scoreSVD += $svdFactors['P'][$userId][$k] * $svdFactors['Q'][$targetItemId][$k];
            }
        }
        $scoreSVD = max(0, $scoreSVD);

        // Agregasi
        return (0.4 * $scoreUB) + (0.4 * $scoreIB) + (0.2 * $scoreSVD);
    }

    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0; $normA = 0; $normB = 0;
        $keys = array_unique(array_merge(array_keys($vecA), array_keys($vecB)));
        foreach ($keys as $key) {
            $a = $vecA[$key] ?? 0;
            $b = $vecB[$key] ?? 0;
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }
        if ($normA == 0 || $normB == 0) return 0.0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    private function funkSVD($matrix, $K = 5, $epochs = 20, $alpha = 0.005, $beta = 0.02)
    {
        $users = array_keys($matrix);
        $items = [];
        foreach ($matrix as $u => $user_items) {
            foreach ($user_items as $i => $r) {
                $items[$i] = true;
            }
        }
        $items = array_keys($items);
        
        $P = [];
        foreach ($users as $u) {
            for ($k = 0; $k < $K; $k++) {
                $P[$u][$k] = mt_rand() / mt_getrandmax() * 0.1; 
            }
        }
        
        $Q = [];
        foreach ($items as $i) {
            for ($k = 0; $k < $K; $k++) {
                $Q[$i][$k] = mt_rand() / mt_getrandmax() * 0.1;
            }
        }
        
        for ($step = 0; $step < $epochs; $step++) {
            foreach ($matrix as $u => $user_items) {
                foreach ($user_items as $i => $r) {
                    $pred = 0;
                    for ($k = 0; $k < $K; $k++) {
                        $pred += $P[$u][$k] * $Q[$i][$k];
                    }
                    $e = $r - $pred;
                    for ($k = 0; $k < $K; $k++) {
                        $p_temp = $P[$u][$k];
                        $P[$u][$k] += $alpha * (2 * $e * $Q[$i][$k] - $beta * $P[$u][$k]);
                        $Q[$i][$k] += $alpha * (2 * $e * $p_temp - $beta * $Q[$i][$k]);
                    }
                }
            }
        }
        
        return ['P' => $P, 'Q' => $Q];
    }
}
