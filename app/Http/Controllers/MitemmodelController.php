<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Mitemmodel;
use App\Models\Mproductgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MitemmodelController extends Controller
{
    public function index()
    {
        $data = DB::connection('cotte_apps')->table('mitemmodel')->get();
        $mproduct = DB::connection('cotte_apps')->table('mproductgroup')->get();
        return response()->json([
            'code' => 200,
            'data' => [
                'mitem' => $data,
                'mproduct' => $mproduct
            ]
        ]);
    }
}
