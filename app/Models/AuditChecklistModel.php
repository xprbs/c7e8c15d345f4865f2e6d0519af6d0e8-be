<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\OrganizationModels;

class AuditChecklistModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_checklist' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
    
        static::creating(function (AuditChecklistModel $item) {
            $item->audit_uid = (string)Str::uuid() ; //assigning value            
        });
    }

    public function dept() 
    {
        return $this->hasOne(OrganizationModels::class,"unit_code","audit_location");
    }

}
