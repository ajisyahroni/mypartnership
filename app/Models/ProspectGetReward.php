<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProspectGetReward extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_prospect_get_reward';
    protected $guarded = [];
}
