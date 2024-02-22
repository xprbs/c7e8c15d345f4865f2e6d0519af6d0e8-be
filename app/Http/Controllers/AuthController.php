<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator, DB, Config, Str;
use Carbon\Carbon;
use App\Models\WEB\UserHasRolesModel;

class AuthController extends Controller
{
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            // 'username' => 'required|string|min:6',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'username' => 'required|string|min:6',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

       
        $user = New User ;
        $user->name = $request->name ;
        $user->username = $request->username ;
        $user->email = $request->email ;
        $user->password = bcrypt($request->password) ;
        $user->save();
        
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function logout(Request $request){
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);

    }

    public function refresh(Request $request){
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile(Request $request){
        $UserHasRolesModel = UserHasRolesModel::where('user_uid',auth()->user()->user_uid)->first();
        $user = [
            'name' => auth()->user()->name,
            'username' => auth()->user()->username,
            // 'email' => auth()->user()->email,
            // 'user_uid' => auth()->user()->user_uid,
            'role' => $UserHasRolesModel->roleName->role_name,
        ];

        return response()->json([
            'userData' => $user
        ]);
    }

    protected function createNewToken($token){
        $UserHasRolesModel = UserHasRolesModel::where('user_uid',auth()->user()->user_uid)->first();

        $user = [
            'name' => auth()->user()->name,
            'username' => auth()->user()->username,
            // 'email' => auth()->user()->email,
            // 'user_uid' => auth()->user()->user_uid,
            'role' => $UserHasRolesModel->roleName->role_name ?? null,
            'entity' => auth()->user()->entity_uid,
        ];

        return response()->json([
            'message' => 'Authorized',
            'accessToken' => $token,
            // 'refreshToken' => auth()->refresh(),
            // 'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'userData' => $user
        ]);
    }


}
