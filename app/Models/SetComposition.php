<?php

namespace App\Models;

use App\Models\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetComposition extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'qty',
        'tblsets_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function set()
    {
        return $this->hasMany(Set::class, 'tblsets_id', 'set_id');
    }
}
