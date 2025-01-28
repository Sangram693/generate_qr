<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viewer extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertising_id', 'ip_address', 'user_agent', 'city', 'country', 'first_seen', 'last_seen'
    ];
}
