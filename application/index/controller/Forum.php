<?php

namespace app\index\controller;

use app\common\controller\Upload;
use think\Controller;
use think\Request;

class Forum extends Base
{
    public function uploadContentPic()
    {
        $upload = (new Upload())->uploadOnePic('forum/','file_path');
        $upload = $upload->getData();
        if ($upload['code'] == 1) {
            return json(['success' => true, 'msg' => '图片上传成功', 'file_path' => $upload['msg']]);
        } else {
            return json(['success' => false, 'msg' => $upload['msg'], 'file_path' => '']);
        }
    }
}
