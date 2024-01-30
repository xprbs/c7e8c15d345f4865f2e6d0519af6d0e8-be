<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\MasterAnswerModel;

class MasterQuestionDetailModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'master_question_detail' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
    
        static::creating(function (MasterQuestionDetailModel $item) {
            $item->question_detail_uid = (string)Str::uuid() ; //assigning value            
        });
    }

    public function answer()
    {
        return $this->hasOne(MasterAnswerModel::class,'question_answer_uid','question_answer_uid');
    }

}
