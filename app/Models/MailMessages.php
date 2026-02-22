<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailMessages extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mail_messages';
    protected $primaryKey = 'id';
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string';
    protected $guarded = [];
}
