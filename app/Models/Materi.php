<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Materi extends Model {
    protected $table = "materi";
    protected $guarded = [];

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function mata_pelajaran() { return $this->belongsTo(MataPelajaran::class); }
    public function guru() { return $this->belongsTo(User::class, 'guru_id'); }
}
