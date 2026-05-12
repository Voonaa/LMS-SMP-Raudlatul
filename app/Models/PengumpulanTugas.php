<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';
    
    protected $fillable = ['tugas_id', 'user_id', 'file_jawaban', 'nilai', 'komentar'];

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
