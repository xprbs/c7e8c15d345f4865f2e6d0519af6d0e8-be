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

use App\Models\MasterData;
use App\Models\User;

class AuditeeController extends Controller
{
    public function auditeeStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "auditee_name" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
        $existingData = MasterData::where('key1', $request->auditee_name)->where('doc_type', 'IS_AUDITEE')->first();

        if ($existingData) {
        return response()->json([
            'code' => 500,
            'message' => 'Data Already Exist',
        ], 500);
        } else {
            $model = new MasterData;
            $model->key1 = $request->auditee_name;
            $model->key2 = 1;
            $model->doc_type = 'IS_AUDITEE';
            $model->save();
        }

        return response()->json([
            'code' => 200,
            'message' => 'Successfully created or updated data',
        ], 200);

    } catch (Exception $e) {
        $error = [
            'error' => $e->getMessage()
        ];

        return response()->json($error, 500);
    }

    }

    public function auditeeList(Request $request)
    {
        $params = $request->filterData ;

        if($request->limit == null OR $request->limit == ""){
            $limit = 10 ;
        }else{
            $limit = $request->limit ;
        }

        if($request->sort == null OR $request->sort == ""){
            $sortColumn = 'id' ;
            $sortType = 'DESC' ;
        }else{
            $sort = $request->sort[0] ;
            $sortColumn = $sort['field'] ;
            $sortType = $sort['sort'] ;
        }
        
        $model = MasterData::orderBy($sortColumn, $sortType)
            ->where('doc_type', 'IS_AUDITEE')
            ->with('auditee')
            ->paginate($limit);

        $data_master = [];

        foreach ($model as $key => $value) {
            $data_master[$key]['id'] = ($model->currentPage() - 1) * $model->perPage() + $key + 1;
            $data_master[$key]['row_id'] = $value->id;
            $data_master[$key]['key1'] = $value->key1;
            $data_master[$key]['key2'] = $value->key2;
            $data_master[$key]['username'] = $value->auditor->username;
        }

        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_master,
            'firstItem' => $model->firstItem(),
            'lastItem' => $model->lastItem(),
            'perPage' => $model->perPage(),
            'lastPage' => $model->lastPage(),
            'total' => $model->total(),
            'previousPageUrl' => $model->previousPageUrl(),
            'currentPage' => $model->currentPage(),
            'nextPageUrl' => $model->nextPageUrl(),
        ];

        return response()->json($success, 200);
    }

    public function auditeeUpdate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "row_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            $existingData = MasterData::where('id', $request->row_id)->first();

        if ($existingData->key2 === "1") {
            $existingData->key2 = 0;
            $existingData->save();
        } else {
            $existingData->key2 = 1;
            $existingData->save();
        }

            return response()->json([
                'code' => 200,
                'message' => 'Successfully updated data',
            ], 200);

        } catch (Exception $e) {


            $error = [
                'request' => json_decode($e),
                'response' => json_decode($e)
            ];

            return response()->json($error, 500);
        }

    }

    public function auditeeCategory(){

    $model = MasterData::where('doc_type','AUDITEE_TYPE')
                        ->orderBy('value1','ASC')
                        ->get();

    $data_array = [];

    foreach ($model as $key => $value) {
        $data_array[$key]['id'] = $value->key1;
        $data_array[$key]['label'] = $value->value1;
    }                    
    
    $success = [
        'code' => 200,
        'message' => 'Successfully get data',
        'data' => $data_array,
    ];

    return response()->json($success, 200);
}

    public function auditeeUserList(){

    $model = User::orderBy('id','ASC')->get();

    $data_array = [];

    foreach ($model as $key => $value) {
        $data_array[$key]['id'] = $value->user_uid;
        $data_array[$key]['label'] = $value->username;
    }                    
    
    $success = [
        'code' => 200,
        'message' => 'Successfully get data',
        'data' => $data_array,
    ];

    return response()->json($success, 200);
}

}
