<?php

namespace App\Http\Controllers;

use App\Service\LessonService;
use App\Service\UserService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;

class LessonController extends Controller
{

    private $lessonService;
    private $userService;
    public function __construct(LessonService $lessonService,UserService $userService)
    {
        $this->lessonService=$lessonService;
        $this->userService=$userService;
    }
    public function createLesson(Request $request){
        $user=$request->user;
        if ($this->userService->permissionCheck($user->user_id,"teacher")||$this->userService->permissionCheck($user->user_id,"admin"))
        {
            $rule = [
                'lesson_name'=>'required',
            ];
            $res=ValidationHelper::validateCheck($request->all(),$rule);
            if ($res->fails()){
                return response()->json([
                    'code' => 3002,
                    'message'=> $res->errors()
                ]);
            }
            $lessonInfo=ValidationHelper::getInputData($request,$rule);
            $lessonInfo['lesson_master_id']=$user->user_id;
            $lessonId=$this->lessonService->createLesson($lessonInfo);
            return response()->json([
                'code' => 3000,
                'message' => '课程创建成功',
                'lessonId' => $lessonId
            ]);
        }
        else{
            return response()->json([
                'code' => 3001,
                'message' => '没有权限'
            ]);
        }
    }
    public function getLessonInfo($lessonId){
        $lessonInfo=$this->lessonService->getLessonInfo($lessonId);
        if (!$lessonInfo){
            return response()->json([
                'code'=> 3003,
                'message' => '课程不存在'
            ]);
        }
        return response()->json([
            'code' => 3000,
            'message' => '查询成功',
            'lessonInfo' => $lessonInfo
        ]);
    }
    public function getLessonList(){
        $list=$this->lessonService->getLessonList();
        return response()->json([
            'code' => 3000,
            'list' =>$list
        ]);
    }
    public function updateLesson(Request $request){
        $rule = [
            'lesson_id'=>'required',
            'lesson_name' => 'required',
            'lesson_master_id'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->all(),$rule);
        if ($res->fails()){
            return response()->json([
                'code' =>3002,
                'message'=>$res->errors()
            ]);
        }
        $lessonInfo=ValidationHelper::getInputData($request,$rule);
        $user=$request->user;
        if (!$lessonInfo['lesson_master_id']==$user->user_id || !$this->userService->permissionCheck($user->user_id,"admin")){
            return response()->json([
                'code' => 3001,
                'message' => '没有权限'
            ]);
        }
        $this->lessonService->updateLesson($lessonInfo);
        return response()->json([
            'code'=>3000,
            'message' =>'课程信息修改成功'
        ]);
    }
    public function deleteLesson(Request $request,$lessonId){
        $lessonInfo=$this->lessonService->getLessonInfo($lessonId);
        if (!$lessonInfo)
            return response()->json([
                'code'=> 3003,
                'message' => '课程不存在'
            ]);
        $user=$request->user;
        if (!$lessonInfo->lesson_master_id==$user->user_id && $this->userService->permissionCheck($user->user_id,"admin")){
            return response()->json([
                'code' => 3001,
                'message' => '没有权限'
            ]);
        }
        $this->lessonService->deleteLesson($lessonId);
        return response()->json([
            'code'=> 3000,
            'message'=> '删除成功'
        ]);
    }
}
