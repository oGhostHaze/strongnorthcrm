<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicingInfo extends Model
{
    use HasFactory;
    protected $table = 'servicing_info', $primaryKey = 'info_id';
    protected $fillable = [
        'date_received',
        'received_from',
        'inspected_by',
        'client',
        'contact_no',
        'sr_no',
        'inc',
        'estimated_cost',
        'status',
    ];
    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(ServicingItem::class, 'sr_id', 'info_id');
    }
}
