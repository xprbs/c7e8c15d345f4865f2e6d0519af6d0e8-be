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
use App\Models\AuditChecklistModel;
use App\Models\OrganizationModels;
use App\Models\MasterQuestionModel;

class AuditChecklistController extends Controller
{
    public function auditChecklist(Request $request)
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

        $model = AuditChecklistModel::orderBy($sortColumn,$sortType)
                                ->orWhere('audit_name', 'LIKE', '%'.$params.'%')
                                ->orWhere('audit_ref', 'LIKE', '%'.$params.'%')
                                ->orWhere('audit_date', 'LIKE', '%'.$params.'%')
                                ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['dataAreaId']          = $value->dataAreaId ;
            $data_master[$key]['audit_category']          = $value->audit_category ;
            $data_master[$key]['audit_ref']          = $value->audit_ref ;
            $data_master[$key]['audit_number']          = $value->audit_number ;
            $data_master[$key]['audit_name']          = $value->audit_name ;
            $data_master[$key]['audit_location']          = $value->audit_location ;
            $data_master[$key]['question_uid']          = $value->question_uid ;
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


    public function auditChecklistStore(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'audit_name' => 'required',
            'audit_ref' => 'required',
            'audit_category' => 'required',
            'audit_location' => 'required',
            'question_uid' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        DB::beginTransaction();

        try {

            $model = new AuditChecklistModel;
            $model->audit_name = $request->audit_name;
            $model->audit_ref = $request->audit_ref;
            $model->audit_category = $request->audit_category;
            $model->audit_location = $request->audit_location;
            $model->question_uid = $request->question_uid;
            $model->audit_number = WebHelper::GENERATE_AUDIT_NUMBER();
            $model->save();

            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'Successfully create data',
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();      
            $error = [
                'code' => 500,
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);
        }
    }

    public function getDept(){

        $model = OrganizationModels::where('unit_level','DEPT')
                            ->orderBy('unit_description','ASC')
                            ->get();

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->unit_code;
            $data_array[$key]['label'] = $value->unit_description;
        }                    
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

    public function getQuestion(){

        $model = MasterQuestionModel::orderBy('id','DESC')
                            ->get();

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->question_uid;
            $data_array[$key]['label'] = $value->question_name;
        }                    
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

    public function questionTemplateList(Request $request)
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

        $model = MasterQuestionModel::orderBy($sortColumn,$sortType)
                                ->orWhere('question_name', 'LIKE', '%'.$params.'%')
                                ->orWhere('question_type', 'LIKE', '%'.$params.'%')
                                ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['dataAreaId']          = $value->dataAreaId ;
            $data_master[$key]['question_name']          = $value->question_name ;
            $data_master[$key]['question_type']          = $value->question_type ;
            $data_master[$key]['question_number']          = $value->question_number ;
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
}
