<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterAnswerModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'master_answer' ;
    protected $guarded = [];
    
}
