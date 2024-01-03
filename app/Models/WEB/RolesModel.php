<?php

namespace App\Models\WEB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RolesModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'roles' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
    
        static::creating(function (RolesModel $item) {
            $item->role_uid = (string)Str::uuid() ; //assigning value            
        });
    }
}
