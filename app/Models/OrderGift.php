<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderGift extends Model
{
    use HasFactory;
    protected $table = 'order_gifts', $primaryKey = 'gift_id';
    public $timestamps = false;
    protected $fillable = [
        'oa_id',
        'product_id',
        'item_price',
        'item_qty',
        'item_total',
        'status',
        'type',
        'released',
        'returned',
    ];

    public function details()
    {
        return $this->belongsTo(Order::class, 'oa_id', 'oa_id');
    }

    public function gift()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function pending_items()
    {
        return $this->where('status', 'Pending')->orWhere('status', 'To Follow');
    }
}
