<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_name',
        'password',
        'role',
        'admin_id',
        'origin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function beams()
    {
        return $this->hasMany(Beam::class);
    }

    public function poles()
    {
        return $this->hasMany(Pole::class);
    }

    public function highmasts()
    {
        return $this->hasMany(HighMast::class);
    }


}
