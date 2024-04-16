<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchandiseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transno',
        'product_id',
        'item_price',
        'item_qty_ordered',
        'item_qty_released',
        'item_qty_returned',
        'item_total',
        'type',
    ];

    public function item()
    {
        return $this->belongsTo(MerchandiseItem::class, 'product_id');
    }
}
