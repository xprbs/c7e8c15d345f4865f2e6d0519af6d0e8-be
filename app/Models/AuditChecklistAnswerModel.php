<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AuditChecklistAnswerModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_checklist_answer' ;
    protected $guarded = [];
}
