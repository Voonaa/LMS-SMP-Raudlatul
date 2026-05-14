<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\HasilKuis;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\ForumThread;
use Illuminate\Support\Facades\DB;

class CollaborativeFilteringService
{
    /**
     * Merekomendasikan materi untuk siswa berdasarkan Hybrid CF (User-Based, Item-Based, SVD)
     */
    public function getRecommendations($userId, $kelasId, $limit = 5)
    {
        $userIds = DB::table('users')->where('kelas_id', $kelasId)->pluck('id')->toArray();
        if(!in_array($userId, $userIds)) {
            $userIds[] = $userId;
        }

        $userItemMatrix = [];
        $itemUserMatrix = [];

        // 1. Baca Materi (+1)
        $logsMateri = LogAktivitas::whereIn('user_id', $userIds)
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();
        foreach ($logsMateri as $log) {
            $score = 1;
            if ($log->durasi > 0) {
                $score += min(5, ceil($log->durasi / 60)); 
            }
            $this->addScore($userItemMatrix, $itemUserMatrix, $log->user_id, $log->item_id, $score);
        }

        // 2. Like Forum (+2) & Reply Forum (+3)
        $logsForum = LogAktivitas::whereIn('user_id', $userIds)
            ->whereIn('jenis_aktivitas', ['like_forum', 'reply_forum'])
            ->get();
        
        foreach ($logsForum as $log) {
            $score = $log->jenis_aktivitas == 'like_forum' ? 2 : 3;
            $thread = ForumThread::find($log->item_id);
            if ($thread && $thread->mata_pelajaran_id) {
                $materis = Materi::where('mata_pelajaran_id', $thread->mata_pelajaran_id)
                                 ->where('kelas_id', $kelasId)
                                 ->pluck('id');
                foreach ($materis as $materiId) {
                    $this->addScore($userItemMatrix, $itemUserMatrix, $log->user_id, $materiId, $score);
                }
            }
        }

        // 3. Nilai Kuis > 80 (+5)
        $hasilKuis = HasilKuis::whereIn('user_id', $userIds)->where('nilai', '>', 80)->get();
        foreach ($hasilKuis as $hk) {
            $kuis = Kuis::find($hk->kuis_id);
            if ($kuis && $kuis->materi_id) {
                $this->addScore($userItemMatrix, $itemUserMatrix, $hk->user_id, $kuis->materi_id, 5);
            }
        }

        $myItems = $userItemMatrix[$userId] ?? [];

        if (empty($myItems) || empty($itemUserMatrix)) {
            // COLD-START: User baru
            return $this->getColdStartRecommendations($userId, $kelasId, $limit);
        }

        $allClassMateriIds = Materi::where('kelas_id', $kelasId)->pluck('id')->toArray();
        $unseenClassMateri = array_diff($allClassMateriIds, array_keys($myItems));

        if (empty($unseenClassMateri)) {
            // User sudah lihat semua materi kelas
            return $this->getColdStartRecommendations($userId, $kelasId, $limit);
        }

        $allItemIds = array_keys($itemUserMatrix);
        $allUserIds = array_keys($userItemMatrix);

        // ==========================================
        // MODEL 1: ITEM-BASED CF (Bobot 40%)
        // ==========================================
        $itemSimilarities = [];
        $pred_IB = [];
        foreach ($myItems as $seenItemId => $seenScore) {
            foreach ($allItemIds as $otherItemId) {
                if ($seenItemId == $otherItemId) continue;
                if (!isset($itemSimilarities[$seenItemId][$otherItemId])) {
                    $sim = $this->cosineSimilarity($itemUserMatrix[$seenItemId] ?? [], $itemUserMatrix[$otherItemId] ?? []);
                    $itemSimilarities[$seenItemId][$otherItemId] = $sim;
                    $itemSimilarities[$otherItemId][$seenItemId] = $sim; 
                }
            }
        }

        foreach ($allItemIds as $itemId) {
            if (isset($myItems[$itemId])) continue; 
            $numerator = 0; $denominator = 0;
            foreach ($myItems as $seenItemId => $seenScore) {
                $sim = $itemSimilarities[$itemId][$seenItemId] ?? 0;
                if ($sim > 0) {
                    $numerator += $sim * $seenScore;
                    $denominator += $sim;
                }
            }
            $pred_IB[$itemId] = $denominator > 0 ? ($numerator / $denominator) : 0;
        }

        // ==========================================
        // MODEL 2: USER-BASED CF (Bobot 40%)
        // ==========================================
        $userSimilarities = [];
        $pred_UB = [];
        foreach ($allUserIds as $otherUserId) {
            if ($userId == $otherUserId) continue;
            $userSimilarities[$otherUserId] = $this->cosineSimilarity($myItems, $userItemMatrix[$otherUserId] ?? []);
        }

        foreach ($allItemIds as $itemId) {
            if (isset($myItems[$itemId])) continue;
            $numerator = 0; $denominator = 0;
            foreach ($allUserIds as $otherUserId) {
                if ($userId == $otherUserId) continue;
                $sim = $userSimilarities[$otherUserId] ?? 0;
                $score = $userItemMatrix[$otherUserId][$itemId] ?? 0;
                if ($sim > 0 && $score > 0) {
                    $numerator += $sim * $score;
                    $denominator += $sim;
                }
            }
            $pred_UB[$itemId] = $denominator > 0 ? ($numerator / $denominator) : 0;
        }

        // ==========================================
        // MODEL 3: SVD Matrix Factorization (Bobot 20%)
        // ==========================================
        // Konfigurasi Funk SVD ringan
        $svdFactors = $this->funkSVD($userItemMatrix, 5, 20, 0.005, 0.02);
        $pred_SVD = [];
        
        foreach ($allItemIds as $itemId) {
            if (isset($myItems[$itemId])) continue;
            $pred = 0;
            if (isset($svdFactors['P'][$userId]) && isset($svdFactors['Q'][$itemId])) {
                for ($k = 0; $k < 5; $k++) {
                    $pred += $svdFactors['P'][$userId][$k] * $svdFactors['Q'][$itemId][$k];
                }
            }
            $pred_SVD[$itemId] = max(0, $pred); // cegah nilai negatif
        }

        // ==========================================
        // HYBRID AGGREGATION
        // ==========================================
        $hybridPredictions = [];
        foreach ($allItemIds as $itemId) {
            if (isset($myItems[$itemId])) continue;

            // Pastikan item ada di list prediksi, jika tidak default 0
            $score_ib = $pred_IB[$itemId] ?? 0;
            $score_ub = $pred_UB[$itemId] ?? 0;
            $score_svd = $pred_SVD[$itemId] ?? 0;

            // Pembobotan: 40% UB, 40% IB, 20% SVD
            $hybridScore = (0.4 * $score_ub) + (0.4 * $score_ib) + (0.2 * $score_svd);
            
            if ($hybridScore > 0) {
                $hybridPredictions[$itemId] = $hybridScore;
            }
        }

        arsort($hybridPredictions);
        $recommendedItemIds = array_slice(array_keys($hybridPredictions), 0, $limit);

        if (empty($recommendedItemIds)) {
            // Predictions kosong → cold-start fallback
            return $this->getColdStartRecommendations($userId, $kelasId, $limit);
        }

        // GUARD UTAMA: CONTEXT-AWARE PRIVACY
        $placeholders = implode(',', array_fill(0, count($recommendedItemIds), '?'));
        return Materi::whereIn('id', $recommendedItemIds)
            ->where('kelas_id', $kelasId)
            ->with('mata_pelajaran')
            ->orderByRaw("FIELD(id, $placeholders)", $recommendedItemIds)
            ->get();
    }

