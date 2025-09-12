<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModeOfPayment;

class OrderPaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'order_payment_histories', $primaryKey = 'id';

    protected $fillable = [
        'batch_receipt_number', // The main batch receipt number
        'receipt_number',       // Full receipt number including sequence
        'receipt_sequence',     // Sequence within the batch
        'oa_id',
        'delivery_id',          // New field to link to delivery
        'mop',                  // Mode of Payment: ModeOfPayment::all(); saves the legend column
        'amount',
        'date_of_payment',
        'remarks',
        'status',               // Posted, Unposted, On-hold
        'due_date',
        'pdc_date',
        'reference_no',
        'recon_date',
    ];

    public function details()
    {
        return $this->belongsTo(Order::class, 'oa_id', 'oa_id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id', 'info_id');
    }

    /**
     * Get the formatted receipt number for display
     *
     * @return string
     */
    public function getFormattedReceiptNumber()
    {
        // Fallback for older records without receipt numbers
        return 'PR-' . date('ymd', strtotime($this->created_at)) . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get all payment entries in the same batch
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBatchPayments()
    {
        if (empty($this->batch_receipt_number)) {
            return collect([$this]);
        }

        return self::where('batch_receipt_number', $this->batch_receipt_number)
            ->orderBy('receipt_sequence')
            ->get();
    }

    /**
     * Get total amount for the batch
     *
     * @return float
     */
    public function getBatchTotal()
    {
        if (empty($this->batch_receipt_number)) {
            return $this->amount;
        }

        return self::where('batch_receipt_number', $this->batch_receipt_number)
            ->sum('amount');
    }

    /**
     * Check if this payment is part of a batch
     *
     * @return bool
     */
    public function isPartOfBatch()
    {
        if (empty($this->batch_receipt_number)) {
            return false;
        }

        return self::where('batch_receipt_number', $this->batch_receipt_number)
            ->count() > 1;
    }
}
