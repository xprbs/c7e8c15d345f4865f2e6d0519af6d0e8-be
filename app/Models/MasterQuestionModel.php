<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Auth;
use App\Models\OrganizationModels;

class MasterQuestionModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'master_question' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
    
        static::creating(function (MasterQuestionModel $item) {
            $item->question_uid = (string)Str::uuid() ; //assigning value          
            $item->created_by = Auth::user()->user_uid ;   
        });
    }

    public function dept() 
    {
        return $this->hasOne(OrganizationModels::class,"unit_code","question_dept");
    }
    
}
