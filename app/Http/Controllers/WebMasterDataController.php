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

use App\Models\WEB\RolesModel;
use App\Models\WEB\PermissionModel;
use App\Models\WEB\MenusModel;
use App\Models\WEB\UserHasRolesModel;
use App\Models\WEB\RoleHasPermissionsModel;
use App\Models\WEB\RoleHasMenusModel;
use App\Models\WEB\UserHasCompanyModel;
use App\Models\User;
use App\Models\MasterData;

class WebMasterDataController extends Controller
{
    public function roleStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "role_name" => "required|unique:roles",
            "role_group" => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            $model = new RolesModel;
            $model->dataAreaId = $request->dataAreaId;
            $model->role_name = $request->role_name;
            $model->role_group = $request->role_group;
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

    public function roleList(Request $request)
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

        $model = RolesModel::orderBy($sortColumn,$sortType)
                                ->orWhere('role_name', 'LIKE', '%'.$params.'%')
                                ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['dataAreaId']          = $value->dataAreaId ;
            $data_master[$key]['role_uid']          = $value->role_uid ;
            $data_master[$key]['role_group']          = $value->role_group ;
            $data_master[$key]['role_name']          = $value->role_name ;
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

    public function roleDelete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "row_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        DB::beginTransaction();

        try {
            
            RolesModel::where('id', $request->row_id)->delete();

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

    public function permissionStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "permissions_name" => "required|unique:permissions",
            "parent_id" => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            $model = new PermissionModel;
            $model->dataAreaId = $request->dataAreaId;
            $model->permissions_name = $request->permissions_name;
            $model->type = $request->parent_id ? null : "menu";
            $model->parent_id = $request->parent_id;
            $model->acl_action = 'manage';
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

    public function permissionList(Request $request)
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

        $model = PermissionModel::orderBy($sortColumn,$sortType)
                                ->orWhere('permissions_name', 'LIKE', '%'.$params.'%')
                                ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['dataAreaId']          = $value->dataAreaId ;
            $data_master[$key]['permissions_name']          = $value->permissions_name ;
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

    public function permissionParent(Request $request)
    {
        $model = PermissionModel::where('type','menu')->orderBy('permissions_name','ASC')->get();

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->permissions_uid;
            $data_array[$key]['label'] = $value->permissions_name;
        }

        $success = [
            "status" => 200,
            "message" => "successfully get data",
            "data" => $data_array
        ];

        return response()->json($success, 200);
    }

    public function permissionDelete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "row_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        DB::beginTransaction();

        try {
            
            PermissionModel::where('id', $request->row_id)->delete();

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

    public function menuList(Request $request)
    {
        $params = $request->filterData ;

        if($request->limit == null OR $request->limit == ""){
            $limit = 10 ;
        }else{
            $limit = $request->limit ;
        }

        if($request->sort == null OR $request->sort == ""){
            $sortColumn = 'order_by' ;
            $sortType = 'ASC' ;
        }else{
            $sort = $request->sort[0] ;
            $sortColumn = $sort['field'] ;
            $sortType = $sort['sort'] ;
        }

        $model = MenusModel::orderBy($sortColumn,$sortType)
                            ->orWhere('menus_name', 'LIKE', '%'.$params.'%')
                            ->orWhere('url', 'LIKE', '%'.$params.'%')
                            ->orWhere('menus_type', 'LIKE', '%'.$params.'%')
                            ->orWhere('level', 'LIKE', '%'.$params.'%')
                            ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $data_master[$key]['id']          = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']          = $value->id ;
            $data_master[$key]['dataAreaId']          = $value->dataAreaId ;
            $data_master[$key]['menus_uid']           = $value->menus_uid ;
            $data_master[$key]['menus_type']          = MenusModel::MENU_TYPE[$value->menus_type] ;
            $data_master[$key]['menus_name']          = $value->menus_name ;
            $data_master[$key]['url']          = $value->url ;
            $data_master[$key]['icon']          = $value->icon ;
            $data_master[$key]['level']          = $value->level ;
            $data_master[$key]['parent']          = $value->parent ;
            $data_master[$key]['order_by']          = $value->order_by ;
            $data_master[$key]['acl_action']          = $value->acl_action ;
            $data_master[$key]['acl_subject']          = $value->acl_subject ;
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

    public function menuStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "menus_name" => "required",
            "menus_type" => "required",
            "url" => "required",
            "icon" => "required",
            "level" => "required",
            "parent_id" => "required",
            "order_by" => "required",
            "acl_subject" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            $model = new MenusModel;
            $model->dataAreaId = $request->dataAreaId;
            $model->menus_name = $request->menus_name;
            $model->menus_type = $request->menus_type ?? null;
            $model->url = $request->url ?? null;
            $model->icon = $request->icon ?? null;
            $model->level = $request->level;
            $model->parent_id = $request->parent_id ?? null;
            $model->order_by = $request->order_by;
            $model->acl_action = $request->acl_action;
            $model->acl_subject = $request->acl_subject;
            $model->save();

            $model = new PermissionModel;
            $model->dataAreaId = $request->dataAreaId;
            $model->permissions_name = $request->acl_subject;
            $model->acl_action = 'manage';
            $model->type = 'menu';
            $model->save();

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully created data',
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();
            
            $error = [
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);
        }

    }

