<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicingItem extends Model
{
    use HasFactory;
    protected $table = 'servicing_items', $primaryKey = 'item_id';
    protected $fillable = [
        'item_received',
        'parts_included',
        'description',
        'action_needed',
        'status',
        'sr_id',
        'image',
        'price',
        'model'
    ];
    public $timestamps = false;

    public function info()
    {
        return $this->belongsTo(ServicingInfo::class, 'sr_id', 'info_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_received', 'product_id');
    }
}
