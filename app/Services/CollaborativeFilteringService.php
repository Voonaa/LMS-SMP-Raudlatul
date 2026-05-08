<?php

namespace App\Services;

use App\Models\LogAktivitas;
use App\Models\Materi;
use Illuminate\Support\Facades\DB;

class CollaborativeFilteringService
{
    /**
     * Merekomendasikan materi untuk siswa berdasarkan Item-Based CF
     */
    public function getRecommendations($userId, $kelasId, $limit = 5)
    {
        $interactions = LogAktivitas::select('user_id', 'item_id', 'jenis_aktivitas', 'durasi')
            ->whereIn('item_id', function($query) use ($kelasId) {
                $query->select('id')->from('materi')->where('kelas_id', $kelasId);
            })
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();

        $itemUserMatrix = [];
        $userItems = []; 

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

            if ($uId == $userId) {
                $userItems[$itemId] = $itemUserMatrix[$itemId][$uId];
            }
        }

        if (empty($userItems) || empty($itemUserMatrix)) {
            return Materi::where('kelas_id', $kelasId)->inRandomOrder()->limit($limit)->get();
        }

        $itemSimilarities = [];
        $allItemIds = array_keys($itemUserMatrix);
        
        foreach ($userItems as $targetItemId => $targetScore) {
            foreach ($allItemIds as $otherItemId) {
                if ($targetItemId == $otherItemId) continue;
                
                if (isset($itemSimilarities[$targetItemId][$otherItemId])) continue;

                $similarity = $this->cosineSimilarity($itemUserMatrix[$targetItemId], $itemUserMatrix[$otherItemId]);
                $itemSimilarities[$targetItemId][$otherItemId] = $similarity;
                $itemSimilarities[$otherItemId][$targetItemId] = $similarity; 
            }
        }

        $predictions = [];
        foreach ($allItemIds as $itemId) {
            if (isset($userItems[$itemId])) continue; 

            $numerator = 0;
            $denominator = 0;

            foreach ($userItems as $seenItemId => $seenScore) {
                $sim = $itemSimilarities[$itemId][$seenItemId] ?? 0;
                if ($sim > 0) {
                    $numerator += $sim * $seenScore;
                    $denominator += $sim;
                }
            }

            if ($denominator > 0) {
                $predictions[$itemId] = $numerator / $denominator;
            }
        }

        arsort($predictions);
        $recommendedItemIds = array_slice(array_keys($predictions), 0, $limit);

        if (empty($recommendedItemIds)) {
            $seenIds = array_keys($userItems);
            return Materi::where('kelas_id', $kelasId)
                ->whereNotIn('id', $seenIds)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        $placeholders = implode(',', array_fill(0, count($recommendedItemIds), '?'));
        return Materi::whereIn('id', $recommendedItemIds)
            ->orderByRaw("FIELD(id, $placeholders)", $recommendedItemIds)
            ->get();
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
