<?php

namespace App\Models\WEB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MenusModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'menus' ;
    protected $guarded = [];

    const MENU_TYPE = [
        "sectionTitle" => "MODULE",
        "Pages" => "MENU",
    ];

    public static function boot() {
        parent::boot();
    
        static::creating(function (MenusModel $item) {
            $item->menus_uid = (string)Str::uuid() ; //assigning value            
        });
    }
}
