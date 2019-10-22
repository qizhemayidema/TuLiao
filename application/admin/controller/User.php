<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class User extends Base
{
    public function index(Request $request)
    {
        $search = $request->get('search') ?? '';
        $user = (new \app\common\model\User());
        if ($search) $user = $user->whereLike('phone',$search)->whereOr('nickname','like','%'.$search.'%');
        $user = $user->order('id','desc')->paginate(15,false,['query' => request()->param()]);

        $this->assign('user',$user);
        $this->assign('search',$search);

        return $this->fetch();
    }

    public function changeStatus(Request $request)
    {
        $user_id = $request->param('user_id');
        (new \app\common\model\User())->changeStatus($user_id);
        return json(['code'=>1,'msg'=>'success']);
    }

    public function authInfo(Request $request)
    {
        $user = (new \app\common\model\User())->find($request->param('id'));

        $this->assign('user',$user);

        return $this->fetch();
    }
}
