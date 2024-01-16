<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

}
