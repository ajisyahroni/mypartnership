<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SettingBobot extends Model
{
    protected $table = 'tbl_setting_bobot';
    protected $primaryKey = 'id_setting_bobot'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];

    public static function getData()
    {
        // return self::where('id_setting_hibah', '1')->first();

        return self::orderBy('created_at', 'DESC')->first();
    }
}
