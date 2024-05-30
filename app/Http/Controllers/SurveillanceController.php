<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, Validator, DB, Exception, Config;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

use App\Helper\WebHelper;

use App\Models\Workflow;
use App\Models\Surveillance;
use App\Models\SurveillanceDetail;
use App\Models\SurveillanceHistory;

class SurveillanceController extends Controller
{
    public function surveillanceList(Request $request)
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

        $model = Surveillance::where(function($query) use ($params) {
                            $query->orWhere('project_number', 'LIKE', '%'.$params.'%')
                                    ->orWhere('project_name', 'LIKE', '%'.$params.'%');
                        })
                        ->orderBy($sortColumn,$sortType)
                        ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']   = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']   = $value->id ;
            $data_master[$key]['entity_uid'] = $value->entity_uid;
            $data_master[$key]['project_uid'] = $value->project_uid;
            $data_master[$key]['dataAreaId'] = $value->dataAreaId;
            $data_master[$key]['project_location'] = $value->project_location;
            $data_master[$key]['project_number'] = $value->project_number;
            $data_master[$key]['project_name'] = $value->project_name;
            $data_master[$key]['project_category'] = $value->project_category;
            $data_master[$key]['project_date'] = $value->project_date;
            $data_master[$key]['due_date'] = $value->due_date;
            $data_master[$key]['finding'] = $value->finding;
            $data_master[$key]['recommendation'] = $value->recommendation;
            $data_master[$key]['risk'] = $value->risk;
            $data_master[$key]['is_she'] = $value->is_she;
            $data_master[$key]['status'] = Surveillance::STATUS[$value->status ?? 10];
            $data_master[$key]['status_code'] = $value->status ?? 10 ;
            $data_master[$key]['project_location']          = $value->dept['unit_description'] ?? null ;

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

    public function surveillanceStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "dataAreaId" => "required",
            "project_location" => "required",
            "project_number" => "nullable",
            "project_name" => "required",
            "project_category" => "nullable",
            "project_date" => "required",
            "due_date" => "nullable",
            "finding" => "required",
            "is_she" => "required",
            "recommendation" => "nullable",
            "risk" => "nullable",
            "details.*.image" => "nullable",
            "details.*.geo_location" => "nullable",
            "details.*.description" => "nullable",
            "details.*.comment01" => "nullable",
            "details.*.comment02" => "nullable"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();  

        try {
            
            $model = new Surveillance();
            $model->dataAreaId = $request->dataAreaId;
            $model->project_location = $request->project_location;
            $model->project_number = WebHelper::GENERATE_PROJECT_NUMBER();
            $model->project_name = $request->project_name;
            $model->project_category = $request->project_category;
            $model->project_date = Carbon::parse($request->project_date)->format('Y-m-d');
            $model->due_date = Carbon::parse($request->due_date)->format('Y-m-d');
            $model->finding = $request->finding;
            $model->is_she = $request->is_she == 1 ? 'SHE' : 'NON_SHE';
            $model->recommendation = $request->recommendation;
            $model->risk = $request->risk;
            $model->status = $request->is_she == 1 ? Surveillance::IS_CREATED : Surveillance::IS_COMPLETED;
            $model->save();

            foreach ($request->details as $key => $value) {

                $PATH = '/audit/surveillances/'.$model->project_uid.'/' ;
                
                $attchment = $value['image'];

                $file_name = time().'_'.$attchment->getClientOriginalName();
                $file_type = $attchment->getClientOriginalExtension();
                $file_path = '/storage'.$PATH.$file_name;
                Storage::putFileAs('/public'.$PATH,$attchment,$file_name);

                $detail = new SurveillanceDetail();
                $detail->dataAreaId = $request->dataAreaId;
                $detail->project_uid = $model->project_uid;
                $detail->image = $file_path;
                $detail->filename = $file_name;
                $detail->file_type = $file_type;
                $detail->geo_location = $value['geo_location'];
                $detail->description = $value['description'];
                $detail->comment01 = $value['comment01'];
                $detail->comment02 = $value['comment02'];
                $detail->save();
            }

            $workflow = Workflow::where('doc_type','PIC_DEPT')->where('key01', $request->project_location)->get();

            if($workflow){
                foreach ($workflow as $key => $value) {

                    $TITLE = "NOTIF_SURVEILLANCE";
                    $NO = $model->project_number;
                    $Description = "You have notification \nPlease check and follow up the findings \nFollowing detail below\n";
                    $enter = "\n";
                    $env = Config::get('app.name') ;
                    $baseUrl = URL::to('');
                    $MSG = $TITLE.$enter.$NO.$enter.$Description.$enter.$baseUrl.$enter.$env ;

                    $TO = $value->user->hp.'@c.us';

                    WebHelper::WA_HELPER('NOTIF_SURVEILLANCE', $TO, $MSG);

                }
            }

            $history = new SurveillanceHistory;
            $history->dataAreaId = $request->dataAreaId;
            $history->project_uid = $model->project_uid;
            $history->doc_type = Surveillance::IS_CREATED;
            $history->save();

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

    public function surveillanceDetail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "project_uid" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try{

            $model = Surveillance::where('project_uid', $request->project_uid)->get();

            $data_master = array() ;

            foreach ($model as $key => $value) {
                // dd($value);
                $data_master['dataAreaId']          = $value['dataAreaId'] ;
                $data_master['project_uid']          = $value['project_uid'] ;
                $data_master['project_location']          = $value->dept['unit_description'] ?? null ;
                $data_master['project_number']          = $value['project_number'] ;
                $data_master['project_name']          = $value['project_name'] ;
                $data_master['project_date']          = Carbon::parse($value['project_date'])->format('Y-m-d') ;
                $data_master['due_date']          = Carbon::parse($value['due_date'])->format('Y-m-d') ;
                $data_master['finding']          = $value['finding'] ;
                $data_master['recommendation']          = $value['recommendation'] ;
                $data_master['risk']                = $value['risk'] ;
                $data_master['is_she']                = $value['is_she'] ;
                $data_master['detail']                = $value->detail ;

            }

            DB::commit();  
            return response()->json([
                'code' => 200,
                'message' => 'Successfully created data',
                'data' => $data_master
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

    public function surveillanceHistory(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "dataAreaId" => "required",
            "project_uid" => "required",
            "note" => "nullable",
            "file" => "nullable",
            "doc_type" => "required" //20 Follow // 30 Closed 
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();  

        try {

            if($request->has('file')){

                $PATH = '/audit/surveillances/'.$request->project_uid.'/'.$request->doc_type.'/' ;
                
                $attchment = $request->file;

                $file_name = time().'_'.$attchment->getClientOriginalName();
                $file_type = $attchment->getClientOriginalExtension();
                $file_path = '/storage'.$PATH.$file_name;
                Storage::putFileAs('/public'.$PATH,$attchment,$file_name);
            }
            

            $model = new SurveillanceHistory;
            $model->dataAreaId = $request->dataAreaId;
            $model->project_uid = $request->project_uid;
            $model->doc_type = $request->doc_type;
            $model->note = $request->note;
            $model->path = $file_path ?? null;
            $model->filename = $file_name ?? null;
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

    public function surveillanceHistoryGet(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "project_uid" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $model = SurveillanceHistory::where('project_uid', $request->project_uid)->orderBy('id','ASC')->get();

        $data_master = [] ;

        foreach ($model as $key => $value) {
            $data_master[$key]['project_uid'] = $value->project_uid;
            $data_master[$key]['doc_type'] = Surveillance::STATUS[$value->doc_type];
            $data_master[$key]['note'] = $value->note;
            $data_master[$key]['path'] = $value->path;
        }

        return response()->json([
            'code' => 200,
            'message' => 'Successfully created data',
            'data' => $data_master
        ], 200);

    }
}
