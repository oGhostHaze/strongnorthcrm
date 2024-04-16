<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyItemDisposed extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'qty',
        'reason',
    ];

    public function item()
    {
        return $this->belongsTo(SupplyItem::class, 'item_id');
    }
}
