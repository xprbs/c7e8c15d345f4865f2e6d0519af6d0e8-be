<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CompanyModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'company' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
    
        static::creating(function (AuditChecklistModel $item) {
            $item->company_uid = (string)Str::uuid() ; //assigning value            
        });
    }
}
