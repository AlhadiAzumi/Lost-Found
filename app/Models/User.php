<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'nim_nip', 'phone_number', 'password', 'role', 'avatar'];
    protected $hidden = ['password', 'remember_token'];

    public function items() { return $this->hasMany(Item::class); }
    public function claims() { return $this->hasMany(Claim::class); }
    public function activityLogs() { return $this->hasMany(ActivityLog::class); }
}