<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditChecklistAuditorModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_checklist_auditor' ;
    protected $guarded = [];
}
