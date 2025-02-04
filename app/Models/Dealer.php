<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dealer extends Model
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    // Automatically hash password before saving
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'dealer_id');
    }
}
