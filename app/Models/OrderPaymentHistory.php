<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'order_payment_histories', $primaryKey = 'id';

    protected $fillable = [
        'oa_id',
        'mop',
        'amount',
        'date_of_payment',
    ];

    public function details()
    {
        return $this->belongsTo(Order::class, 'oa_id', 'oa_id');
    }
}
