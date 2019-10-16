<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\model\Category as CategoryModel;
use app\common\model\Goods as GoodsModel;
use think\Validate;

class GoodsCate extends Base

{
    public $cateType = 5;

    public $cacheName = 'goodsCate';

    public function index(Request $request)
    {
        $categoryModel = new CategoryModel();
        $cate = $categoryModel->getList($this->cacheName,$this->cateType);

        $this->assign('cate',$cate);

        return $this->fetch();
    }

    public function add()
    {

        $pCate = (new CategoryModel())->where(['p_id'=>0,'type'=>$this->cateType])->order('id','desc')->select();

        $this->assign('pCate',$pCate);


        return $this->fetch();
    }

    public function save(Request $request)
    {
        $data = $request->post();

        $rules = [
            'p_id'  => 'require',
            'name'  => 'require|max:60',
            'order_num' => 'require|between:0,999',
            '__token__'     => 'token',
        ];

        $messages = [
            'p_id.require'  => '必须选择一个所属节点',
            'name.require'  => '名称必须填写',
            'name.max'      => '名称最大60个字符',
            'order_num.require' => '排序必须填写',
            'order_num.between' => '排序数字最小为0,最大为999',
            '__token__.token'   => '不能重复提交'
        ];

        $validate = new Validate($rules,$messages);
        if (!$validate->check($data)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $insert = [
            'p_id'  => $data['p_id'],
            'type'  => $this->cateType,
            'name'  => $data['name'],
            'order_num' => $data['order_num'],
        ];

        CategoryModel::insert($insert);

        (new CategoryModel())->clearCache($this->cacheName);
        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $cate_id = $request->param('cate_id');
        $cate = CategoryModel::find($cate_id);

        $this->assign('cate',$cate);

        $pCate = (new CategoryModel())->where(['p_id'=>0,'type'=>$this->cateType])
            ->where('id','<>',$cate_id)->order('id','desc')->select();

        $this->assign('pCate',$pCate);


        return $this->fetch();
    }

    public function update(Request $request)
    {
        $data = $request->post();

        $rules = [
            'id'        => 'require',
            'p_id'      => 'require',
            'name'  => 'require|max:60',
            'order_num' => 'require|between:0,999',
            '__token__'     => 'token',
        ];

        $messages = [
            'p_id.require'  => '必须选择一个父级节点',
            'name.require'  => '名称必须填写',
            'name.max'      => '名称最大60个字符',
            'order_num.require' => '排序必须填写',
            'order_num.between' => '排序数字最小为0,最大为999',
            '__token__.token'   => '不能重复提交'
        ];

        $validate = new Validate($rules,$messages);
        if (!$validate->check($data)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $update = [
            'p_id'  => $data['p_id'],
            'type'  => $this->cateType,
            'name'  => $data['name'],
            'order_num' => $data['order_num'],
        ];

        CategoryModel::where(['id'=>$data['id']])->update($update);

        (new CategoryModel())->clearCache($this->cacheName);

        return json(['code'=>1,'msg'=>'success']);

    }

    public function delete(Request $request)
    {
        $cate_id = $request->post('cate_id');

        $is_exists = GoodsModel::where(['cate_id'=>$cate_id])->find();

        if ($is_exists) return json(['code'=>0,'msg'=>'有商品正在使用此分类']);

        $cate_exists = CategoryModel::where(['p_id'=>$cate_id])->find();

        if ($cate_exists) return json(['code'=>0,'msg'=>'此分类有子分类']);

        CategoryModel::where(['id'=>$cate_id])->delete();

        (new CategoryModel())->clearCache($this->cacheName);

        return json(['code'=>1,'msg'=>'success']);

    }

}