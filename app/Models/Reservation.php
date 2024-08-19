<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $connection = 'cotte';
    protected $table = 'mreservation';
}
