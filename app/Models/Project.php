<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            $project->project_id = self::generateRandomString(16);
        });
    }

    public function beams()
    {
        return $this->hasMany(Beam::class, 'project_id');
    }

    public function poles()
    {
        return $this->hasMany(Pole::class, 'project_id');
    }

    public function highMasts()
    {
        return $this->hasMany(HighMast::class, 'project_id');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    private static function generateRandomString($length = 16)
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, $length);
    }
}
