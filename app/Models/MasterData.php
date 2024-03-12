<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MasterData extends Model
{
    use HasFactory;

    public function auditor()
    {
        return $this->hasOne(User::class,'user_uid','key1');
    }

    public function auditee()
    {
        return $this->hasOne(User::class,'user_uid','key1');
    }
}
