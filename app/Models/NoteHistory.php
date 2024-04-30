<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Auth;
use App\Models\User;

class NoteHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'note_histories' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();

        static::creating(function (NoteHistory $item) {
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity        
            $item->created_by = Auth::user()->user_uid ;       
        });

    }

    public function user()
    {
        return $this->hasOne(User::class,'user_uid','created_by');
    }


}
