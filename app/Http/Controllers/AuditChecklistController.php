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
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

use App\Helper\WebHelper;
use App\Models\AuditChecklistModel;
use App\Models\AuditChecklistAnswerModel;
use App\Models\AuditChecklistAuditorModel;
use App\Models\AuditChecklistAuditeeModel;
use App\Models\WorkflowHistory;
use App\Models\Workflow;
use App\Models\AuditChecklistFile;

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
            $data_master[$key]['audit_category']          = $value->question->question_type ;
            $data_master[$key]['audit_ref']          = $value->question->question_ref ;
            $data_master[$key]['audit_number']          = $value->audit_number ;
            $data_master[$key]['audit_name']          = $value->audit_name ;
            $data_master[$key]['audit_location']          = $value->dept['unit_description'] ;
            $data_master[$key]['question_uid']          = $value->question->question_name ;
            $data_master[$key]['status']          = $value->status ?? 0;
            $data_master[$key]['status_name']          = AuditChecklistModel::STATUS[$value->status ?? 0];
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
            'audit_ref' => 'nullable',
            'audit_category' => 'nullable',
            'audit_location' => 'required',
            'question_uid' => 'required',
            'company' => 'required',
            'auditor.*.auditor_uid' => "required",
            'auditor.*.auditor_name' => "required",
            'auditor.*.auditor_type' => "nullable",
            'auditee.*.auditee_uid' => "required",
            'auditee.*.auditee_name' => "required",
            'auditee.*.auditee_type' => "nullable",
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
            $model->status = 0 ;
            $model->audit_number = WebHelper::GENERATE_AUDIT_NUMBER();
            $model->save();

            foreach ($request->auditor as $key => $value) {

                AuditChecklistAuditorModel::create(
                        [
                            "dataAreaId" => $request->company,
                            "audit_uid" => $model->audit_uid,
                            "auditor_uid" => $value['auditor_uid'],
                            "auditor_name" => $value['auditor_name'],
                            "auditor_type" => $value['auditor_type'],
                        ]
                    );
            }

            foreach ($request->auditee as $key => $value) {

                AuditChecklistAuditeeModel::create(
                        [
                            "dataAreaId" => $request->company,
                            "audit_uid" => $model->audit_uid,
                            "auditee_uid" => $value['auditee_uid'],
                            "auditee_name" => $value['auditee_name'],
                            "auditee_type" => $value['auditee_type'],
                        ]
                    );
            }

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
                $data_master['auditor']          = $value->auditor ;
                $data_master['auditee']          = $value->auditee ;
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
            "is_submit" => "required", // 1 submit // 0 Draft
            "details.*.id" => "required",
            "details.*.answer" => "nullable",
            "details.*.answer_description" => "nullable",
            'details.*.file_uploads.*.files' => "mimes:doc,docx,xls,xlsx,ppt,pptx,pdf"
        ]);

        // $attchment = $request->file('file_uploads');

        // $file_name = time().'_'.$attchment->getClientOriginalName();
        // $file_type = $attchment->getClientOriginalExtension();
        // $file_path = '/storage/audit/attactment-process/'.$file_name;
        
        // // $attchment->move(storage() . '/audit/attactment-process/', $file_name);
        // Storage::putFileAs('/public/audit/attactment-process/',$attchment,$file_name);

        // $fileUpload = new AuditChecklistFile ;
        // $fileUpload->audit_uid = $request->audit_uid ;
        // $fileUpload->question_uid = $request->question_uid ;
        // // $fileUpload->question_detail_uid = $value['id'] ;
        // $fileUpload->filename = $file_name ;
        // $fileUpload->filepath = $file_path ;
        // $fileUpload->filetype = $file_type ;
        // // $fileUpload->filesize = $valueFile->filesize ;
        // $fileUpload->save();

        // return response()->json([
        //     'code' => 200,
        //     'message' => 'Successfully created data',
        // ], 200);

        // dd($request->all());
        // dd(is_array($request->details[0]['file_uploads'] ?? null));
        // dd($request->hasFile("details[0]['file_uploads[0]']"));

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();  
        try {
            
            AuditChecklistModel::where('audit_uid', $request->audit_uid)->update([
                "status" => $request->is_submit == 1 ? AuditChecklistModel::IS_WAITING_APPROVAL : AuditChecklistModel::IS_DRAFT 
            ]);

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

                // dd($value['file_uploads']);

                if(is_array($request->details[$key]['file_uploads'] ?? null)){

                    foreach ($value['file_uploads'] as $keyFile => $valueFile) {
                        // dd($valueFile);

                        $file_name = time().'_'.$valueFile[$keyFile]['files']->getClientOriginalName();
                        $file_type = $valueFile[$keyFile]['files']->getClientOriginalExtension();
                        $file_path = '/storage/audit/'.$file_name;
                        
                        Storage::disk('audit_file')->put($file_name,$valueFile[$keyFile]['files']);

                        $fileUpload = new AuditChecklistFile ;
                        $fileUpload->audit_uid = $request->audit_uid ;
                        $fileUpload->question_uid = $request->question_uid ;
                        $fileUpload->question_detail_uid = $value['id'] ;
                        $fileUpload->filename = $file_name ;
                        $fileUpload->filepath = $file_path ;
                        $fileUpload->filetype = $file_type ;
                        // $fileUpload->filesize = $valueFile->filesize ;
                        $fileUpload->save();

                    }
                }
               
            }            

            if ($request->is_submit == 1) {
                
                $model = Workflow::where('doc_type','AUDIT_APPROVAL')->get();  
                
                foreach ($model as $key2 => $value2) {
                    
                    $approval = new WorkflowHistory();
                    $approval->doc_type = 'AUDIT_APPROVAL';
                    $approval->doc_uid = $request->audit_uid;
                    $approval->user_uid = $value2->user_uid;
                    $approval->user_name = $value2->user->name;
                    $approval->priority = $value2->priority;
                    $approval->approval = $value2->priority == 1 ? 1 : 0;
                    $approval->save();

                }
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

    public function getAuditApproval(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_uid" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $model = WorkflowHistory::where('doc_uid', $request->audit_uid)->where('doc_type','AUDIT_APPROVAL')->orderBy('priority','ASC')->get();    

        $data_array = [];

        foreach ($model as $key => $value) {
            
            $data_array[$key]['user_name'] = $value->user_name;
            $data_array[$key]['priority'] = $value->priority;
            $data_array[$key]['approval'] = $value->approval;
            $data_array[$key]['approval_name'] = WorkflowHistory::STATUS[$value->approval];
            $data_array[$key]['action_date'] = $value->action_date == null ? "-" : Carbon::parse($value->action_date)->format('d-M-Y H:i:s');
            $data_array[$key]['message'] = $value->command ?? "-";
        } 

        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }
}
