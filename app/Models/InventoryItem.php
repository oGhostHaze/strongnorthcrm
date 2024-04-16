<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_date_id',
        'product_id',
        'beginning_balance',
        'total_delivered',
        'total_released',
        'total_returned',
    ];

    public function inv()
    {
        return $this->belongsTo(InventoryDate::class, 'inventory_date_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
