<?php

namespace App\Service;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TokenService{

    public function createToken($userId){
        $time=new Carbon();
        $expiredTime = $time->copy();
        $expiredTime->hour+=1;
        $tokenContent=md5($time."##".$userId);
        DB::table('tokens')->insert([
            'token_content'=> $tokenContent,
            'user_id' =>$userId,
            'created_at' =>$time,
            'expired_at' =>$expiredTime
        ]);
        return $tokenContent;
    }
    public function getTokenByContent($tokenContent){
        $tokenInfo=DB::table('tokens')->where('token_content',$tokenContent)->first();
        return $tokenInfo;
    }

    public function getUserByToken($userId){
        $userInfo=DB::table('users')->where('user_id',$userId)->first();
        return $userInfo;
    }


}