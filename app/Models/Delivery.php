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
}
