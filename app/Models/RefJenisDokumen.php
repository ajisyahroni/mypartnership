<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefJenisDokumen extends Model
{
    use SoftDeletes;
    protected $table = 'ref_jenis_dokumen';
    protected $guarded = [];
}
