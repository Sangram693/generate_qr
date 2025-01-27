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
    public $incrementing = false; // Prevent auto-increment issues
    protected $keyType = 'string'; 

    protected $fillable = [
        'id',
        'name',
        'description',
        'model_number',
        'serial_number',
        'bach_number',
        'manufacturer',
        'beam_type',
        'beam_shape',
        'beam_length',
        'beam_width',
        'beam_height',
        'beam_weight',
    ];
}
