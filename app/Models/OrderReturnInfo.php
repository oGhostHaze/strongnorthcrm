<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnInfo extends Model
{
    use HasFactory;
    protected $table = 'order_return_infos';
    protected $fillable = [
        'oa_id',
        'oa_no',
        'dr_no',
        'received_by',
        'status',
    ];

    public function return_items()
    {
        return $this->hasMany(OrderReturn::class, 'return_no', 'id');
    }

    public function oa()
    {
        return $this->belongsTo(Order::class, 'oa_id', 'oa_id');
    }

    public function dr()
    {
        return $this->belongsTo(Delivery::class, 'dr_no', 'transno');
    }
}
