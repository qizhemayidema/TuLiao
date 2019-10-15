<?php

namespace app\admin\controller;

use app\common\controller\Upload;
use think\Controller;
use think\Request;

class Config extends Base
{
    public function index()
    {
        $config = $this->getConfig('*');

        $this->assign('config',$config);

        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $data = [
            'index_information_qrCode_1' => $post['index_information_qrCode_1'],
            'index_information_qrCode_2' => $post['index_information_qrCode_2'],
            'hotLine'                    => $post['hotLine'],
            'address'                    => $post['address'],
            'phone'                      => $post['phone'],
            'fax'                        => $post['fax'],
            'email'                      => $post['email']
        ];


        file_put_contents(self::WEBSITE_CONFIG_PATH,json_encode($data));

        return json(['code'=>1,'msg'=>'success']);

    }


    //上传课程封面
    public function uploadPic()
    {
        $path = 'config/';
        return (new Upload())->uploadOnePic($path);
    }
}
