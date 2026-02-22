<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JawabanFeedback extends Model
{
    use SoftDeletes;
    protected $table = 'jawaban_feedback';
    protected $guarded = [];
}
