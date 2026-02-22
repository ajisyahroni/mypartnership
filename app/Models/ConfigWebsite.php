<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigWebsite extends Model
{
    use HasFactory;

    protected $table = 'config_website';
    protected $guarded = [];
}
