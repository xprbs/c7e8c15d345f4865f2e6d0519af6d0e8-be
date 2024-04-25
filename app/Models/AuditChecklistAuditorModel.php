<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Auth;

class AuditChecklistAuditorModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_checklist_auditor' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        
        static::addGlobalScope('entity_uid', function (Builder $builder) {
            $builder->where('entity_uid', Auth::user()->entity_uid);
        });

        static::creating(function (AuditChecklistAuditorModel $item) {
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity       
            $item->created_by = Auth::user()->user_uid ;     
        });
    }
}
