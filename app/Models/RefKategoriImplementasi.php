<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefKategoriImplementasi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ref_kategori_implementasi';
    protected $guarded = [];
}
