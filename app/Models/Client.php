<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';
    protected $primaryKey = 'client_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'contact_number',
        'tin_number',
        'lifechanger_id',
    ];

    /**
     * Get the full name of the client
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->last_name}, {$this->first_name} {$this->middle_name}";
    }

    /**
     * Get all orders associated with this client
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id', 'client_id');
    }
}
