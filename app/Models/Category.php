<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'tblcategories', $primaryKey = 'category_id';
    public $timestamps = false;
    protected $fillable = [
                            'category_name',
                        ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }


}
