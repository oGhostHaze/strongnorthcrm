<?php

namespace App\Models;

use App\Models\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $connection = 'mysql', $table = 'tblproducts', $primaryKey = 'product_id';
    protected $fillable = [
        'code',
        'product_description',
        'category_id',
        'product_price',
        'spv',
        'tblset_id',
        'reorder_level'
    ];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function set()
    {
        return $this->belongsTo(Set::class, 'tblset_id', 'set_id');
    }
}
