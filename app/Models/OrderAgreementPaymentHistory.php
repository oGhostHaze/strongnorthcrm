<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAgreementPaymentHistory extends Model
{
    use HasFactory;
    protected $connection = 'strongnorthoa';

    protected $fillable = [
        'order_agreement_id',
        'mop',
        'amount',
        'date_of_payment',
        'status',
        'due_date',
        'pdc_date',
        'reference_no',
        'recon_date'
    ];

    public function details()
    {
        return $this->belongsTo(OrderAgreement::class);
    }
}