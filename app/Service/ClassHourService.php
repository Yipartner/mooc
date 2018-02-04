<?php

namespace App\Service;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClassHourService{

    public function createClassHour($classHourInfo){
        $time=new Carbon();
        $classHourInfo['created_at']=$time;
        DB::table('class_hours')->insert($classHourInfo);
    }
    public function updateClassHour($newClassHourInfo){
        DB::table('class_hours')->where('class_hour_id',$newClassHourInfo['class_hour_id'])->update($newClassHourInfo);
    }
    public function classList($lesson_id){
        $list=DB::table('class_hours')->where('lesson_id',$lesson_id)->select('class_hour_id','class_hour_name','free')->get();
        return $list;
    }
    public function classInfo($class_hour_id){
        $info=DB::table('class_hours')->where('class_hour_id',$class_hour_id)->first();
        return $info;
    }
    public function deleteClass($class_hour_id){
        DB::table('class_hours')->where('class_hour_id',$class_hour_id)->delete();
    }
    
}