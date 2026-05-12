<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ForumThread extends Model {
    protected $table = "forum_threads";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mata_pelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
