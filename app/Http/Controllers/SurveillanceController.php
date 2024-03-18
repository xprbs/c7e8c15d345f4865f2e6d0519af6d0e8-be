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

use App\Models\Surveillance;
use App\Models\SurveillanceDetail;

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
            $model->recommendation = $request->recommendation;
            $model->risk = $request->risk;
            $model->save();

            foreach ($request->details as $key => $value) {

                $detail = new SurveillanceDetail();
                $detail->dataAreaId = $request->dataAreaId;
                $detail->project_uid = $model->project_uid;
                // $detail->image = $value['image'];
                $detail->geo_location = $value['geo_location'];
                $detail->description = $value['description'];
                $detail->comment01 = $value['comment01'];
                $detail->comment02 = $value['comment02'];
                $detail->save();
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
