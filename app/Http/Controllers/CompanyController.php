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

use App\Models\CompanyModel;
use App\Models\OrganizationModels;
use App\Models\WEB\UserHasCompanyModel;

class CompanyController extends Controller
{
    public function companyList(Request $request)
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
        

        $model = CompanyModel::orderBy($sortColumn,$sortType)
                                ->orWhere('dataAreaId', 'LIKE', '%'.$params.'%')
                                ->orWhere('dataAreaName', 'LIKE', '%'.$params.'%')
                                ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['dataAreaId']          = $value->dataAreaId ;
            $data_master[$key]['dataAreaName']          = $value->dataAreaName ;
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

    public function companyStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dataAreaId' => 'required',
            'dataAreaName' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        DB::beginTransaction();

        try {

            $model = new CompanyModel;
            $model->dataAreaId = $request->dataAreaId;
            $model->dataAreaName = $request->dataAreaName;
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

    public function companyDelete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "row_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        DB::beginTransaction();

        try {
            
            CompanyModel::where('id', $request->row_id)->delete();

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully delete data',
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
    
    public function getCompany(Request $request)
    {

        $model = CompanyModel::orderBy('dataAreaName','ASC')
                            ->get();

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->dataAreaId;
            $data_array[$key]['label'] = $value->dataAreaName;
            $data_array[$key]['company_uid'] = $value->company_uid;
        }  
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }
    
    public function getCompanyTrans(Request $request)
    {
        
        $company = UserHasCompanyModel::where('user_uid', Auth::user()->user_uid)->get();

        $data_array = [];

        foreach ($company as $key => $value) {
            $data_array[$key]['id'] = $value->company->dataAreaId;
            $data_array[$key]['label'] = $value->company->dataAreaName;
            $data_array[$key]['company_uid'] = $value->company->company_uid;
        }  
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }
}
