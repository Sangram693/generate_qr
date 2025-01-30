<?php

namespace App\Models;

use App\Models\Beam;
use App\Models\Pole;
use App\Models\HighMast;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_name',
        'password',
        'role',
        'admin_id'
    ];

    protected $hidden = [
        'password',
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
