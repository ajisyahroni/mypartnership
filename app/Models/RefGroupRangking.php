<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefGroupRangking extends Model
{
    use SoftDeletes;
    protected $table = 'ref_group_rangking';
    protected $guarded = [];

    public function getRangking(){
        return $this->hasMany(RefRangkingUniversitas::class,'type','group');
    }
}
