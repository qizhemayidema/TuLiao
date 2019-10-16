<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\model\Category as CategoryModel;
use app\common\model\Article as ArticleModel;
use think\Validate;

class VideoCate extends Base
{
    public $cateType = 4;

    public $cacheName = 'VideoCate';

    public function index()
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
            'name'  => 'require|max:60',
            'order_num' => 'require|between:0,999',
            '__token__'     => 'token',
        ];

        $messages = [
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

        return $this->fetch();
    }

    public function update(Request $request)
    {
        $data = $request->post();

        $rules = [
            'id'        => 'require',
            'name'  => 'require|max:60',
            'order_num' => 'require|between:0,999',
            '__token__'     => 'token',
        ];

        $messages = [
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

        $is_exists = ArticleModel::where(['cate_id'=>$cate_id])->find();

        if ($is_exists) return json(['code'=>0,'msg'=>'有视频正在使用此分类']);

        CategoryModel::where(['id'=>$cate_id])->delete();

        (new CategoryModel())->clearCache($this->cacheName);

        return json(['code'=>1,'msg'=>'success']);

    }
}
