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
     * Merekomendasikan materi untuk siswa berdasarkan Item-Based CF
     */
    public function getRecommendations($userId, $kelasId, $limit = 5)
    {
        $userIds = DB::table('users')->where('kelas_id', $kelasId)->pluck('id')->toArray();
        if(!in_array($userId, $userIds)) {
            $userIds[] = $userId;
        }

        $itemUserMatrix = [];
        $userItems = []; 

        // 1. Baca Materi (+1)
        $logsMateri = LogAktivitas::whereIn('user_id', $userIds)
            ->where('jenis_aktivitas', 'baca_materi')
            ->get();
        foreach ($logsMateri as $log) {
            // implicit rule: durasi also adds point if any? Prompt just says "membaca materi = +1", so let's stick to +1 as base.
            $score = 1;
            if ($log->durasi > 0) {
                $score += min(5, ceil($log->durasi / 60)); 
            }
            $this->addScore($itemUserMatrix, $log->item_id, $log->user_id, $score);
        }

        // 2. Like Forum (+2) & Reply Forum (+3)
        $logsForum = LogAktivitas::whereIn('user_id', $userIds)
            ->whereIn('jenis_aktivitas', ['like_forum', 'reply_forum'])
            ->get();
        
        foreach ($logsForum as $log) {
            $score = $log->jenis_aktivitas == 'like_forum' ? 2 : 3;
            $thread = ForumThread::find($log->item_id);
            if ($thread && $thread->mata_pelajaran_id) {
                // Distribute score to all materi in that mapel for this class
                $materis = Materi::where('mata_pelajaran_id', $thread->mata_pelajaran_id)
                                 ->where('kelas_id', $kelasId)
                                 ->pluck('id');
                foreach ($materis as $materiId) {
                    $this->addScore($itemUserMatrix, $materiId, $log->user_id, $score);
                }
            }
        }

        // 3. Nilai Kuis > 80 (+5)
        $hasilKuis = HasilKuis::whereIn('user_id', $userIds)->where('nilai', '>', 80)->get();
        foreach ($hasilKuis as $hk) {
            $kuis = Kuis::find($hk->kuis_id);
            if ($kuis && $kuis->materi_id) {
                $this->addScore($itemUserMatrix, $kuis->materi_id, $hk->user_id, 5);
            }
        }

        foreach ($itemUserMatrix as $itemId => $usersScore) {
            if (isset($usersScore[$userId])) {
                $userItems[$itemId] = $usersScore[$userId];
            }
        }

        if (empty($userItems) || empty($itemUserMatrix)) {
            // COLD-START: Gunakan hasil diagnostik jika ada, fallback ke random
            return $this->getColdStartRecommendations($userId, $kelasId, $limit);
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
            // GUARD: fallback hanya materi kelas sendiri
            return Materi::where('kelas_id', $kelasId)
                ->whereNotIn('id', $seenIds)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // GUARD UTAMA: Walau CF menghitung similarity dari semua item,
        // hasil akhir WAJIB di-filter hanya untuk kelas pengguna.
        // Ini mencegah materi kelas lain masuk ke rekomendasi.
        $placeholders = implode(',', array_fill(0, count($recommendedItemIds), '?'));
        return Materi::whereIn('id', $recommendedItemIds)
            ->where('kelas_id', $kelasId)  // <-- CONTEXT-AWARE PRIVACY FILTER
            ->orderByRaw("FIELD(id, $placeholders)", $recommendedItemIds)
            ->get();
    }

    private function addScore(&$matrix, $itemId, $userId, $score)
    {
        if (!isset($matrix[$itemId])) {
            $matrix[$itemId] = [];
        }
        if (!isset($matrix[$itemId][$userId])) {
            $matrix[$itemId][$userId] = 0;
        }
        $matrix[$itemId][$userId] += $score;
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

    /**
     * Cold-Start Recommendation: Gunakan hasil kuis diagnostik sebagai sinyal awal.
     *
     * Cara kerja:
     * 1. Ambil log_aktivitas siswa yang berasal dari kuis diagnostik (durasi tinggi = nilai rendah)
     * 2. Sortir berdasarkan durasi tertinggi (= mapel paling butuh bantuan)
     * 3. Rekomendasikan materi dari mapel tersebut di kelas siswa
     *
     * Dipanggil oleh getRecommendations() jika user tidak punya cukup data CF normal.
     */
    public function getColdStartRecommendations($userId, $kelasId, $limit = 6)
    {
        // Ambil log diagnostik siswa (durasi tinggi = nilai rendah di mapel itu)
        $diagnostikLogs = LogAktivitas::where('user_id', $userId)
            ->where('jenis_aktivitas', 'baca_materi')
            ->where('durasi', '>=', 60)  // Semua log diagnostik punya durasi >= 60
            ->orderByDesc('durasi')       // Prioritas: durasi tertinggi = mapel paling lemah
            ->limit(5)
            ->get();

        if ($diagnostikLogs->isEmpty()) {
            return Materi::where('kelas_id', $kelasId)->inRandomOrder()->limit($limit)->get();
        }

        // Kumpulkan materi yang berkaitan dengan mapel terlemah
        $recommendations = collect();
        $added = [];

        foreach ($diagnostikLogs as $log) {
            // Cari mata_pelajaran_id dari materi yang ada di log
            $materi = Materi::find($log->item_id);
            if (!$materi) continue;

            $mapelId = $materi->mata_pelajaran_id;

            // Ambil materi di mapel yang sama untuk kelas ini
            $materisMapel = Materi::where('kelas_id', $kelasId)
                ->where('mata_pelajaran_id', $mapelId)
                ->whereNotIn('id', $added)
                ->inRandomOrder()
                ->limit(2)
                ->get();

            foreach ($materisMapel as $m) {
                if (!in_array($m->id, $added)) {
                    $recommendations->push($m);
                    $added[] = $m->id;
                }
            }

            if ($recommendations->count() >= $limit) break;
        }

        // Jika belum cukup, tambah materi random dari kelas
        if ($recommendations->count() < $limit) {
            $extra = Materi::where('kelas_id', $kelasId)
                ->whereNotIn('id', $added)
                ->inRandomOrder()
                ->limit($limit - $recommendations->count())
                ->get();
            $recommendations = $recommendations->merge($extra);
        }

        return $recommendations->take($limit);
    }
}

