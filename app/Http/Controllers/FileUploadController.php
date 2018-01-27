<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Qiniu\Auth;
use function Qiniu\base64_urlSafeDecode;
use function Qiniu\base64_urlSafeEncode;

class FileUploadController extends Controller
{
    private $accessKey= "_0Ik_hl2cFK2_b5fK6E-32kwgYHWHKYAXHNp6UsT";
    private $secretKey= "8xltykfGaN_lm-ow3VC5KAfb13wWOEehtyQKRMPo";
    private $bucket ="mooc";

    public function getUploadToken(){
        $auth=new Auth($this->accessKey,$this->secretKey);
        //过期时间
        $expires =3600;
        //存储位置和名称
        $saveMp4Entry=base64_urlSafeEncode($this->bucket.":test.mp4");
        //视频处理方式
        $videoDeal ="avthumb/m3u8/noDomain/1/vb/500k|saveas/".$saveMp4Entry;
        $policy = array(
            'persistentOps' => $videoDeal,
            'persistentPipeline' => "video-pipe",
            'persistentNotifyUrl' => 'mooc.sealbaby.cn/notify'
        );
        $uploadToken=$auth->uploadToken($this->bucket,null,$expires,$policy,true);
        return response()->json([
            'uploadToken' => $uploadToken
        ]);
    }
}
