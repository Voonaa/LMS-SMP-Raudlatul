<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Kuis extends Model {
    protected $table = "kuis";
    protected $guarded = [];

    public function soal() { return $this->hasMany(SoalKuis::class, 'kuis_id'); }
    public function materi() { return $this->belongsTo(Materi::class); }
    public function mata_pelajaran() { return $this->belongsTo(MataPelajaran::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function guru() { return $this->belongsTo(User::class, 'guru_id'); }
}
