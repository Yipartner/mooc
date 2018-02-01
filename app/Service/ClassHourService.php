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
}