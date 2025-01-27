<?php

namespace App\Models;

use App\Models\Delivery;
use App\Models\OrderGift;
use App\Models\OrderItem;
use App\Models\OrderPaymentHistory;
use App\Models\OrderReturnInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'orders', $primaryKey = 'oa_id';
    public $timestamps = false;
    protected $fillable = [
        'oa_number',
        'oa_date',
        'oa_client',
        'oa_address',
        'oa_contact',
        'oa_consultant',
        'oa_associate',
        'oa_presenter',
        'oa_team_builder',
        'oa_distributor',
        'oa_count',
        'oa_price_diff',
        'oa_price_override',
        'oa_status',
        'oa_ar',
        'price_diff',
        'price_override',
        'reference_oa',

        'current_level',
        'delivery_date',
        'delivery_time',
        'initial_investment',
        'terms',
        'host_signature',
        'price_diff',
        'price_override',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'oa_id', 'oa_id');
    }

    public function gifts()
    {
        return $this->hasMany(OrderGift::class, 'oa_id', 'oa_id');
    }

    public function drs()
    {
        return $this->hasMany(Delivery::class, 'oa_no', 'oa_id');
    }

    public function returns()
    {
        return $this->hasMany(OrderReturnInfo::class, 'oa_id', 'oa_id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPaymentHistory::class, 'oa_id', 'oa_id')->latest('date_of_payment');
    }

    public function percentage()
    {
        $subtotal = $this->oa_price_override ? $this->oa_price_override : $this->items()->sum('item_total');
        $price_diff = $this->oa_price_diff;
        $total = (float) $subtotal + (float) $price_diff;
        $total_paid = $this->payments->sum('amount');
        $percentage = 0;
        if ($total_paid > 0) {
            $percentage = $total_paid / $total;
        }

        return number_format($percentage * 100, 0);
    }
}
