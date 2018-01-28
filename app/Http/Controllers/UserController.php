<?php

namespace App\Http\Controllers;

use App\Service\TokenService;
use App\Service\UserService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    private $tokenService;

    public function __construct(UserService $userService, TokenService $tokenService)
    {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }

    public function register(Request $request)
    {
        $rule = [
            'user_name' => 'required',
            'user_number' => 'required',
            'password' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->all(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1004,
                'message' => $res->errors()
            ]);
        } else {
            $userInfo = ValidationHelper::getInputData($request, $rule);
            $exist = $this->userService->isUserNumberExist($userInfo['user_number']);
            if ($exist == true) {
                return response()->json([
                    'code' => 1005,
                    'message' => "账号已存在"
                ]);
            }
            $userInfo['status'] = 1;
             $this->userService->register($userInfo);
            return response()->json([
                'code' => 1000,
                'message' => "注册成功",
            ]);

        }
    }

    public function login(Request $request)
    {
        $rule = [
            'user_number' => 'required',
            'password' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->all(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 1004,
                'message' => $res->errors()
            ]);
        } else {
            $userInfo = ValidationHelper::getInputData($request, $rule);
            $loginRes = $this->userService->login($userInfo);
            if ($loginRes['code'] != 1000) {
                return response()->json([
                    'code' => $loginRes['code'],
                    'message' => $loginRes['message']
                ]);
            } else {
                $token=$this->tokenService->createToken($loginRes['user_id']);
                return response()->json([
                    'code'=>1000,
                    'message'=>"登录成功，请查收token！",
                    'token' =>$token
                ]);
            }
        }
    }
    public function test(Request $request){
        dd($request->user);
    }
}
