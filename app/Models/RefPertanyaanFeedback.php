<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefPertanyaanFeedback extends Model
{
    use SoftDeletes;
    protected $table = 'ref_pertanyaan_feedback';
    protected $guarded = [];
}
