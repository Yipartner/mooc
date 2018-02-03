<?php

namespace App\Http\Controllers;

use App\Service\ClassHourService;
use App\Service\FileService;
use App\Service\UserService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;

class ClassHourController extends Controller
{
    private $classHourService;
    private $userService;
    private $fileService;
    public function __construct(ClassHourService $classHourService,UserService $userService,FileService $fileService)
    {
        $this->classHourService=$classHourService;
        $this->userService=$userService;
        $this->fileService=$fileService;
    }
    public function createClass(Request $request){
        $user=$request->user;
        if (!($this->userService->permissionCheck($user->user_id,'teacher')||$this->userService->permissionCheck($user->user_id,'admin')))
        {
            return response()->json([
                'code' => 4001,
                'message' => '没有权限'
            ]);

        }
        $rule=[
            'lesson_id' =>'required',
            'class_type' => 'required',
            'class_hour_name'=>'required',
            'free' => 'required'
        ];
        $res=ValidationHelper::validateCheck($request->all(),$rule);
        if ($res->fails()){
            return response()->json([
                'code' => 4002,
                'message' => $res->errors()
            ]);
        }
        $info=ValidationHelper::getInputData($request,$rule);
        if ($info['class_type']==1){
            if (isset($request->class_url)||empty($request->class_url))
            {
                return response()->json([
                    'code' => 4002,
                    'message' => "缺少class_url字段"
                ]);
            }
            $info['class_url']=$request->class_url;
        }
        else{
            if (isset($request->class_content)||empty($request->class_content))
            {
                return response()->json([
                    'code' => 4002,
                    'message' => "缺少class_content字段"
                ]);
            }
            $info['class_content']=$request->class_content;
        }
        $this->classHourService->createClassHour($info);
        return response()->json([
            'code'=>4000,
            'message' => '课程创建成功'
        ]);
    }
    public function updateClass(Request $request){
        $user=$request->user;
        if (!($this->userService->permissionCheck($user->user_id,'teacher')||$this->userService->permissionCheck($user->user_id,'admin')))
        {
            return response()->json([
                'code' => 4001,
                'message' => '没有权限'
            ]);

        }
        $rule=[
            'class_hour_id' => 'required',
            'class_type' => 'required',
            'class_hour_name'=>'required',
            'free' => 'required'
        ];
        $res=ValidationHelper::validateCheck($request->all(),$rule);
        if ($res->fails()){
            return response()->json([
                'code' => 4002,
                'message' => $res->errors()
            ]);
        }
        $info=ValidationHelper::getInputData($request,$rule);
        if ($info['class_type']==1){
            if (isset($request->class_url)||empty($request->class_url))
            {
                return response()->json([
                    'code' => 4002,
                    'message' => "缺少class_url字段"
                ]);
            }
            $info['class_url']=$request->class_url;
        }
        else{
            if (isset($request->class_content)||empty($request->class_content))
            {
                return response()->json([
                    'code' => 4002,
                    'message' => "缺少class_content字段"
                ]);
            }
            $info['class_content']=$request->class_content;
        }
        $this->classHourService->updateClassHour($info);
        return response()->json([
            'code'=>4000,
            'message' => '课程更新成功'
        ]);
    }
    public function deleteClass(Request  $request,$class_id){
        $user=$request->user;
        if (!($this->userService->permissionCheck($user->user_id,'teacher')||$this->userService->permissionCheck($user->user_id,'admin')))
        {
            return response()->json([
                'code' => 4001,
                'message' => '没有权限'
            ]);

        }
        $this->classHourService->deleteClass($class_id);
        return response()->json([
            'code' => 4000,
            'message' => '删除成功'
        ]);
    }
    public function getClassInfo($class_id){
        $info = $this->classHourService->classInfo($class_id);
        if ($info['free']==1){
            return response()->json([
                'code' => 4000,
                'info' =>$info
            ]);
        }
    }
}
