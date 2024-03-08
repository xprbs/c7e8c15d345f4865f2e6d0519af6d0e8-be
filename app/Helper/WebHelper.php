<?php 

namespace App\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth, Validator, DB, Exception, Config;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\WEB\RolesModel;
use App\Models\WEB\PermissionModel;
use App\Models\WEB\UserHasRolesModel;
use App\Models\WEB\RoleHasPermissionsModel;
use App\Models\AuditChecklistModel;
use App\Models\MasterQuestionModel;
use App\Models\Surveillance;

class WebHelper
{
    public static function USER_ACCESS($level, $user_uid)
    {

        $model = UserHasRolesModel::select('user_has_roles.user_uid','user_has_roles.role_uid','pp.menus_uid','pp.menus_name','pp.menus_type','pp.level','pp.parent_id','pp.url','pp.icon','pp.order_by','pp.acl_action','pp.acl_subject')
                                ->leftJoin('role_has_permissions as rhp','user_has_roles.role_uid','rhp.role_uid')
                                ->leftJoin('permissions as pv','rhp.permission_uid','pv.permissions_uid')
                                ->join('menus as pp','pv.permissions_name','pp.acl_subject')
                                ->where('user_has_roles.user_uid', $user_uid)
                                ->where('pp.level',$level)
                                ->whereNull('user_has_roles.deleted_at')
                                ->whereNull('pp.deleted_at')
                                ->whereNull('rhp.deleted_at')
                                ->whereNull('pv.deleted_at')
                                ->orderBy('pp.level','ASC')
                                ->orderBy('pp.order_by','ASC')
                                ->get();

        return $model;

    }

    public static function USER_ACCESS_SUB($level, $user_uid, $parent)
    {
        
        $model = UserHasRolesModel::select('user_has_roles.user_uid','user_has_roles.role_uid','pp.menus_uid','pp.menus_name','pp.menus_type','pp.level','pp.parent_id','pp.url','pp.icon','pp.order_by','pp.acl_action','pp.acl_subject')
                                ->leftJoin('role_has_permissions as rhp','user_has_roles.role_uid','rhp.role_uid')
                                ->leftJoin('permissions as pv','rhp.permission_uid','pv.permissions_uid')
                                ->join('menus as pp','pv.permissions_name','pp.acl_subject')
                                ->where('user_has_roles.user_uid', $user_uid)
                                ->where('pp.level',$level)
                                ->where('pp.parent_id',$parent)
                                ->whereNull('user_has_roles.deleted_at')
                                ->whereNull('pp.deleted_at')
                                ->whereNull('rhp.deleted_at')
                                ->whereNull('pv.deleted_at')
                                ->orderBy('pp.level','ASC')
                                ->orderBy('pp.order_by','ASC')
                                ->get();

        return $model;

    }

    public static function USER_ACL($user_uid)
    {
        $model = UserHasRolesModel::select('pv.permissions_name')
                                ->leftJoin('role_has_permissions as rhp','user_has_roles.role_uid','rhp.role_uid')
                                ->leftJoin('permissions as pv','rhp.permission_uid','pv.permissions_uid')
                                ->where('user_has_roles.user_uid', $user_uid)
                                ->whereNull('rhp.deleted_at')
                                ->whereNull('pv.deleted_at')
                                ->get()->toArray();

        return $model ;

    }

    public static function VERIFY_TOKEN()
    {
        return response()->json([
            'status' => 200,
            'message' => 'Authorized'
        ], 200);
    }

    public static function GENERATE_AUDIT_NUMBER()
    {
        $lastest = AuditChecklistModel::orderBy('id','DESC')->first();

        $PREFIX = 'PA#';
        $YEAR = date('y');
        $MONTH = date('m');

        if($lastest){
            $last = Str::substr($lastest->audit_number,-4);
            $tambah = $last + 1 ;
            $padded = Str::padLeft($tambah, 4, '0');
        }else{
            
            $padded = '0001' ;
        }

        return $PREFIX.$YEAR.$MONTH.$padded;
    }

    public static function GENERATE_QT_NUMBER()
    {
        // Question Template Generate Number
        $lastest = MasterQuestionModel::orderBy('id','DESC')->first();

        $PREFIX = 'QT#';

        if($lastest){
            $last = Str::substr($lastest->question_number,-7);
            $tambah = $last + 1 ;
            $padded = Str::padLeft($tambah, 7, '0');
        }else{
            
            $padded = '0000001' ;
        }

        return $PREFIX.$padded;
    }

    public static function GENERATE_PROJECT_NUMBER()
    {
        // Question Template Generate Number
        $lastest = Surveillance::orderBy('id','DESC')->first();

        $PREFIX = 'SV#';

        if($lastest){
            $last = Str::substr($lastest->project_number,-7);
            $tambah = $last + 1 ;
            $padded = Str::padLeft($tambah, 7, '0');
        }else{
            
            $padded = '0000001' ;
        }

        return $PREFIX.$padded;
    }
}
