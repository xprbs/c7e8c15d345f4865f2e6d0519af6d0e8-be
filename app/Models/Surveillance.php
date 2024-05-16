<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Auth;
use App\Models\OrganizationModels;
use App\Models\SurveillanceDetail;

class Surveillance extends Model
{
    use HasFactory;
    
    public const IS_COMPLETED = 30 ;
    public const IS_REJECTED = 40 ;
    public const IS_REVIEW = 20 ;

    public const STATUS = [ 
        0 => 'Created', 
        20 => 'On Review', 
        30 => 'Completed' ,
        40 => 'Rejected' 
    ];

    public static function boot() {
        parent::boot();
        
        static::addGlobalScope('entity_uid', function (Builder $builder) {
            $builder->where('entity_uid', Auth::user()->entity_uid);
        });

        static::creating(function (Surveillance $item) {
            $item->project_uid = (string)Str::uuid() ; //assigning value            
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity
            $item->created_by = Auth::user()->user_uid ;             
        });
    }

    public function dept() 
    {
        return $this->hasOne(OrganizationModels::class,"unit_code","project_location");
    }

    public function detail()
    {
        return $this->hasMany(SurveillanceDetail::class,"project_uid","project_uid");
    }
}
