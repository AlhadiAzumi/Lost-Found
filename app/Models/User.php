<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\BelongsToCampus;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use MustVerifyEmailTrait;
    use BelongsToCampus;

    protected $fillable = ['name', 'email', 'nim_nip', 'phone_number', 'password', 'role', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
