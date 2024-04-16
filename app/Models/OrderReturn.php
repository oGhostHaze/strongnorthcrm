<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;
    protected $table = 'order_returns', $primaryKey = 'return_id';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'qty',
        'date_returned',
        'received_by',
        'oa_id',
        'item_type',
        'return_no',
        'reason',
        'item_id',
    ];

    public function info()
    {
        return $this->belongsTo(OrderReturnInfo::class, 'id', 'return_no');
    }

    public function item()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
