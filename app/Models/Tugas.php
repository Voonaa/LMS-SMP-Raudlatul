<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tugas extends Model
{
    protected $fillable = ['kelas_id', 'mata_pelajaran_id', 'guru_id', 'judul', 'deskripsi', 'file_lampiran', 'tenggat_waktu'];

    protected $casts = ['tenggat_waktu' => 'datetime'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mata_pelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function pengumpulan(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class);
    }
}
