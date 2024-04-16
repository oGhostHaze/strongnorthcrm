<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockin extends Model
{
    use HasFactory;
    protected $table = 'tblstockin', $primaryKey = 'stockIn_id';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'date',
        'stockin_qty',
        'stockin_by',
        'remarks',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'stockin_by');
    }
}
