<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefJenisHibah extends Model
{
    use SoftDeletes;
    protected $table = 'ref_jenis_hibah';
    protected $guarded = [];
}
