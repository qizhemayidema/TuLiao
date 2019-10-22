<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class UserAuth extends Base
{
    //展示
    public function index(Request $request)
    {
        $user = (new \app\common\model\User())->where(['is_auth'=>1])->paginate(15);

        $this->assign('user',$user);
        return $this->fetch();
    }

    //改变状态
    public function changeAuth(Request $request)
    {
        $status = $request->post('status');
        $user_id = $request->post('user_id');

        (new \app\common\model\User())->where(['id'=>$user_id])->update(['is_auth'=>$status]);

        return json(['code'=>1,'msg'=>'success']);
    }
}
