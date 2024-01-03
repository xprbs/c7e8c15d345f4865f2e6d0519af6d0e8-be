<?php

namespace App\Models\WEB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PermissionModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'permissions' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
    
        static::creating(function (PermissionModel $item) {
            $item->permissions_uid = (string)Str::uuid() ; //assigning value            
        });
    }
}
