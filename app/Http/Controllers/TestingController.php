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
     * Menggunakan Item-Based CF dengan Cosine Similarity
     * Pendekatan: Leave-One-Out Cross Validation
     */
    public function mae()
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }

        // ============================================================
        // TAHAP 1: Bangun Matriks Item-User dari log aktivitas
        // Setiap baris = item (materi), setiap kolom = user
        // Nilai sel = implicit rating berdasarkan jumlah akses & durasi
        // ============================================================
        $interactions = LogAktivitas::select('user_id', 'item_id', 'jenis_aktivitas', 'durasi')
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();

        // Bangun matriks user-item & hitung total interaksi per user
        $userItemMatrix  = [];  // [user_id][item_id] = rating
        $userInteractions = []; // [user_id] = total count interaksi

        foreach ($interactions as $log) {
            $uId    = $log->user_id;
            $itemId = $log->item_id;

            // Implicit rating: 1 (akses) + durasi bonus (max 5)
            $score = 1;
            if ($log->durasi > 0) {
                $score += min(5, ceil($log->durasi / 60));
            }

            if (!isset($userItemMatrix[$uId][$itemId])) {
                $userItemMatrix[$uId][$itemId] = 0;
            }
            $userItemMatrix[$uId][$itemId] += $score;
            $userInteractions[$uId] = ($userInteractions[$uId] ?? 0) + 1;
        }

        // Jika tidak ada data sama sekali
        if (empty($userItemMatrix)) {
            return view('admin.testing.mae', [
                'mae' => 0, 'precision' => 0, 'recall' => 0,
                'n' => 0, 'totalUsers' => 0,
                'sampleData' => collect([]),
                'chartLabels' => '[]', 'chartActual' => '[]', 'chartPredicted' => '[]',
                'materiNames' => [],
            ]);
        }

        // ============================================================
        // TAHAP 2: Transposisi → Item-User Matrix untuk Cosine Similarity
        // ============================================================
        $itemUserMatrix = [];
        foreach ($userItemMatrix as $uId => $items) {
            foreach ($items as $itemId => $rating) {
                if (!isset($itemUserMatrix[$itemId])) {
                    $itemUserMatrix[$itemId] = [];
                }
                $itemUserMatrix[$itemId][$uId] = $rating;
            }
        }

        // ============================================================
        // TAHAP 3: Hitung Cosine Similarity antar item
        // ============================================================
        $allItemIds = array_keys($itemUserMatrix);
        $itemSimilarities = [];

        foreach ($allItemIds as $itemA) {
            foreach ($allItemIds as $itemB) {
                if ($itemA == $itemB) continue;
                if (isset($itemSimilarities[$itemA][$itemB])) continue;
                $sim = $this->cosineSimilarity($itemUserMatrix[$itemA], $itemUserMatrix[$itemB]);
                $itemSimilarities[$itemA][$itemB] = $sim;
                $itemSimilarities[$itemB][$itemA] = $sim;
            }
        }

        // ============================================================
        // TAHAP 4: Evaluasi per user — Leave-One-Out CV
        // Untuk setiap user: prediksi satu item yang sudah berinteraksi
        // menggunakan item lainnya yang sudah berinteraksi.
        // ============================================================
        $allErrors     = [];
        $truePositives = 0;
        $falsePositives = 0;
        $falseNegatives = 0;
        $threshold     = 2.0; // Rating >= threshold dianggap "relevan"

        // Ambil semua user yang punya minimal 2 interaksi agar bisa LOO
        $eligibleUsers = array_keys(array_filter($userInteractions, fn($cnt) => $cnt >= 2));
        $totalUsers    = count($eligibleUsers);

        // Data per-user untuk tabel sampel
        $perUserResults = [];

        foreach ($eligibleUsers as $uId) {
            $items      = $userItemMatrix[$uId]; // [itemId => rating]
            $itemIds    = array_keys($items);
            $userErrors = [];

            foreach ($itemIds as $targetItemId) {
                $actualScore = $items[$targetItemId];

                // Prediksi: weighted average similarity dari item lain user ini
                $numerator   = 0;
                $denominator = 0;
                foreach ($itemIds as $seenItemId) {
                    if ($seenItemId == $targetItemId) continue;
                    $sim = $itemSimilarities[$targetItemId][$seenItemId] ?? 0;
                    if ($sim > 0) {
                        $numerator   += $sim * $items[$seenItemId];
                        $denominator += $sim;
                    }
                }

                $predictedScore = $denominator > 0 ? $numerator / $denominator : 0;
                $error          = abs($actualScore - $predictedScore);
                $userErrors[]   = $error;
                $allErrors[]    = $error;

                // Precision & Recall (threshold-based)
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
                    collect($itemIds)->avg(function ($itemId) use ($items, $itemIds, $itemSimilarities, $targetItemId) {
                        return $this->predictOneItem($itemId, $items, $itemSimilarities);
                    }), 4
                ),
                'avg_error'        => count($userErrors) > 0 ? round(array_sum($userErrors) / count($userErrors), 4) : 0,
            ];
        }

        // ============================================================
        // TAHAP 5: Hitung MAE, Precision, Recall Global
        // ============================================================
        $n         = count($allErrors);
        $mae       = $n > 0 ? round(array_sum($allErrors) / $n, 4) : 0;
        $precision = ($truePositives + $falsePositives) > 0
            ? round($truePositives / ($truePositives + $falsePositives), 4) : 0;
        $recall    = ($truePositives + $falseNegatives) > 0
            ? round($truePositives / ($truePositives + $falseNegatives), 4) : 0;

        // ============================================================
        // TAHAP 6: Ambil 10 Sampel Representatif
        // 3 paling aktif, 4 sedang, 3 paling pasif
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

        // ============================================================
        // TAHAP 7: Data untuk Chart.js (Sampel 10 siswa)
        // ============================================================
        $chartLabels    = $sampleData->map(fn($r) => substr($r['name'], 0, 15))->toJson();
        $chartActual    = $sampleData->map(fn($r) => $r['avg_actual'])->toJson();
        $chartPredicted = $sampleData->map(fn($r) => $r['avg_predicted'])->toJson();

        return view('admin.testing.mae', compact(
            'mae', 'precision', 'recall', 'n', 'totalUsers',
            'sampleData', 'chartLabels', 'chartActual', 'chartPredicted'
        ));
    }

    /**
     * Export data evaluasi lengkap semua siswa ke CSV
     */
    public function export()
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }

        $interactions = LogAktivitas::select('user_id', 'item_id', 'jenis_aktivitas', 'durasi')
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();

        $userItemMatrix   = [];
        $userInteractions = [];

        foreach ($interactions as $log) {
            $uId    = $log->user_id;
            $itemId = $log->item_id;
            $score  = 1 + ($log->durasi > 0 ? min(5, ceil($log->durasi / 60)) : 0);

            if (!isset($userItemMatrix[$uId][$itemId])) {
                $userItemMatrix[$uId][$itemId] = 0;
            }
            $userItemMatrix[$uId][$itemId] += $score;
            $userInteractions[$uId] = ($userInteractions[$uId] ?? 0) + 1;
        }

        $itemUserMatrix = [];
        foreach ($userItemMatrix as $uId => $items) {
            foreach ($items as $itemId => $rating) {
                $itemUserMatrix[$itemId][$uId] = $rating;
            }
        }

        $allItemIds = array_keys($itemUserMatrix);
        $itemSimilarities = [];
        foreach ($allItemIds as $iA) {
            foreach ($allItemIds as $iB) {
                if ($iA == $iB || isset($itemSimilarities[$iA][$iB])) continue;
                $sim = $this->cosineSimilarity($itemUserMatrix[$iA], $itemUserMatrix[$iB]);
                $itemSimilarities[$iA][$iB] = $sim;
                $itemSimilarities[$iB][$iA] = $sim;
            }
        }

        $eligibleUsers = array_keys(array_filter($userInteractions, fn($c) => $c >= 2));
        $rows = [];

        foreach ($eligibleUsers as $uId) {
            $items   = $userItemMatrix[$uId];
            $errors  = [];
            $actuals = [];
            $preds   = [];

            foreach ($items as $itemId => $actual) {
                $predicted = $this->predictOneItem($itemId, $items, $itemSimilarities);
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
            'Content-Disposition' => 'attachment; filename="evaluasi_cf_lengkap.csv"',
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
     * Prediksi satu item untuk satu user berdasarkan item lainnya
     */
    private function predictOneItem(int $targetItemId, array $userItems, array $itemSimilarities): float
    {
        $numerator   = 0;
        $denominator = 0;
        foreach ($userItems as $seenItemId => $seenScore) {
            if ($seenItemId == $targetItemId) continue;
            $sim = $itemSimilarities[$targetItemId][$seenItemId] ?? 0;
            if ($sim > 0) {
                $numerator   += $sim * $seenScore;
                $denominator += $sim;
            }
        }
        return $denominator > 0 ? $numerator / $denominator : 0.0;
    }

    /**
     * Cosine Similarity antara dua vektor item
     */
    private function cosineSimilarity(array $itemA, array $itemB): float
    {
        $dotProduct = 0;
        $normA      = 0;
        $normB      = 0;
        $userIds    = array_unique(array_merge(array_keys($itemA), array_keys($itemB)));
        foreach ($userIds as $uid) {
            $a = $itemA[$uid] ?? 0;
            $b = $itemB[$uid] ?? 0;
            $dotProduct += $a * $b;
            $normA      += $a * $a;
            $normB      += $b * $b;
        }
        if ($normA == 0 || $normB == 0) return 0.0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
