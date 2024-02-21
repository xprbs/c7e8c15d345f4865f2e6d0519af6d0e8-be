<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, Validator, DB, Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;

use App\Helper\WebHelper;

use App\Models\CompanyModel;

class CompanyController extends Controller
{
    public function getCompany(Request $request)
    {

        $model = CompanyModel::orderBy('dataAreaName','ASC')
                            ->get();

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->dataAreaId;
            $data_array[$key]['label'] = $value->dataAreaName;
        }  
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }
}
