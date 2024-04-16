<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    use HasFactory;
    protected $table = 'delivery_items', $primaryKey = 'item_id';
    public $timestamps = false;
    protected $fillable = [
        'transno',
        'product_id',
        'item_price',
        'item_qty',
        'item_total',
        'status',
        'tblset_id'
    ];

    public function item()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
