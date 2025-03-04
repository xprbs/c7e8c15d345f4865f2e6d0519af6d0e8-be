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
use Auth;

class AuditChecklistModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_checklist' ;
    protected $guarded = [];

    public const IS_WAITING_APPROVAL = 20 ;
    public const IS_DRAFT = 10 ;

    public const STATUS = [ 
        0 => 'Created', 
        10 => 'Drafted', 
        20 => 'Waiting Approval', 
        30 => 'Completed' ,
        40 => 'Rejected' 
    ];

    public static function boot() {
        parent::boot();
    
        static::creating(function (AuditChecklistModel $item) {
            $item->audit_uid = (string)Str::uuid() ; //assigning value     
            $item->created_by = Auth::user()->user_uid ;       
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
