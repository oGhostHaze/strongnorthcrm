<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchandiseOrderHeader extends Model
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
    ];


    public function items()
    {
        return $this->hasMany(MerchandiseOrderItem::class, 'transno', 'transno');
    }

    public function drs()
    {
        return $this->hasMany(MerchandiseDelivery::class, 'mo_no', 'transno');
    }

    public function returns()
    {
        return $this->hasMany(MerchandiseOrderReturnInfo::class, 'oa_id');
    }
}
