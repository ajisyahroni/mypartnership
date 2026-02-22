<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefBentukKerjaSama extends Model
{
    use SoftDeletes;
    protected $table = 'ref_bentuk_kerjasama';
    protected $guarded = [];

}
