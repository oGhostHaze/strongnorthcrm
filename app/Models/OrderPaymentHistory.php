<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModeOfPayment;

class OrderPaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'order_payment_histories', $primaryKey = 'id';

    protected $fillable = [
        'oa_id',
        'mop', //library: ModeOfPayment::all(); saves the legend column
        'amount',
        'date_of_payment',
        'remarks',
        'status', //Posted, Unposted, On-hold$table->date('due_date')->nullable();
        'due_date',
        'pdc_date',
        'reference_no',
        'recon_date',

    ];

    public function details()
    {
        return $this->belongsTo(Order::class, 'oa_id', 'oa_id');
    }
}