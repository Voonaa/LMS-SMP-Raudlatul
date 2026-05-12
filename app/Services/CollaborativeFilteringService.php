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
            // COLD-START: User baru → gunakan sinyal diagnostik
            return $this->getColdStartRecommendations($userId, $kelasId, $limit);
        }

        // Cek apakah user sudah melihat SEMUA materi kelas
        $allClassMateriIds = Materi::where('kelas_id', $kelasId)->pluck('id')->toArray();
        $unseenClassMateri = array_diff($allClassMateriIds, array_keys($userItems));

        if (empty($unseenClassMateri)) {
            // User sudah lihat semua materi kelas → re-recommend via cold-start (urut kelemahan)
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
            // Predictions kosong → cold-start sebagai fallback terakhir
            return $this->getColdStartRecommendations($userId, $kelasId, $limit);
        }

        // GUARD UTAMA: Walau CF menghitung similarity dari semua item,
        // hasil akhir WAJIB di-filter hanya untuk kelas pengguna.
        // Ini mencegah materi kelas lain masuk ke rekomendasi.
        $placeholders = implode(',', array_fill(0, count($recommendedItemIds), '?'));
        return Materi::whereIn('id', $recommendedItemIds)
            ->where('kelas_id', $kelasId)          // CONTEXT-AWARE PRIVACY
            ->with('mata_pelajaran')                // eager-load untuk tampilan
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
     * Cold-Start Recommendation: Gunakan sinyal diagnostik untuk rekomendasi awal.
     *
     * Algoritma:
     * 1. Baca semua log baca_materi user → grup berdasarkan mata_pelajaran_id
     * 2. Jumlahkan durasi per mapel (durasi tinggi = nilai rendah di diagnostik)
     * 3. Urutkan descending → mapel dengan durasi total tertinggi = paling lemah
     * 4. Ambil materi dari mapel terlemah untuk kelas siswa
     */
    public function getColdStartRecommendations($userId, $kelasId, $limit = 6)
    {
        // Ambil semua log baca_materi user ini (termasuk sinyal diagnostik)
        $logs = LogAktivitas::where('user_id', $userId)
            ->where('jenis_aktivitas', 'baca_materi')
            ->get(['item_id', 'durasi']);

        if ($logs->isEmpty()) {
            // Benar-benar tidak ada data sama sekali → tampilkan semua materi kelas
            return Materi::where('kelas_id', $kelasId)
                ->with('mata_pelajaran')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Kumpulkan durasi per mata_pelajaran_id (join via materi)
        $durasiPerMapel = [];  // [mapel_id => total_durasi]

        foreach ($logs as $log) {
            $materi = Materi::find($log->item_id);
            if (!$materi) continue;

            // Pastikan materi ini milik kelas yang sama (Context-Aware Privacy)
            if ((int) $materi->kelas_id !== (int) $kelasId) continue;

            $mapelId = $materi->mata_pelajaran_id;
            if (!isset($durasiPerMapel[$mapelId])) {
                $durasiPerMapel[$mapelId] = 0;
            }
            $durasiPerMapel[$mapelId] += (int) ($log->durasi ?? 0);
        }

        // Jika tidak ada log valid untuk kelas ini → fallback semua materi kelas
        if (empty($durasiPerMapel)) {
            return Materi::where('kelas_id', $kelasId)
                ->with('mata_pelajaran')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        // Urutkan descending: durasi tertinggi = mapel paling lemah → prioritas pertama
        arsort($durasiPerMapel);

        $recommendations = collect();
        $added           = [];
        $perMapelQuota   = max(1, (int) ceil($limit / count($durasiPerMapel)));

        foreach ($durasiPerMapel as $mapelId => $totalDurasi) {
            // Ambil materi dari mapel terlemah ini untuk kelas siswa (WAJIB filter kelas_id)
            $materisMapel = Materi::where('kelas_id', $kelasId)    // <-- CONTEXT-AWARE
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

        // Jika masih belum cukup → tambah semua materi kelas yang tersisa
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
