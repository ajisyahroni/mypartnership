<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class LaporanHibah extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_laporan_hibah';
    protected $primaryKey = 'id_laporan_hibah'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];

    public function getPengusul()
    {
        return $this->hasOne(User::class, 'username', 'add_by');
    }
}
