<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beam extends Model
{
    /** @use HasFactory<\Database\Factories\BeamFactory> */
    use HasFactory;

    protected $table = 'beams';
    protected $primaryKey = 'id';
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $guarded = [];
}
