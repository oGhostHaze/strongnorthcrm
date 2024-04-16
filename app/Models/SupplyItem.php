<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_purchased',
        'item_name',
        'qty',
        'unit',
        'location',
        'category',
        'unit_price',
    ];

    public function category_name()
    {
        return $this->belongsTo(SupplyCategory::class, 'category');
    }

    public function location_name()
    {
        return $this->belongsTo(SupplyLocation::class, 'location');
    }

    public function unit_name()
    {
        return $this->belongsTo(UnitOfMeasurement::class, 'unit');
    }
}
