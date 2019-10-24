<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class FooterArticle extends Base
{
    public  function index(Request $request)
    {
        $type = $request->param('type');
        $arr = [
            'about_our' => '关于我们',
            'contact_our' => '联系我们',
        ];
        if (!isset($arr[$type])) throw new \Exception('',404);

        $title = $arr[$type];

        $content = $this->getConfig($type);

        $this->assign('title',$title);

        $this->assign('type',$type);

        $this->assign('content',$content);

        return $this->fetch();
    }
}
