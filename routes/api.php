<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebMasterDataController;
use App\Http\Controllers\WebTransactionController;
use App\Http\Controllers\AuditChecklistController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuditCategoryController;
use App\Http\Controllers\QuestionTemplateController;
use App\Http\Controllers\SurveillanceController;
use App\Http\Controllers\CustomFieldController;

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
        Route::post('/user-change-password', [AuthController::class, 'userChangePassword']);  
     
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

            // GET DROPDOWN
            // Question
            Route::post('/get-question', [QuestionTemplateController::class, 'getQuestion']);
            // Audit Category
            Route::post('/get-audit-category', [AuditCategoryController::class, 'getAuditCategory']);
            Route::post('/get-audit-category-ref', [AuditCategoryController::class, 'getAuditCategoryRef']);
            // Organization
            Route::post('/get-company', [CompanyController::class, 'getCompany']);
            Route::post('/get-company-trans', [CompanyController::class, 'getCompanyTrans']);
            Route::post('/get-dept', [CompanyController::class, 'getDept']);
            Route::post('/get-auditor', [WebMasterDataController::class, 'getAuditor']);
            Route::post('/get-auditee', [WebMasterDataController::class, 'getAuditee']);
            Route::post('/get-auditor-type', [WebMasterDataController::class, 'getAuditorType']);
            Route::post('/get-auditee-type', [WebMasterDataController::class, 'getAuditeeType']);
            
            Route::group(['prefix' => 'question-template'], function () {
                Route::post('/list', [QuestionTemplateController::class, 'questionTemplateList']);
                Route::post('/store', [QuestionTemplateController::class, 'questionTemplateStore']);
                Route::post('/get-detail', [QuestionTemplateController::class, 'questionGetDetail']);
                Route::post('/question-detail-store', [QuestionTemplateController::class, 'questionDetailStore']);
                Route::post('/question-detail-list', [QuestionTemplateController::class, 'getQuestionDetailList']);
                Route::post('/get-master-answer', [QuestionTemplateController::class, 'getMasterAnswer']);
                Route::post('/get-master-answer-id', [QuestionTemplateController::class, 'getMasterAnswerId']);
            });

            Route::group(['prefix' => 'audit-category'], function () {
                Route::post('/list', [AuditCategoryController::class, 'auditCategoryList']);
                Route::post('/store', [AuditCategoryController::class, 'auditCategoryStore']);
                Route::post('/delete', [AuditCategoryController::class, 'auditCategoryDelete']);
            });
            
            Route::group(['prefix' => 'company'], function () {
                Route::post('/list', [CompanyController::class, 'companyList']);
                Route::post('/store', [CompanyController::class, 'companyStore']);
                Route::post('/delete', [CompanyController::class, 'companyDelete']);
            });

            Route::group(['prefix' => 'custom-field'], function () {
                Route::post('/get', [CustomFieldController::class, 'customGet']);
                Route::post('/store', [CustomFieldController::class, 'customStore']);
            });

        });
        
        Route::post('/user-access', [WebTransactionController::class, 'USER_ACCESS']);
        Route::post('/check-verify-token', [WebTransactionController::class, 'VERIFY_TOKEN']);
        
        Route::group(['prefix' => 'audit-checklist'], function () {
            Route::post('/store', [AuditChecklistController::class, 'auditChecklistStore']);
            Route::post('/list', [AuditChecklistController::class, 'auditChecklist']);
            Route::post('/get-detail', [AuditChecklistController::class, 'auditChecklistGetDetail']);
            Route::post('/answer-store', [AuditChecklistController::class, 'AuditChecklistAnswerStore']);
            Route::post('/get-answer', [AuditChecklistController::class, 'getAuditChecklistAnswer']);
            Route::post('/get-approval', [AuditChecklistController::class, 'getAuditApproval']);
        });
        
        Route::group(['prefix' => 'surveillance'], function () {
            Route::post('/list', [SurveillanceController::class, 'surveillanceList']);
            Route::post('/store', [SurveillanceController::class, 'surveillanceStore']);
        });
    });
    
});
