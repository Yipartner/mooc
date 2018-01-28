<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/29
 * Time: 下午11:24
 */

namespace App\Tool;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 表单验证辅助类
 * Class ValidationHelper
 * @package App\Common
 */

class ValidationHelper
{
    public static function validateCheck(array $inputs,array $rules)
    {
        $validator = Validator::make($inputs,$rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => '表单验证出错'
            ]);
        }
    }

    public static function getInputData(Request $request,array $rules)
    {
        $data = [];

        foreach ($rules as $key => $rule) {
            $data[$key] = $request->input($key,null);
        }

        return $data;
    }

    public static function checkAndGet(Request $request,array $rules)
    {
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => '表单验证出错'
            ]);
        }

        $data = [];

        foreach ($rules as $key => $rule) {
            $data[$key] = $request->input($key,null);
        }

        return $data;
    }
}