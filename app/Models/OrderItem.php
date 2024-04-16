<?php

namespace App\Models;

use App\Models\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    protected $table = 'order_items', $primaryKey = 'item_id';
    public $timestamps = false;
    protected $fillable = [
                            'oa_id',
                            'product_id',
                            'item_price',
                            'item_qty',
                            'item_total',
                            'status',
                            'remarks',
                            'released',
                            'returned',
                            'tblset_id'
                        ];

    public function details()
    {
        return $this->belongsTo(Order::class, 'oa_id', 'oa_id');
    }

    public function item()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function pending_items()
    {
        return $this->where('status', 'Pending')->orWhere('status', 'To Follow');
    }

    public function set()
    {
        return $this->belongsTo(Set::class, 'tblset_id', 'set_id');
    }
}
