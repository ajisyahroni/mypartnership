<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mail_record';
    protected $primaryKey = 'id_record'; // Gunakan primary key yang benar
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];
}
