<?php

namespace App\Http\Controllers;

use App\Service\FileService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Qiniu\Auth;
use function Qiniu\base64_urlSafeDecode;
use function Qiniu\base64_urlSafeEncode;

class FileUploadController extends Controller
{
    private $accessKey = "_0Ik_hl2cFK2_b5fK6E-32kwgYHWHKYAXHNp6UsT";
    private $secretKey = "8xltykfGaN_lm-ow3VC5KAfb13wWOEehtyQKRMPo";
    private $bucket = "mooc";
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService=$fileService;
    }

    public function getUploadToken(Request $request)
    {
        $rule = [
            'file_name' => 'required',
            'lesson_id' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->all(), $rule);
        if ($res->fails()) {
            return response()->json([
                'code' => 2001,
                'message' => $res->errors()
            ]);
        }
        $mp4Info = ValidationHelper::getInputData($request, $rule);
        $trueFileName = $mp4Info['lesson_id'] . "#" . $mp4Info['file_name'];
        $fileName = $mp4Info['lesson_id'] . "#TS#" . $mp4Info['file_name'];
        $mp4Info['true_file_name'] = $trueFileName;
        $mp4Info['file_name'] = $fileName;
        $mp4Info['file_url']="http://p37gfblil.bkt.clouddn.com".$mp4Info['true_file_name'];
        if ($this->fileService->isFileNameExist($mp4Info['file_name'])){
            return response()->json([
                'code' => 2002,
                'message' =>"文件名已存在"
            ]);
        }
        $auth = new Auth($this->accessKey, $this->secretKey);
        //过期时间
        $expires = 3600;
        //存储位置和名称
        $saveMp4Entry = base64_urlSafeEncode($this->bucket . ":" . $fileName);
        //视频处理方式

        $url = base64_urlSafeEncode("p37gfblil.bkt.clouddn.com");
        $videoDeal = "avthumb/m3u8/noDomain/0/domain/" . $url . "/vb/500k|saveas/" . $saveMp4Entry;
        $policy = array(
            'saveKey'=>$trueFileName,
            'callbackUrl' => 'mooc.sealbaby.cn/upload/callback',
            'callbackBody' => '{"persistentId":"$(persistentId)","mp4Info":'.json_encode($mp4Info).'}',
            'callbackBodyType' => 'application/json',
            'persistentOps' => $videoDeal,
            'persistentPipeline' => "video-pipe",
            'persistentNotifyUrl' => 'mooc.sealbaby.cn/notify'
        );
        $uploadToken = $auth->uploadToken($this->bucket,null, $expires, $policy, true);
        return response()->json([
            'uploadToken' => $uploadToken
        ]);
    }
    public function callback(Request $request){
        $persistentId=$request->persistentId;
        $mp4Info=$request->mp4Info;
        $mp4Info['status_id']=$persistentId;
        $addRes=$this->fileService->addFile($mp4Info);

        if ($addRes['code']!=2000){
            return response()->json([
                "code"=>$addRes['code'],
                "message"=>$addRes['message']
            ]);
        }
    }
    public function notify(Request $request){
        DB::table('files')->where('status_id',$request->id)->update([
            'status' => $request->code
        ]);
    }
}
