<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogItem extends Model
{
    protected $connection = 'cotte';
    protected $table = 'log_item';
    protected $fillable = [
        'id_item',
        'type',
        'message',
        'created_date',
        'created_by',
    ];
    public $timestamps = false;
}
