<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchandiseDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'transno',
        'print_count',
        'date',
        'client',
        'address',
        'contact',
        'consultant',
        'associate',
        'presenter',
        'team_builder',
        'distributor',
        'mo_no',
        'code',
        'dr_count',
    ];


    public function items()
    {
        return $this->hasMany(MerchandiseDeliveryItem::class, 'transno', 'transno');
    }

    public function mo()
    {
        return $this->belongsTo(MerchandiseOrderHeader::class, 'mo_no', 'transno');
    }
}
