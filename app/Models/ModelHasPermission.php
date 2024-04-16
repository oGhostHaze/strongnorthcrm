<?php

namespace App\Models;

use App\Models\PermissionRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModelHasPermission extends Model
{
    use HasFactory;
    protected $table = 'model_has_permissions';

    public function permission()
    {
        return $this->belongsTo(PermissionRelation::class);
    }
}
