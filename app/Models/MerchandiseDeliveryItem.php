<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchandiseDeliveryItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'transno',
        'product_id',
        'item_price',
        'item_qty',
        'item_total',
        'status',
        'type',
    ];

    public function item()
    {
        return $this->belongsTo(MerchandiseItem::class, 'product_id');
    }

}
