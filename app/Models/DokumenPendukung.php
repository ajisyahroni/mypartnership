<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumenPendukung extends Model
{
    protected $table = 'dokumen_pendukung';
    protected $guarded = [];
}
