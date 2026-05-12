<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilKuis extends Model
{
    protected $table = 'hasil_kuis';
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class);
    }
}
