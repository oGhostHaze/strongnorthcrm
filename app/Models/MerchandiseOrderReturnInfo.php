<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchandiseOrderReturnInfo extends Model
{
    use HasFactory;

    protected $table = 'merchandise_order_return_infos';
    protected $fillable = [
        'oa_id',
        'oa_no',
        'received_by',
        'status',
    ];

    public function return_items()
    {
        return $this->hasMany(MerchandiseOrderReturn::class, 'return_no', 'id');
    }

    public function oa()
    {
        return $this->belongsTo(MerchandiseOrderHeader::class, 'oa_id', 'id');
    }
}
