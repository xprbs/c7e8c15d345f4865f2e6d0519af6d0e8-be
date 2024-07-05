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

class AuditCategoryRefController extends Controller
{
    public function auditCategoryRefStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_category_ref" => "required",
            "audit_category" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            $model = new MasterData;
            $model->key1 = $request->audit_category_ref;
            $model->value1 = $request->audit_category_ref;
            $model->key2 = $request->audit_category;
            $model->doc_type = 'AUDIT_CATEGORY_REF';
            $model->save();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully created data',
            ], 200);

        } catch (Exception $e) {


            $error = [
                'request' => json_decode($e),
                'response' => json_decode($e)
            ];

            return response()->json($error, 500);
        }

    }

    public function auditCategoryRefList(Request $request)
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

        $model = MasterData::orderBy($sortColumn,$sortType)
                            ->where('doc_type','AUDIT_CATEGORY_REF')
                            ->Where('value1', 'LIKE', '%'.$params.'%')
                            ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['key1']          = $value->key1 ;
            $data_master[$key]['value1']          = $value->value1 ;
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

    public function auditCategoryRefDelete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "row_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            MasterData::where('id', $request->row_id)->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully created data',
            ], 200);

        } catch (Exception $e) {


            $error = [
                'request' => json_decode($e),
                'response' => json_decode($e)
            ];

            return response()->json($error, 500);
        }

    }
    
    public function getAuditCategory(){

        $model = MasterData::where('doc_type','AUDIT_CATEGORY')
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

    public function getAuditCategoryRef(Request $request){

        $model = MasterData::where('doc_type','AUDIT_CATEGORY_REF')
                            ->where('key2', $request->key2 )
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
}
