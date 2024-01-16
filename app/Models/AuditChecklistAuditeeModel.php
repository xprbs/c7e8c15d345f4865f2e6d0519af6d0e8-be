<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditChecklistAuditeeModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'audit_checklist_auditee' ;
    protected $guarded = [];
}
