<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'commodity_id',
        'price',
        'lat',
        'lng',
        'quantity_unit',
        'source',
        'reported_at',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'reported_at' => 'datetime',
    ];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }
}