<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'inv_date',
    ];

    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'id', 'inventory_date_id');
    }
}
