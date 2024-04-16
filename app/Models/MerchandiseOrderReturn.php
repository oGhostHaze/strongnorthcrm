<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchandiseOrderReturn extends Model
{
    use HasFactory;

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
        return $this->belongsTo(MerchandiseOrderReturnInfo::class, 'id', 'return_no');
    }

    public function item()
    {
        return $this->belongsTo(MerchandiseItem::class, 'product_id');
    }
}
