<?php

namespace app\admin\controller;

use app\common\controller\Upload;
use app\common\model\Category;
use app\common\model\Goods as GoodsModel;
USE app\common\model\Category as CateModel;
use think\Controller;
use think\Request;
use think\Validate;

class Goods extends Base
{
    public function index()
    {
        $goods = \app\common\model\Goods::alias('goods')
            ->where(['goods.delete_time'=>0])
            ->join('category cate','goods.cate_id = cate.id')
            ->field('goods.id,goods.title,goods.pic,goods.real_price,goods.status,goods.create_time,goods.online_time,cate.name cate_name')
            ->paginate(15);

        $this->assign('goods',$goods);

        return $this->fetch();
    }

    public function add()
    {
        $cateController = new GoodsCate();

        $category = (new Category())->getList($cateController->cacheName,$cateController->cateType);

        $this->assign('cate',$category);

        return $this->fetch();
    }

    public function save(Request $request)
    {
        $data = $request->post();

        $rules = [
            'cate_id'   => 'require',
            'title' => 'require|max:60',
            'price' => 'require|number',
            'real_price' => 'require|number',
            'desc'  => 'require|max:300',
            'online_time' => 'require',
            'pic'   => 'require',
            'roll_pic'  => 'require',
            'content' => 'require',
            '__token__' => 'token',
        ];

        $message = [
            'cate_id.require'   =>   '二级分类必须选择',
            'title.require'     =>   '标题必须填写',
            'title.max'         =>   '标题最大60个字符',
            'price.require'     =>   '售价必须填写',
            'price.number'      =>   '售价必须为数字',
            'real_price.require'=>   '真实售价必须填写',
            'real_price.number' =>   '真实售价必须为数字',
            'desc.require'      =>   '介绍必须填写',
            'desc.max'          =>   '介绍最大300个字符',
            'online_time.require' => '上架时间必须填写',
            'pic.require'       =>   '封面图必须上传',
            'roll_pic.require'  =>   '轮播图必须上传',
            'content.require'   =>   '内容必须填写',
            '__token__.token'   =>   '不能重复提交'
        ];

        $validate = new Validate($rules,$message);

        if (!$validate->check($data)){

            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $cate = Category::where(['id'=>$data['cate_id']])->find();

        if ($cate['p_id'] == 0){
            return json(['code'=>0,'msg'=>'必须为二级分类']);
        }

        $insert = [
            'cate_id'   => $data['cate_id'],
            'title'     => $data['title'],
            'desc'      => $data['desc'],
            'pic'       => $data['pic'],
            'roll_pic'  => $data['roll_pic'],
            'price'     => $data['price'],
            'content'   => $data['content'],
            'real_price' => $data['real_price'],
            'create_time' => time(),
            'online_time' => strtotime($data['online_time']),
        ];

        GoodsModel::insert($insert);

        Category::where(['id'=>$data['cate_id']])->setInc('data_sum');

        (new Category())->clearCache((new GoodsCate())->cacheName);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $goods_id = $request->param('goods_id');
        $goods = GoodsModel::where(['id'=>$goods_id])->find();

        $goodsCate = (new GoodsCate());

        $cateType = $goodsCate->cateType;
        $cacheName = $goodsCate->cacheName;

        $cate = (new CateModel())->getList($cacheName,$cateType);

        $this->assign('cate',$cate);
        $this->assign('goods',$goods);

        return $this->fetch();

    }

    public function update(Request $request)
    {
        $data = $request->post();

        $rules = [
            'id'        => 'require',
            'cate_id'   => 'require',
            'title' => 'require|max:60',
            'price' => 'require|float',
            'real_price' => 'require|float',
            'desc'  => 'require|max:300',
            'online_time' => 'require',
            'roll_pic'  => 'require',
            'content' => 'require',
            '__token__' => 'token',
        ];

        $message = [
            'cate_id.require'   =>   '二级分类必须选择',
            'title.require'     =>   '标题必须填写',
            'title.max'         =>   '标题最大60个字符',
            'price.require'     =>   '售价必须填写',
            'price.float'      =>   '售价必须为数字',
            'real_price.require'=>   '真实售价必须填写',
            'real_price.float' =>   '真实售价必须为数字',
            'desc.require'      =>   '介绍必须填写',
            'desc.max'          =>   '介绍最大300个字符',
            'online_time.require' => '上架时间必须填写',
            'roll_pic.require'  =>   '轮播图必须上传',
            'content.require'   =>   '内容必须填写',
            '__token__.token'   =>   '不能重复提交'
        ];

        $validate = new Validate($rules,$message);

        if (!$validate->check($data)){

            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $cate = Category::where(['id'=>$data['cate_id']])->find();

        if ($cate['p_id'] == 0){
            return json(['code'=>0,'msg'=>'必须为二级分类']);
        }

        $update = [
            'cate_id'   => $data['cate_id'],
            'title'     => $data['title'],
            'desc'      => $data['desc'],
            'roll_pic'  => $data['roll_pic'],
            'price'     => $data['price'],
            'real_price' => $data['real_price'],
            'content'   => $data['content'],
            'online_time' => strtotime($data['online_time']),
        ];

        if (isset($data['pic']) && $data['pic']) $update['pic'] = $data['pic'];

        $old_data = GoodsModel::where(['id' => $data['id']])->find();

        Category::where(['id'=>$old_data['cate_id']])->setDec('data_sum');

        GoodsModel::where(['id' => $data['id']])->update($update);

        Category::where(['id'=>$data['cate_id']])->setInc('data_sum');

        (new Category())->clearCache((new GoodsCate())->cacheName);


        return json(['code'=>1,'msg'=>'success']);
    }

    public function changeStatus(Request $request)
    {
        $goods_id = $request->post('goods_id');

        (new GoodsModel())->changeStatus($goods_id);

        return ['code'=>1,'msg'=>'success'];
    }

    public function delete(Request $request)
    {
        $goods_id = $request->post('goods_id');

        $old_data = GoodsModel::where(['id' => $goods_id])->find();

        GoodsModel::where(['id'=>$goods_id])->update(['delete_time'=>time()]);

        Category::where(['id'=>$old_data['cate_id']])->setDec('data_sum');

        (new Category())->clearCache((new GoodsCate())->cacheName);



        return ['code'=>1,'msg'=>'success'];
    }


    //上传课程封面
    public function uploadPic()
    {
        $path = 'goods/';
        return (new Upload())->uploadOnePic($path);
    }


}
