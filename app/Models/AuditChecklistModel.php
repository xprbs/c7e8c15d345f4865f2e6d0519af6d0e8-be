<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\OrganizationModels;
use App\Models\CompanyModel;
use App\Models\MasterQuestionModel;
use App\Models\AuditChecklistAuditeeModel;
use App\Models\AuditChecklistAuditorModel;

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
    
    public function company() 
    {
        return $this->hasOne(CompanyModel::class,"dataAreaId","dataAreaId");
    }

    public function question() 
    {
        return $this->hasOne(MasterQuestionModel::class,"question_uid","question_uid");
    }

    public function auditor() 
    {
        return $this->hasMany(AuditChecklistAuditorModel::class,"audit_uid","audit_uid");
    }

    public function auditee() 
    {
        return $this->hasMany(AuditChecklistAuditeeModel::class,"audit_uid","audit_uid");
    }

}
