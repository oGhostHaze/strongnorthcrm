<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;
    protected $table = 'tblsets', $primaryKey = 'set_id';
    public $timestamps = false;


    protected $fillable = [
        'set_name',
        'set_price'
    ];

    public function compositions()
    {
        return $this->hasMany(SetComposition::class, 'tblsets_id', 'set_id');
    }

}
