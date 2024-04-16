<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sspl extends Model
{
    use HasFactory;
    protected $connection = 'strongnorthoa';
}