    public function menuDelete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "row_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            
            MenusModel::where('acl_subject', $request->row_id)->delete();
            PermissionModel::where('permissions_name', $request->row_id)->delete();

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

    public function menuLevel1(Request $request)
    {

        $model = MenusModel::select('menus_uid','menus_name')
                            ->where('menus_type','Pages')
                            ->where('level', '1')
                            ->orderBy('order_by','ASC')
                            ->get();

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->menus_uid;
            $data_array[$key]['label'] = $value->menus_name;
        }                    
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

    public function menuLevelParent(Request $request)
    {

        $validator = Validator::make($request->all(),[
            "level" => "nullable",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if($request->level == 1){

            $model = MenusModel::select('menus_uid','menus_name')
                        ->where('menus_type','sectionTitle')
                        ->where('level', '1')
                        ->orderBy('order_by','ASC')
                        ->get();

        }else{

            $model = MenusModel::select('menus_uid','menus_name')
                        ->where('menus_type','Pages')
                        ->where('level', '1')
                        ->orderBy('order_by','ASC')
                        ->get();
        }

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->menus_uid;
            $data_array[$key]['label'] = $value->menus_name;
        }                    
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

    public function userList(Request $request)
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

        $model = User::where('entity_uid', Auth::user()->entity_uid)
                        ->where(function($query) use ($params) {
                            $query->orWhere('name', 'LIKE', '%'.$params.'%')
                                    ->orWhere('username', 'LIKE', '%'.$params.'%')
                                    ->orWhere('email', 'LIKE', '%'.$params.'%');
                        })
                        ->orderBy($sortColumn,$sortType)
                        ->paginate($limit);

        $data_master = [] ;

        foreach ($model as $key => $value) {
            // dd($value);
            $UserHasRolesModel = UserHasRolesModel::where('user_uid', $value->user_uid)->first();

            $data_master[$key]['id']        = ($model->currentPage()-1) * $model->perPage() + $key + 1 ;
            $data_master[$key]['row_id']    = $value->id ;
            $data_master[$key]['name']      = $value->name ;
            $data_master[$key]['username']  = $value->username ;
            $data_master[$key]['email']     = $value->email ;
            $data_master[$key]['user_uid']     = $value->user_uid ;
            $data_master[$key]['role_name'] = $UserHasRolesModel->roleName->role_name ?? null;
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

    public function userDelete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "row_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        DB::beginTransaction();

        try {
            
            User::where('id', $request->row_id)->delete();

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

    public function userStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|between:2,100',
            'username' => 'required|string|min:6',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        DB::beginTransaction();

        try {

            $user = New User ;
            $user->name = $request->name ;
            $user->username = $request->username ;
            $user->email = $request->email ;
            $user->entity_uid = Auth::user()->entity_uid ;
            $user->password = bcrypt($request->password) ;
            $user->save();
            $user_uid = $user->user_uid ;
            
            if($request->role_uid){
                UserHasRolesModel::updateOrCreate(
                    [
                        'user_uid' => $user_uid
                    ],
                    [
                        'role_uid' => $request->role_uid
                    ]
                );                    
            }

            if($request->checked){
                foreach ($request->checked as $key => $value) {
                    
                    UserHasCompanyModel::updateOrCreate(
                        [
                            'user_uid' => $user_uid,
                            'company_uid' => $value,
                        ],
                        [
                            "status" => 1
                        ]
                    );
                }                    
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

    public function userEdit(Request $request)
    {
        DB::beginTransaction();

        try {

            $userData = User::where('user_uid', $request->user_uid)->first();
            
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|between:2,100',
                'username' => 'required|string|min:6',
                'email' => 'required|string|email|max:100|unique:users,email,'.$userData->id,
                'role_uid' => 'required|string|min:6',
                'user_uid' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
        

            $userData->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email
            ]);

            UserHasRolesModel::updateOrCreate(
                [
                    'user_uid' => $userData->user_uid
                ],
                [
                    'role_uid' => $request->role_uid
                ]
            );

            UserHasCompanyModel::where('user_uid', $userData->user_uid)->forceDelete();

            if($request->checked){
                foreach ($request->checked as $key => $value) {
                    
                    UserHasCompanyModel::updateOrCreate(
                        [
                            'user_uid' => $userData->user_uid,
                            'company_uid' => $value,
                        ],
                        [
                            "status" => 1
                        ]
                    );
                }                    
            }
            
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully create data',
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {

            DB::rollBack();            
            $error = [
                'code' => 500,
                'request' => $request->all(),
                'response' => $e->getMessage()
            ];

            return response()->json($error, 500);

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

    public function userGetDataById(Request $request)
    {
            
        $validator = Validator::make($request->all(),[
            'user_uid' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        DB::beginTransaction();

        try {
           
            $UserHasRolesModel = UserHasRolesModel::where('user_uid', $request->user_uid)->first();
            $userHasCompany = UserHasCompanyModel::where('user_uid', $request->user_uid)->get()->toArray();
            $userData = User::where('user_uid', $request->user_uid)->first();
            $userData->role_uid = $UserHasRolesModel->role_uid ?? null;
            $userData->company_access = $userHasCompany ?? null;

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully create data',
                'data' => $userData,
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

    public function listRole(Request $request)
    {

        $model = RolesModel::select('role_uid','role_name')
                            ->orderBy('role_name','ASC')
                            ->get()
                            ->toArray();
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => $model,
        ];

        return response()->json($success, 200);
    }

    public function userChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'user_uid' => 'required',
            'new_password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        DB::beginTransaction();

        try {

            User::where('user_uid', $request->user_uid)->update([
                'password' => bcrypt($request->new_password)
            ]);

            DB::commit();
            $success = [
                'code' => '200',
                'message' => 'Successfully change password'
            ];

            return response()->json($success, 200);

        }catch(Exception $e){
            DB::rollback();

            $error = [
                'code' => '500',
                'request' => $request->all(),
                'response' => $e->getMessage(),
            ];

            return response()->json($error, 500);
        }   
    }

    public function permissionGetDataByRoleId(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'role_uid' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $model = RoleHasPermissionsModel::select('permission_uid')->where('role_uid', $request->role_uid)->get()->toArray();

        $roleName = RolesModel::where('role_uid', $request->role_uid)->first();

        $permission = PermissionModel::select('permissions_uid','permissions_name')->get()->toArray();

        $menu = MenusModel::leftJoin('permissions as ps','menus.acl_subject','ps.permissions_name')
                            ->where('ps.type','menu')
                            ->where('menus.menus_type','sectionTitle')
                            ->orderBy('menus.order_by','ASC')
                            ->get();

        $menu_array = [];

        foreach ($menu as $key => $value) {
            $menu_array[$key]['id'] = $value->permissions_uid;
            $menu_array[$key]['name'] = $value->menus_name;

            $menu2 = MenusModel::leftJoin('permissions as ps','menus.acl_subject','ps.permissions_name')
                            ->where('ps.type','menu')
                            ->where('menus.level','1')
                            ->where('menus.parent_id', $value->menus_uid)
                            ->orderBy('menus.order_by','ASC')
                            ->get();

            $menu_array2 = [];
            foreach ($menu2 as $key2 => $value2) {
                $menu_array2[$key2]['id'] = $value2->permissions_uid;
                $menu_array2[$key2]['name'] = $value2->menus_name;
                $menu_array2[$key2]['parent'] = $value->permissions_uid;

                    $menu3 = MenusModel::leftJoin('permissions as ps','menus.acl_subject','ps.permissions_name')
                                ->where('ps.type','menu')
                                ->where('menus.level','2')
                                ->where('menus.parent_id', $value2->menus_uid)
                                ->orderBy('menus.order_by','ASC')
                                ->get();
                        
                        $menu_array3 = [];
                        foreach ($menu3 as $key3 => $value3) {
                            $menu_array3[$key3]['id'] = $value3->permissions_uid;
                            $menu_array3[$key3]['name'] = $value3->menus_name;
                            $menu_array3[$key3]['parent'] = $value2->permissions_uid;
                        }
                        
                        $menu_array2[$key2]['children'] = $menu_array3;

            }

            $menu_array[$key]['children'] = $menu_array2;

        }
        
        $success = [
            'code' => 200,
            'message' => 'Successfully get data',
            'data' => [
                'role_name' => $roleName->role_name,
                'permission_by_role' => $model,
                'permission' => $menu_array,
                'permission_all' => $permission
            ],
        ];

        return response()->json($success, 200);

    }

    public function permissionSave(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'role_uid' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        DB::beginTransaction();

        try {

            RoleHasPermissionsModel::where('role_uid', $request->role_uid)->forceDelete();
            // $data_insert = [];

            foreach ($request->permission_uid as $key => $value) {
                // $data_insert[$key]['role_uid'] = $request->role_uid;
                // $data_insert[$key]['permission_uid'] = $value;
                // $data_insert[$key]['created_at'] = Carbon::now();

                RoleHasPermissionsModel::updateOrInsert(
                    [
                        "role_uid" => $request->role_uid,
                        "permission_uid" => $value
                    ],
                    [
                        "created_at" => Carbon::now()
                    ]
                );
            }

            // RoleHasPermissionsModel::insert($data_insert);

            DB::commit();

            $success = [
                'code' => '200',
                'message' => 'Successfully'
            ];

            return response()->json($success, 200);

        }catch(Exception $e){
            DB::rollback();

            $error = [
                'code' => '500',
                'request' => $request->all(),
                'response' => $e->getMessage(),
            ];

            return response()->json($error, 500);
        }


    }

    public function permissionByParent(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'parent_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $model = PermissionModel::where('parent_id', $request->parent_id)->get();
        $parent = PermissionModel::where('permissions_uid', $request->parent_id)->first();

        $success = [
            "status" => 200,
            "message" => "successfully get data",
            "data_header" => $parent->permissions_name,
            "data" => $model,
        ];

        return response()->json($success, 200);


    }

    public function getAuditor()
    {
        $model = MasterData::where('doc_type','IS_AUDITOR')->get();

        // dd($model);

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->auditor->user_uid;
            $data_array[$key]['label'] = $value->auditor->name;
        }                    
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data Auditor',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

    public function getAuditee()
    {
        $model = MasterData::where('doc_type','IS_AUDITEE')->get();

        // dd($model);

        $data_array = [];

        foreach ($model as $key => $value) {
            $data_array[$key]['id'] = $value->auditee->user_uid;
            $data_array[$key]['label'] = $value->auditee->name;
        }                    
       
        $success = [
            'code' => 200,
            'message' => 'Successfully get data Auditee',
            'data' => $data_array,
        ];

        return response()->json($success, 200);
    }

}
