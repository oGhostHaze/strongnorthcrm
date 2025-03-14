<?php

namespace App\Models;

use App\Models\User;
use App\Models\Contest;
use App\Models\OrderAgreement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CookingShow extends Model
{
    use HasFactory;

    protected $connection = 'strongnorthoa';
    protected $table = 'cooking_shows', $primaryKey = 'cs_id';


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_agreements()
    {
        return $this->hasMany(OrderAgreement::class, 'cs_id', 'cs_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'cs_id', 'cs_id');
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function full_address()
    {
        $line_1 = $this->address . ' ';
        $line_2 = $this->address_2 ? $this->address_2 . ', ' : '';
        $city = $this->city_town ? $this->city_town . ', ' : '';
        $province = $this->province;

        $full_address = $line_1 . $line_2 . $city . $province;

        return $full_address;
    }

    public function host_fullname()
    {
        $surename = $this->host_surename ? $this->host_surename . ', ' : '';
        return $surename . $this->host;
    }

    public function current_result()
    {

        if ($this->result == 'Closed') {
            $result = '
            <div class="shadow-lg badge bg-success">
                <div>
                    <span>' . $this->result . '</span>
                </div>
            </div>';
        } elseif ($this->result == 'For Follow Up') {
            $result = '
            <div class="shadow-lg badge bg-warning">
                <div>
                    <span>' . $this->result . '</span>
                </div>
            </div>';
        } elseif ($this->result == 'Booked') {
            $result = '
            <div class="shadow-lg badge bg-ghost">
                <div>
                    <span>' . $this->result . '</span>
                </div>
            </div>';
        } else {
            $result = '
            <div class="shadow-lg badge bg-error">
                <div>
                    <span>' . $this->result . '</span>
                </div>
            </div>';
        }
        return $result;
    }
}
