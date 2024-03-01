<?php

namespace App\Models\WEB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use App\Models\CompanyModel;

class UserHasCompanyModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_has_company' ;
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        
        static::addGlobalScope('entity_uid', function (Builder $builder) {
            $builder->where('entity_uid', Auth::user()->entity_uid);
        });
    
        static::creating(function (UserHasCompanyModel $item) {
            $item->entity_uid = Auth::user()->entity_uid ; //assigning entity            
        });
    }

    public function company()
    {
        return $this->hasOne(CompanyModel::class,'company_uid','company_uid');
    }
}
