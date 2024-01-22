<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebMasterDataController;
use App\Http\Controllers\WebTransactionController;
use App\Http\Controllers\AuditChecklistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);  
     
    });
   
});

Route::group(['middleware' => 'jwt.verify'], function () {
    Route::group(['prefix' => 'web'], function () {
        Route::group(['prefix' => 'master'], function () {
            Route::post('/role-store', [WebMasterDataController::class, 'roleStore']);
            Route::post('/role-list', [WebMasterDataController::class, 'roleList']);
            Route::post('/role-delete', [WebMasterDataController::class, 'roleDelete']);
            Route::post('/menu-store', [WebMasterDataController::class, 'menuStore']);
            Route::post('/menu-list', [WebMasterDataController::class, 'menuList']);
            Route::post('/menu-delete', [WebMasterDataController::class, 'menuDelete']);
            Route::post('/menu-level-parent', [WebMasterDataController::class, 'menuLevelParent']);
            Route::post('/permission-list', [WebMasterDataController::class, 'permissionList']);
            Route::post('/permission-store', [WebMasterDataController::class, 'permissionStore']);
            Route::post('/permission-delete', [WebMasterDataController::class, 'permissionDelete']);
            Route::post('/permission-by-role-id', [WebMasterDataController::class, 'permissionGetDataByRoleId']);
            Route::post('/permission-save', [WebMasterDataController::class, 'permissionSave']);
            Route::post('/permission-by-parent-id', [WebMasterDataController::class, 'permissionByParent']);
            Route::post('/permission-parent', [WebMasterDataController::class, 'permissionParent']);
            Route::post('/user-list', [WebMasterDataController::class, 'userList']);
            Route::post('/user-delete', [WebMasterDataController::class, 'userDelete']);
            Route::post('/user-store', [WebMasterDataController::class, 'userStore']);
            Route::post('/user-edit', [WebMasterDataController::class, 'userEdit']);
            Route::post('/user-get-by-uid', [WebMasterDataController::class, 'userGetDataById']);
            Route::post('/list-role', [WebMasterDataController::class, 'listRole']);
            Route::post('/user-change-password', [WebMasterDataController::class, 'userChangePassword']);

            // Master Data Audit Checklist
            Route::post('/get-dept', [AuditChecklistController::class, 'getDept']);
            Route::post('/get-question', [AuditChecklistController::class, 'getQuestion']);
            
            Route::group(['prefix' => 'question-template'], function () {
                Route::post('/list', [AuditChecklistController::class, 'questionTemplateList']);
                Route::post('/store', [AuditChecklistController::class, 'questionTemplateStore']);
            });

            Route::group(['prefix' => 'audit-category'], function () {
                Route::post('/list', [AuditChecklistController::class, 'auditCategoryList']);
                Route::post('/store', [AuditChecklistController::class, 'auditCategoryStore']);
                Route::post('/delete', [AuditChecklistController::class, 'auditCategoryDelete']);
            });

        });
        
        Route::post('/user-access', [WebTransactionController::class, 'USER_ACCESS']);
        Route::post('/check-verify-token', [WebTransactionController::class, 'VERIFY_TOKEN']);
        
        Route::group(['prefix' => 'audit-checklist'], function () {
            Route::post('/store', [AuditChecklistController::class, 'auditChecklistStore']);
            Route::post('/list', [AuditChecklistController::class, 'auditChecklist']);
        });

    });
    
});
