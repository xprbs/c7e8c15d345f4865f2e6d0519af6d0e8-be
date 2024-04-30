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

use App\Models\WorkflowHistory;
use App\Models\NoteHistory;
use App\Models\AuditChecklistModel;

class ApprovalController extends Controller
{
    public function AuditApprovalList(Request $request)
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

        $model = WorkflowHistory::whereRelation('audit','status', AuditChecklistModel::IS_WAITING_APPROVAL)->where('doc_type','AUDIT_APPROVAL')
                ->where('user_uid', Auth::user()->user_uid )
                ->where('approval','1')
                ->orderBy($sortColumn,$sortType)
                ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['dataAreaId']          = $value->audit->dataAreaId ;
            $data_master[$key]['audit_uid']          = $value->audit->audit_uid ;
            $data_master[$key]['audit_category']          = $value->audit->audit_category ;
            $data_master[$key]['audit_ref']          = $value->audit->audit_ref ;
            $data_master[$key]['audit_number']          = $value->audit->audit_number ;
            $data_master[$key]['audit_name']          = $value->audit->audit_name ;
            $data_master[$key]['audit_location']          = $value->audit->dept['unit_description'] ;
            $data_master[$key]['status']          = $value->audit->status ?? 0;
            $data_master[$key]['status_name']          = AuditChecklistModel::STATUS[$value->audit->status ?? 0];
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

    public function AuditApprove(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_uid" => "nullable",
            "note" => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            
            $approval = WorkflowHistory::where('doc_uid', $request->audit_uid)
                                ->where('user_uid', Auth::user()->user_uid)
                                ->where('approval', 1)->first();

            $nextPriority = $approval->priority + 1;

            $approval->update([
                "action_date" => Carbon::now(),
                "approval" => 2,
                "command" => $request->note
            ]);

            $nextApproval = WorkflowHistory::where('doc_uid', $request->audit_uid)->where('priority', $nextPriority)->first();
            
            if ($nextApproval) {

                $nextApproval->update([
                    "approval" => 1,
                ]);

            }else{
                
                AuditChecklistModel::where('audit_uid', $request->audit_uid)->update([
                    "status" => 30
                ]);

            }
            
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully approve',
            ], 200);

        } catch (Exception $e) {
            
            DB::rollback();

            $error = [
                'code' => 500,
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);
        }
    }

    public function AuditReject(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_uid" => "nullable",
            "note" => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            
            $approval = WorkflowHistory::where('doc_uid', $request->audit_uid)
                                ->where('user_uid', Auth::user()->user_uid)
                                ->where('approval', 1)->first();


            $approval->update([
                "action_date" => Carbon::now(),
                "approval" => 3,
                "command" => $request->note
            ]);

            AuditChecklistModel::where('audit_uid', $request->audit_uid)->update([
                "status" => 40
            ]);

            
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully reject',
            ], 200);

        } catch (Exception $e) {
            
            DB::rollback();

            $error = [
                'code' => 500,
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);
        }
    }

    public function approvalNoteStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_uid" => "required",
            "question_uid" => "required",
            "question_detail_uid" => "required",
            "note" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            
            $model = new NoteHistory;
            $model->audit_uid = $request->audit_uid;
            $model->question_uid = $request->question_uid;
            $model->question_detail_uid = $request->question_detail_uid;
            $model->note = $request->note;
            $model->save() ;

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully created data',
            ], 200);

        } catch (Exception $e) {
            
            DB::rollback();

            $error = [
                'code' => 500,
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);
        }
    }

    public function approvalNoteGet(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_uid" => "required",
            "question_uid" => "required",
            "question_detail_uid" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            
            $model = NoteHistory::where('audit_uid', $request->audit_uid)
                                ->where('question_uid', $request->question_uid)
                                ->where('question_detail_uid', $request->question_detail_uid)
                                ->orderBy('id','DESC')
                                ->get();

            $data_array = [] ;

            foreach ($model as $key => $value) {
                $data_array[$key]['created_at']             = Carbon::parse($value->created_at)->format('d-m-Y H:i:s') ;
                $data_array[$key]['dataAreaId']             = $value->dataAreaId ;
                $data_array[$key]['audit_uid']              = $value->audit_uid ;
                $data_array[$key]['question_uid']           = $value->question_uid ;
                $data_array[$key]['question_detail_uid']    = $value->question_detail_uid ;
                $data_array[$key]['note']                   = $value->note ;
                $data_array[$key]['created_by']             = $value->user->name ;
            }

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully get data',
                'data' => $data_array
            ], 200);

        } catch (Exception $e) {
            
            DB::rollback();

            $error = [
                'code' => 500,
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);
        }
    }
}
