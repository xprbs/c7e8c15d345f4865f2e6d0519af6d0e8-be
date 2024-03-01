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
use App\Models\AuditChecklistAnswerModel;

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
            $data_master[$key]['audit_uid']          = $value->audit_uid ;
            $data_master[$key]['audit_category']          = $value->audit_category ;
            $data_master[$key]['audit_ref']          = $value->audit_ref ;
            $data_master[$key]['audit_number']          = $value->audit_number ;
            $data_master[$key]['audit_name']          = $value->audit_name ;
            $data_master[$key]['audit_location']          = $value->dept['unit_description'] ;
            $data_master[$key]['question_uid']          = $value->question->question_name ;
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
            'company' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        DB::beginTransaction();

        try {

            $model = new AuditChecklistModel;
            $model->dataAreaId = $request->company;
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

    public function auditChecklistGetDetail(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        try {

            $model = AuditChecklistModel::where('audit_uid', $request->id)->get();

            $data_master = array() ;

            foreach ($model as $key => $value) {
                // dd($value);
                $data_master['dataAreaId']          = $value->dataAreaId ;
                $data_master['audit_uid']          = $value->audit_uid ;
                $data_master['audit_category']          = $value->audit_category ;
                $data_master['audit_ref']          = $value->audit_ref ;
                $data_master['audit_number']          = $value->audit_number ;
                $data_master['audit_name']          = $value->audit_name ;
                $data_master['company']          = $value->company['dataAreaName'] ?? null ;
                $data_master['audit_location']          = $value->dept['unit_description'] ;
                $data_master['question_uid']          = $value->question_uid ;
                $data_master['question_name']          = $value->question->question_name ;
            }
        
            
            return response()->json([
                'code' => 200,
                'message' => 'Successfully data data',
                'data' => $data_master
            ], 200);

        } catch (Exception $e) {

            $error = [
                'code' => 500,
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);
        }
    }

    public function AuditChecklistAnswerStore(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            "dataAreaId" => "nullable",
            "audit_uid" => "required",
            "question_uid" => "required",
            "details.*.id" => "required",
            "details.*.answer" => "nullable",
            "details.*.answer_description" => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();  
        try {
            
            foreach ($request->details as $key => $value) {

                AuditChecklistAnswerModel::updateOrCreate([
                    'dataAreaId'          => $request->dataAreaId ?? null,
                    'audit_uid'           => $request->audit_uid,
                    'question_uid'        => $request->question_uid,
                    'question_detail_uid' => $value['id'],
                ],[ 
                    'answer'              => $value['answer'],
                    'answer_description'  => $value['answer_description'],
                ]);
               
            }            

            DB::commit();  
            return response()->json([
                'code' => 200,
                'message' => 'Successfully created data',
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

    public function getAuditChecklistAnswer(Request $request)
    {

        $validator = Validator::make($request->all(),[
            "audit_uid" => "required",
            "question_uid" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $model = AuditChecklistAnswerModel::where('audit_uid', $request->audit_uid)->where('question_uid', $request->question_uid)->get();          
        // dd($model->count());
        $data_array = [];

        foreach ($model as $key => $value) {
            
            $data_array[$key]['audit_uid'] = $value->audit_uid;
            $data_array[$key]['question_uid'] = $value->question_uid;
            $data_array[$key]['question_detail_uid'] = $value->question_detail_uid;
            $data_array[$key]['answer'] = $value->answer ;
            $data_array[$key]['answer_description'] = $value->answer_description ;
        } 

        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }
}
