<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'item_id',
        'beginning_balance',
        'added',
        'disposed',
    ];

    public function inv()
    {
        return $this->belongsTo(SupplyInventoryDate::class, 'date');
    }

    public function item()
    {
        return $this->belongsTo(SupplyItem::class, 'item_id');
    }
}
