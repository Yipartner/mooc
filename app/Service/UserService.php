<?php

namespace App\Service;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserService
{

    public function register($userInfo)
    {
        $time = new Carbon();
        $userInfo['created_at'] = $time;

        DB::transaction(function () use ($userInfo) {
            $userInfo['user_id'] = DB::table('users')->insertGetId($userInfo);

            DB::table('user_roles')->insert([
                'user_id' => $userInfo['user_id'],
                'user_role' => 'student'
            ]);
        });
    }

    public function isUserNumberExist($userNumber)
    {
        $user = DB::table('users')->where('user_number', $userNumber)->get();
        if ($user->first()) {
            return true;
        } else {
            return false;
        }
    }

    public function login($userInfo)
    {
        $userTrueInfo = DB::table('users')->where('user_number', $userInfo['user_number'])->first();
        $res = [];
        if (!$userTrueInfo) {
            $res['code'] = 1003;
            $res['message'] = "账号不存在";
            return $res;
        }
        $status = $userTrueInfo->status;
        if ($status == 0) {
            $res['code'] = 1001;
            $res['message'] = "账号未激活";
            return $res;
        }
        if ($userTrueInfo->password != $userInfo['password']) {
            $res['code'] = 1002;
            $res['message'] = "密码错误";
            return $res;
        }
        $res['code'] = 1000;
        $res['message'] = "信息正确。允许登录";
        $res['user_id'] = $userTrueInfo->user_id;
        return $res;
    }

    public function userInfoEdit($userInfo)
    {
        DB::table('users')->where('user_id', $userInfo['user_id'])->update($userInfo);
    }

    public function getUserInfo($userId)
    {
        $userInfo = DB::table('users')->where('user_id', $userId)->select('user_id', 'user_name')->first();
        return $userInfo;
    }

    public function checkPassword($user_id, $password)
    {
        $truePassword = DB::table('users')->where('user_id', $user_id)->value('password');
        if ($truePassword == $password)
            return true;
        else
            return false;
    }
    public function permissionCheck($user_id,string $role){
        $trueRole=DB::table('user_roles')->where('user_id',$user_id)->value('user_role');
        if ($trueRole==$role){
            return true;
        }
        else
            return false;
    }
}