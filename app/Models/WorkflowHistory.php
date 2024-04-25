<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Auth;
use App\Models\AuditChecklistModel;

class WorkflowHistory extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = [];

    public const STATUS = [
        "0" => "-",
        "1" => "On Process",
        "2" => "Approve",
        "3" => "Reject"
    ];

    public static function boot() {
        parent::boot();
        
        static::addGlobalScope('entity_uid', function (Builder $builder) {
            $builder->where('entity_uid', Auth::user()->entity_uid);
        });

        static::creating(function (WorkflowHistory $item) {
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity 
            $item->created_by = Auth::user()->user_uid ;            
        });
    }

    public function audit()
    {
        return $this->hasOne(AuditChecklistModel::class,'audit_uid','doc_uid');
    }
}
