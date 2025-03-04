<?php

namespace App\Models\WEB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class RolesModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'roles' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        
        static::addGlobalScope('entity_uid', function (Builder $builder) {
            $builder->where('entity_uid', Auth::user()->entity_uid);
        });
    
        static::creating(function (RolesModel $item) {
            $item->role_uid = (string)Str::uuid() ; //assigning value     
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity             
        });
    }
}
