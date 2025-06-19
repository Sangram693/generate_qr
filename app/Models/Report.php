<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_name',
        'qty',
        'generation_date',
        'delivery_date',
        'page_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generation_date' => 'date',
        'delivery_date' => 'date',
        'qty' => 'integer'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'formatted_generation_date',
        'formatted_delivery_date',
        'status'
    ];

    /**
     * Get the formatted generation date
     * 
     * @return string
     */
    public function getFormattedGenerationDateAttribute(): string
    {
        return Carbon::parse($this->generation_date)->format('d-m-Y');
    }

    /**
     * Get the formatted delivery date
     * 
     * @return string
     */
    public function getFormattedDeliveryDateAttribute(): string
    {
        return $this->delivery_date ? Carbon::parse($this->delivery_date)->format('d-m-Y') : 'Not Delivered';
    }

    /**
     * Get the status of the report
     * 
     * @return string
     */
    public function getStatusAttribute(): string
    {
        return $this->delivery_date ? 'Delivered' : 'Pending';
    }

    /**
     * Scope a query to only include reports from a specific product.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $productName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProduct($query, $productName)
    {
        return $query->where('product_name', $productName);
    }

    /**
     * Scope a query to only include reports between two dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('generation_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include pending deliveries.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->whereNull('delivery_date');
    }

    /**
     * Scope a query to only include delivered reports.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDelivered($query)
    {
        return $query->whereNotNull('delivery_date');
    }

    /**
     * Get the page that this report belongs to
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}