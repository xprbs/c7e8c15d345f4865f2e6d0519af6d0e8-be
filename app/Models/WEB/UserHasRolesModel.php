<?php

namespace App\Models\WEB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WEB\RolesModel;

class UserHasRolesModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_has_roles' ;
    protected $guarded = [];

    public function roleName()
    {
        return $this->hasOne(RolesModel::class, 'role_uid', 'role_uid');
    }
}
