<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function getReservations($groupid)
    {
        $reservations = DB::table('mreservation')
                          ->where('groupid', $groupid)
                          ->get();
                          
        return response()->json(['data' => $reservations]);
    }
}
