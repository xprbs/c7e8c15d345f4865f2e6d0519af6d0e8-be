<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OrganizationModels extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'organizations' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
    
        static::creating(function (OrganizationModels $item) {
            $item->unit_uid = (string)Str::uuid() ; //assigning value            
        });
    }
}
