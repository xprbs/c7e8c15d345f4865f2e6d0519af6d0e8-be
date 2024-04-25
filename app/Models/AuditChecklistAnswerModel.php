<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Auth;

class AuditChecklistAnswerModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_checklist_answer' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();

        static::creating(function (AuditChecklistAnswerModel $item) {
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity        
            $item->created_by = Auth::user()->user_uid ;       
        });

    }
}
