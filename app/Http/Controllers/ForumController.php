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
        return view('forum.index', compact('threads', 'user'));
    }

    public function like($id)
    {
        $user = Auth::user();
        $thread = ForumThread::findOrFail($id);

        LogAktivitas::create([
            'user_id' => $user->id,
            'jenis_aktivitas' => 'like_forum',
            'item_id' => $thread->id,
        ]);

        return back();
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'konten' => 'required|string'
        ]);

        $user = Auth::user();
        $thread = ForumThread::findOrFail($id);

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
}
