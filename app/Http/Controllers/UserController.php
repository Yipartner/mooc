<?php

namespace App\Http\Controllers;

use App\Service\TokenService;
use App\Service\UserService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $token = $this->tokenService->createToken($loginRes['user_id']);
                return response()->json([
                    'code' => 1000,
                    'message' => "登录成功，请查收token！",
                    'token' => $token,
                    'user_id' => $loginRes['user_id']
                ]);
            }
        }
    }

    public function userInfoEdit(Request $request)
    {
        $userInfo = [];
        if (!isset($request->user_id))
            return response()->json([
                'code' => 1004,
                'message' => '缺少用户id'
            ]);
        $user_id = $request->user_id;
        if ($request->user->user_id != $user_id)
            return response()->json([
                'code' => 1007,
                'message' => '没有权限'
            ]);
        $userInfo['user_id'] = $user_id;
        if (isset($request->newPassword) && isset($request->password)) {

            $userInfo['password'] = $request->newPassword;
            if (!$this->userService->checkPassword($user_id, $request->password))
                return response()->json([
                    'code' => 1007,
                    'message' => '密码错误'
                ]);
            $this->userService->userInfoEdit($userInfo);
            return response()->json([
                'code' => 1000,
                'message' => '密码修改成功'
            ]);
        }
        if (isset($request->user_name) ){
            $userInfo['user_name'] = $request->user_name;
            $this->userService->userInfoEdit($userInfo);
            return response()->json([
                'code' => 1000,
                'message' => '信息修改成功'
            ]);
        }
        return response()->json([
            'code' => 1004,
            'message' => '缺少字段，请检查'
        ]);
    }

    public function getUserInfo($user_id)
    {
        $userInfo = $this->userService->getUserInfo($user_id);
        if ($userInfo)
            return response()->json([
                'code' => 1000,
                'userInfo' => $userInfo
            ]);
        return response()->json([
            'code' => 1003,
            'message' => '用户不存在'
        ]);
    }

    public function test(Request $request)
    {
        dd($request->user->user_id);
    }
}
