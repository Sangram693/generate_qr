<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    protected $fillable = [
        'page_height',
        'page_width',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'qr_height',
        'qr_width',
        'excel_file',
        'user_id',
        'product'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
