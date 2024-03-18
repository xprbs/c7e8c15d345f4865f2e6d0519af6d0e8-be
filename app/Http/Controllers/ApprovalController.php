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

        $model = WorkflowHistory::where('doc_type','AUDIT_APPROVAL')
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

    public function AuditApprove(request $request)
    {
        $validator = Validator::make($request->all(),[
            "audit_uid" => "nullable",
            "note" => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

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

            $nextApproval = WorkflowHistory::where('doc_uid', $request->audit_uid)->where('priority', $nextPriority)->update([
                "approval" => 1,
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Successfully approve',
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
}
