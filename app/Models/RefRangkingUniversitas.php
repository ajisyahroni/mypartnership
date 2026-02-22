<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefRangkingUniversitas extends Model
{
    use SoftDeletes;
    protected $table = 'ref_rangking_universitas';
    protected $guarded = [];

}
