<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;

class LessonService
{

    public function createLesson($lessonInfo)
    {
        $id=DB::table('lessons')->insertGetId($lessonInfo);
        return $id;
    }

    public function updateLesson($lessonInfo)
    {
        DB::table('lessons')->where('lesson_id', $lessonInfo['lesson_id'])->update([
            'lesson_name' => $lessonInfo['lesson_name'],
            'lesson_description' => $lessonInfo['lesson_description']
        ]);
    }
    public function getLessonInfo($lessonId){
        $lessonInfo=DB::table('lessons')->where('lesson_id',$lessonId)->first();
        return $lessonInfo;
    }
    public function getLessonList()
    {
        $lessonList = DB::table('lessons')->select('lesson_id','lesson_name','lesson_master_id')->get();
        return $lessonList;
    }

    public function deleteLesson($lessonId)
    {
        DB::transaction(function () use ($lessonId) {
            DB::table('lessons')->where('lesson_id', $lessonId)->delete();
            DB::table('files')->where('lesson_id',$lessonId)->update([
                'lesson_id' => 0
            ]);
            DB::table('class_hours')->where('lesson_id', $lessonId)->delete();
            DB::table('lesson_users')->where('lesson_id', $lessonId)->delete();
        });
    }
}