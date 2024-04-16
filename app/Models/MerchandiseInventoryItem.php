<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchandiseInventoryItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'product_id',
        'beginning_balance',
        'total_delivered',
        'total_released',
        'total_returned',
    ];

    public function inv()
    {
        return $this->belongsTo(MerchandiseInventoryDate::class, 'date', 'id');
    }

    public function product()
    {
        return $this->belongsTo(MerchandiseItem::class, 'product_id', 'id');
    }
}
