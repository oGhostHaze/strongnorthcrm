<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryGift extends Model
{
    use HasFactory;
    protected $table = 'delivery_gifts', $primaryKey = 'gift_id';
    public $timestamps = false;
    protected $fillable = [
        'transno',
        'product_id',
        'item_price',
        'item_qty',
        'item_total',
        'status',
        'type',
    ];

    public function gift()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
