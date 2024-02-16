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
use App\Models\MasterQuestionDetailModel;
use App\Models\MasterData;
use App\Models\MasterAnswerModel;
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

    public function questionTemplateStore(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'question_name' => 'required',
            'question_type' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        DB::beginTransaction();

        try {

            $model = new MasterQuestionModel;
            $model->question_name = $request->question_name;
            $model->question_type = $request->question_type;
            $model->question_number = WebHelper::GENERATE_QT_NUMBER();
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

    public function questionGetDetail(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }


        try {

            $model = MasterQuestionModel::where('question_uid', $request->id)->first();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully data data',
                'data' => $model
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

    public function auditCategoryStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_category" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            $model = new MasterData;
            $model->key1 = $request->audit_category;
            $model->value1 = $request->audit_category;
            $model->doc_type = 'AUDIT_CATEGORY';
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

    public function auditCategoryList(Request $request)
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
                            ->where('doc_type','AUDIT_CATEGORY')
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

    public function auditCategoryDelete(Request $request)
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

    public function questionDetailStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "question_uid" => "required",
            "question_answer_description" => "required",
            "question_answer_uid" => "required",
            "control_point" => "required",
            "klausul" => "required",
            "question_category1" => "required",
            "question_category2" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();  
        try {
            
            $model = new MasterQuestionDetailModel;
            $model->question_uid     = $request->question_uid;
            $model->question_answer_description     = $request->question_answer_description;
            $model->question_answer_uid             = $request->question_answer_uid;
            $model->klausul                         = $request->klausul;
            $model->control_point                   = $request->control_point;
            $model->question_category1              = $request->question_category1;
            $model->question_category2              = $request->question_category2;
            $model->save();

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

    public function getMasterAnswer(Request $request)
    {

        $model = MasterAnswerModel::select('question_answer_uid','question_answer_category')->groupBy('question_answer_uid','question_answer_category')->get();

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->question_answer_uid;
            $data_array[$key]['label'] = $value->question_answer_category;
        }                    
        
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

    public function getMasterAnswerId(Request $request)
    {

        $model = MasterAnswerModel::where('question_answer_uid', $request->id)->orderBy('order','ASC')->get();               
        
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $model,
        ];

        return response()->json($success, 200);
    }

    public function getQuestionDetailList(Request $request)
    {

        $validator = Validator::make($request->all(),[
            "question_uid" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $model = MasterQuestionDetailModel::where('question_uid', $request->question_uid)->get();          
        // dd($model->count());
        $data_array = [];

        foreach ($model as $key => $value) {
            
            $data_array[$key]['id'] = $value->question_answer_uid;
            $data_array[$key]['question_detail_uid'] = $value->question_detail_uid;
            $data_array[$key]['question_answer_description'] = $value->question_answer_description;
            $data_array[$key]['question_answer_category'] = $value->answerName->question_answer_category ?? null ;
            $data_array[$key]['answer'] = $value->answer ?? null ;
            $data_array[$key]['klausul'] = $value->klausul ?? '-' ;
            $data_array[$key]['question_category1'] = $value->question_category1 ?? '-' ;
            $data_array[$key]['question_category2'] = $value->question_category2 ?? '-' ;
            $data_array[$key]['control_point'] = $value->control_point ?? null ;
        } 

        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

    public function AuditChecklistAnswerStore(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            "dataAreaId" => "nullable",
            "audit_uid" => "required",
            "question_uid" => "required",
            "details.*.id" => "required",
            "details.*.answer" => "required",
            "details.*.answer_description" => "required",
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

}