    private function addScore(&$userMatrix, &$itemMatrix, $userId, $itemId, $score)
    {
        if (!isset($userMatrix[$userId][$itemId])) $userMatrix[$userId][$itemId] = 0;
        $userMatrix[$userId][$itemId] += $score;

        if (!isset($itemMatrix[$itemId][$userId])) $itemMatrix[$itemId][$userId] = 0;
        $itemMatrix[$itemId][$userId] += $score;
    }

    private function cosineSimilarity($vecA, $vecB)
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

        if ($normA == 0 || $normB == 0) return 0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Funk SVD Matrix Factorization menggunakan Gradient Descent
     */
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

    public function getColdStartRecommendations($userId, $kelasId, $limit = 6)
    {
        $logs = LogAktivitas::where('user_id', $userId)
            ->where('jenis_aktivitas', 'baca_materi')
            ->get(['item_id', 'durasi']);

        if ($logs->isEmpty()) {
            return Materi::where('kelas_id', $kelasId)
                ->with('mata_pelajaran')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        $durasiPerMapel = [];  
        foreach ($logs as $log) {
            $materi = Materi::find($log->item_id);
            if (!$materi || (int) $materi->kelas_id !== (int) $kelasId) continue;

            $mapelId = $materi->mata_pelajaran_id;
            if (!isset($durasiPerMapel[$mapelId])) $durasiPerMapel[$mapelId] = 0;
            $durasiPerMapel[$mapelId] += (int) ($log->durasi ?? 0);
        }

        if (empty($durasiPerMapel)) {
            return Materi::where('kelas_id', $kelasId)
                ->with('mata_pelajaran')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        arsort($durasiPerMapel);

        $recommendations = collect();
        $added           = [];
        $perMapelQuota   = max(1, (int) ceil($limit / count($durasiPerMapel)));

        foreach ($durasiPerMapel as $mapelId => $totalDurasi) {
            $materisMapel = Materi::where('kelas_id', $kelasId)    
                ->where('mata_pelajaran_id', $mapelId)
                ->whereNotIn('id', $added)
                ->with('mata_pelajaran')
                ->inRandomOrder()
                ->limit($perMapelQuota)
                ->get();

            foreach ($materisMapel as $m) {
                if (!in_array($m->id, $added)) {
                    $recommendations->push($m);
                    $added[] = $m->id;
                }
            }
            if ($recommendations->count() >= $limit) break;
        }

        if ($recommendations->count() < $limit) {
            $extra = Materi::where('kelas_id', $kelasId)
                ->whereNotIn('id', $added)
                ->with('mata_pelajaran')
                ->inRandomOrder()
                ->limit($limit - $recommendations->count())
                ->get();
            $recommendations = $recommendations->merge($extra);
        }

        return $recommendations->take($limit)->values();
    }
}

