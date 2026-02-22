<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'username', 'password', 'email', 'last_seen', 'last_login', 'deleted_at', 'is_active', 'uuid', 'google_id', 'email_verified_at', 'avatar_google', 'remember_token', 'place_state', 'status_user', 'status_tempat', 'jabatan', 'uuid_jabatan', 'uniid'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isOnline()
    {
        return $this->last_seen && Carbon::parse($this->last_seen)->diffInMinutes(now()) < 5;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getJabatan()
    {
        return $this->hasOne(RefJabatan::class, 'nama_jabatan', 'jabatan');
    }

    public function getChatByMou($id_mou)
    {
        return $this->hasMany(Chat::class, 'sender_id', 'id')
            ->where('is_seen', '0')
            ->where('id_mou', $id_mou);
    }


    public function getChat()
    {
        return $this->hasMany(Chat::class, 'sender_id', 'id')->where('is_seen', '0');
    }

    public function getChatRekognisi()
    {
        return $this->hasMany(ChatRekognisi::class, 'sender_id', 'id')->where('is_seen', '0');
    }
}
