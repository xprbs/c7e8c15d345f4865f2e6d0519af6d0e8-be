<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Auth;

class JobNotification extends Model
{
    use SoftDeletes;
    use HasFactory;

    public static function boot() {
        parent::boot();
        
        static::addGlobalScope('entity_uid', function (Builder $builder) {
            $builder->where('entity_uid', Auth::user()->entity_uid);
        });

        static::creating(function (WorkflowHistory $item) {
            $item->doc_uid = (string)Str::uuid() ; //assigning value    
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity 
            $item->created_by = Auth::user()->user_uid ;            
        });
    }
}
