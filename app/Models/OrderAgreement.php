<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAgreement extends Model
{
    use HasFactory;
    protected $connection = 'strongnorthoa';

    protected $fillable = [
        'oa_number',
        'date',
        'client',
        'address',
        'contact',
        'consultant',
        'associate',
        'presenter',
        'team_builder',
        'distributor',
        'user_id',
        'cs_id',
        'status',
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
        return $this->hasMany(OrderAgreementItem::class);
    }

    public function gifts()
    {
        return $this->hasMany(OrderAgreementGift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function final_oa()
    {
        return $this->hasOne(Order::class, 'reference_oa', 'id');
    }
}
