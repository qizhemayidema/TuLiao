<?php

namespace app\admin\controller;

use app\common\model\OrderGoods;
use think\Controller;
use think\Request;

class Order extends Base
{
    public function index()
    {
        $order = (new \app\common\model\Order())->order('status','asc')
            ->order('create_time','desc')
            ->paginate(15);

        $this->assign('order',$order);

        return $this->fetch();
    }

    //列表
    public function info(Request $request)
    {
        $id = $request->param('id');

        $order = (new \app\common\model\Order())->find($id);

        $user = (new \app\common\model\User())->find($order['user_id']);

        $goods = (new OrderGoods())->where(['order_id'=>$order['id']])->select()->toArray();

        foreach ($goods as $key => $value) {
            $goods[$key]['goods'] = json_decode($value['goods_json_encode'],true);
        }
        $order['address'] = json_decode($order['address'],true);

        $this->assign('order',$order);

        $this->assign('user',$user);

        $this->assign('goods',$goods);

        return $this->fetch();


    }

    //详情 --下单用户 --订单商品 --订单基本信息

    //操作
    public function changeStatus(Request $request)
    {
        $status = $request->post('status');
        $order_id = $request->post('order_id');

        (new \app\common\model\Order())->where(['id'=>$order_id])->update(['status'=>$status]);

        return json(['code'=>1,'msg'=>'success']);
    }
}
