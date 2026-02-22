<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mail_setting';
    protected $primaryKey = 'id_setting';
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];
}
