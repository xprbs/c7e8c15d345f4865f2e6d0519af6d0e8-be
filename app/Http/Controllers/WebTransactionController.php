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

use App\Helper\WebHelper;

use App\Models\WEB\RolesModel;
use App\Models\WEB\PermissionModel;
use App\Models\WEB\UserHasRolesModel;
use App\Models\WEB\RoleHasPermissionsModel;

class WebTransactionController extends Controller
{
    public function USER_ACCESS(Request $request)
    {
        $user_uid = Auth::user()->user_uid;
        $model = WebHelper::USER_ACCESS('1', $user_uid);

        $data = [];
        foreach ($model as $key => $value) {

            if($value->menus_type == 'sectionTitle'){
                $data[$key]['sectionTitle']          = $value->menus_name ;                
                $data[$key]['action']          = $value->acl_action ;
                $data[$key]['subject']          = $value->acl_subject ;
            }else{
                $data[$key]['title']          = $value->menus_name ;
                $data[$key]['icon']            = $value->icon ;
                $data[$key]['path']          = $value->url ;
                $data[$key]['order']          = $value->order_by ;
                $data[$key]['action']          = $value->acl_action ;
                $data[$key]['subject']          = $value->acl_subject ;

                $sub = WebHelper::USER_ACCESS_SUB('2', $user_uid, $value->menus_uid);
                if ($sub->count() > 0 ) {
                    
                    $sub_data = [];
                    foreach ($sub as $key2 => $value2) {
                        $sub_data[$key2]['title']          = $value2->menus_name ;
                        // $sub_data[$key2]['icon']            = $value2->icon ;
                        $sub_data[$key2]['path']          = $value2->url ;
                        $sub_data[$key2]['order']          = $value2->order_by ;
                        $sub_data[$key2]['action']          = $value2->acl_action  ;
                        $sub_data[$key2]['subject']          = $value->acl_subject ;
                    }
                    
                $data[$key]['children']          = $sub_data ;
                }
            }
        }

        $model_acl = WebHelper::USER_ACL($user_uid);

        $success = [
            "menus" => $data,
            "acl" => $model_acl
        ];

        return response()->json($success, 200);
    }

    public function VERIFY_TOKEN(Request $request)
    {
        $verify = WebHelper::VERIFY_TOKEN();

        return $verify ;
    }
}
