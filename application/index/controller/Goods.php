<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use app\admin\controller\GoodsCate as GoodsCateController;
use app\common\model\Category as CateModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\Shopping as ShoppingModel;
use think\Validate;

class Goods extends Base
{
    public function info(Request $request)
    {
        $goods_id = $request->param('id');
        //商品分类
        $gc = new GoodsCateController();
        $cateModel = (new CateModel());
        $goods_cate = $cateModel->getList($gc->cacheName, $gc->cateType);


        $goods = (new GoodsModel())->find($goods_id);

        $two_cate = $cateModel->where(['id' => $goods->cate_id])->find();

        $one_cate = $cateModel->where(['id' => $two_cate->p_id])->find();


        $this->assign('goods_cate', $goods_cate);
        $this->assign('goods', $goods);
        $this->assign('two_cate', $two_cate);
        $this->assign('one_cate', $one_cate);

        return $this->fetch();
    }

    /**
     * $order_price         0 正序 1 倒序
     * $order_online_time   0 正序 1 倒序
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function category(Request $request)
    {
        $one_cate = $request->param('one_cate');
        $two_cate = $request->param('two_cate');

        $order = explode('_',$request->param('order'));
        $order_price = 'asc';
        $order_online_time = 'asc';
        //商品分类
        $gc = new GoodsCateController();
        $cateModel = new CateModel();
        $goods_cate = (new CateModel())->getList($gc->cacheName, $gc->cateType);
        if (!$one_cate){
            isset($goods_cate[0]) && $one_cate = $goods_cate[0];
        }else{
            $one_cate = $cateModel->find($one_cate);
        }
        if (!$two_cate){
            isset($goods_cate[0]['children'][0]) && $two_cate = $goods_cate[0]['children'][0];
        }else{
            $two_cate = $cateModel->find($two_cate);
        }
        if (isset($order[0]) && $order[0] == 1) $order_price = 'desc';
        if (isset($order[1]) && $order[1] == 1) $order_online_time = 'desc';


        $goods_list = (new GoodsModel())->allowData()
            ->where(['cate_id'=>$two_cate['id']])
            ->order('real_price',$order_price)
            ->order('online_time',$order_online_time)
            ->paginate(1);

//        dump($goods_list);die;

        $this->assign('goods_cate', $goods_cate);
        $this->assign('goods_list',$goods_list);
        $this->assign('one_cate',$one_cate);
        $this->assign('two_cate',$two_cate);
        $this->assign('order_price',$order_price);
        $this->assign('order_online_time',$order_online_time);
        $this->assign('order_price_num',$order_price == 'desc' ? 0 : 1);
        $this->assign('order_price_time_num',$order_online_time == 'desc' ? 0 : 1);

        return $this->fetch();
    }

    public function addShopping(Request $request)
    {
        $user_info = $this->userInfo;
        $post = $request->post();

        $rules = [
            "num" => 'require|number',
            "goods_id" => 'require|number',
        ];

        $message = [
            'num.require' => '请求非法',
            'num.number' => '请求非法',
            'goods_id.require' => '请求非法',
            'goods_id.number' => '请求非法',
        ];

        $validate = new Validate($rules, $message);
        if (!$validate->check($post)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }

        //检查商品状态
        if (!(new GoodsModel())->allowData()->find($post['goods_id'])) {
            return json(['code' => 0, 'msg' => '该商品已下架,无法添加到购物车']);
        }

        $userShopping = (new ShoppingModel())->where(['user_id' => $user_info->id, 'goods_id' => $post['goods_id']]);

        if ($userShopping){
            $userShopping->setInc('num',$post['num']);
        }else{
            $userShopping->insert([
                'user_id'   => $user_info->id,
                'goods_id'  => $post['goods_id'],
                'num'       => $post['num']
            ]);
        }
        return json(['code'=>1,'msg'=>'添加成功']);
    }

}
