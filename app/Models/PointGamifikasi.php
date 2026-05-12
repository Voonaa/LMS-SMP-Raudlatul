<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointGamifikasi extends Model
{
    //
    protected $table = 'point_gamifikasi';
    protected $fillable = ['user_id', 'poin'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
