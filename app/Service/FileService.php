<?php

namespace App\Service;


use Illuminate\Support\Facades\DB;

class FileService
{

    public function addFile($fileInfo)
    {
        $res=[];
        if ($this->isFileNameExist($fileInfo['file_name'])){
            $res['code'] = 2002;
            $res['message'] = "文件名已存在";
        }
        else{
            $id=DB::table('files')->insertGetId($fileInfo);
            $res['code'] = 2000;
            $res['message'] ="文件信息添加成功";
            $res['file_id']= $id;
        }
        return $res;

    }

    public function isFileNameExist($fileName)
    {
        $res = DB::table('files')->where('file_name', $fileName)->get();
        if ($res->first())
            return true;
        else return false;
    }
}