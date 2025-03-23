<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table = 'delivery_info', $primaryKey = 'info_id';
    public $timestamps = false;
    protected $fillable = [
                            'transno',
                            'client',
                            'address',
                            'contact',
                            'consultant',
                            'associate',
                            'presenter',
                            'team_builder',
                            'distributor',
                            'code',
                            'date',
                            'dr_count',
                            'price_diff',
                            'price_override',
                            'dr_reference',
                            'oa_no',
                            'print_count',
                        ];

    public function oa()
    {
        return $this->belongsTo(Order::class, 'oa_no', 'oa_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryItem::class, 'transno', 'transno');
    }

    public function gifts()
    {
        return $this->hasMany(DeliveryGift::class, 'transno', 'transno');
    }

    public function payments()
    {
        return $this->hasMany(OrderPaymentHistory::class, 'delivery_id', 'info_id');
    }

    /**
     * Get the total amount of payments associated with this delivery
     *
     * @return float
     */
    public function getTotalPayments()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Check if this delivery has any payments
     *
     * @return bool
     */
    public function hasPayments()
    {
        return $this->payments()->count() > 0;
    }
}