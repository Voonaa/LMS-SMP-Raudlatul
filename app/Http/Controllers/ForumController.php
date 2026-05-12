<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\LogAktivitas;
use App\Models\MataPelajaran;
use App\Models\Kelas;

class ForumController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = ForumThread::with('user', 'mata_pelajaran', 'replies.user');
        
        if ($user->role === 'siswa') {
            $query->where('kelas_id', $user->kelas_id);
        }

        $threads = $query->latest()->get();
        $mapelList = MataPelajaran::all();
        return view('forum.index', compact('threads', 'user', 'mapelList'));
    }

    public function toggleLike(Request $request)
    {
        $request->validate([
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string',
        ]);

        $user = Auth::user();
        $likeable_id = $request->likeable_id;
        $likeable_type = $request->likeable_type;

        $like = \App\Models\Like::where('user_id', $user->id)
            ->where('likeable_id', $likeable_id)
            ->where('likeable_type', $likeable_type)
            ->first();

        if ($like) {
            $like->delete();
            $action = 'unliked';
            $likesCount = \App\Models\Like::where('likeable_id', $likeable_id)->where('likeable_type', $likeable_type)->count();
            return response()->json(['success' => true, 'action' => $action, 'likes_count' => $likesCount]);
        } else {
            \App\Models\Like::create([
                'user_id' => $user->id,
                'likeable_id' => $likeable_id,
                'likeable_type' => $likeable_type,
            ]);
            $action = 'liked';

            // Add points to the author
            $model = $likeable_type::find($likeable_id);
            if ($model && $model->user_id !== $user->id) {
                $pointGamifikasi = \App\Models\PointGamifikasi::firstOrCreate(
                    ['user_id' => $model->user_id]
                );
                $pointGamifikasi->increment('poin', 5);
            }

            LogAktivitas::create([
                'user_id' => $user->id,
                'jenis_aktivitas' => 'like_forum',
                'item_id' => $likeable_id,
            ]);

            $likesCount = \App\Models\Like::where('likeable_id', $likeable_id)->where('likeable_type', $likeable_type)->count();
            return response()->json(['success' => true, 'action' => $action, 'likes_count' => $likesCount]);
        }
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'konten' => 'required|string'
        ]);

        $user = Auth::user();
        $thread = ForumThread::findOrFail($id);

        // ─── CONTEXT-AWARE PRIVACY: Proteksi lintas kelas ───
        if ($user->role === 'siswa' && (int) $thread->kelas_id !== (int) $user->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke topik forum ini.');
        }
        // ────────────────────────────────────────────────────

        ForumReply::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'konten' => $request->konten
        ]);

        LogAktivitas::create([
            'user_id' => $user->id,
            'jenis_aktivitas' => 'reply_forum',
            'item_id' => $thread->id,
        ]);

        return back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
        ]);

        $user = Auth::user();
        
        ForumThread::create([
            'user_id' => $user->id,
            'kelas_id' => $user->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'judul' => $request->judul,
            'konten' => $request->konten,
        ]);

        return back()->with('success', 'Topik berhasil dibuat!');
    }
}
