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

use App\Models\MasterQuestionModel;
use App\Models\MasterQuestionDetailModel;
use App\Models\MasterAnswerModel;

class QuestionTemplateController extends Controller
{
    
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
            $data_master[$key]['question_number']          = $value->question_number ;
            $data_master[$key]['question_name']          = $value->question_name ;
            $data_master[$key]['question_dept']          = $value->dept['unit_description'] ?? null ;
            $data_master[$key]['question_type']          = $value->question_type ;
            $data_master[$key]['question_ref']          = $value->question_ref ;
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
            'question_dept' => 'required',
            'question_type' => 'required',
            'question_ref' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        DB::beginTransaction();

        try {

            $model = new MasterQuestionModel;
            $model->question_name = $request->question_name;
            $model->question_dept = $request->question_dept;
            $model->question_type = $request->question_type;
            $model->question_ref = $request->question_ref;
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
}
