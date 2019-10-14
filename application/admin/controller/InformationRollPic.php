<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\model\Image as ImageModel;
use think\Validate;

class InformationRollPic extends Base
{
    public $picType = '2';

    public function index()
    {
        $image = (new ImageModel())->where(['type'=>$this->picType])
            ->order('order_num','desc')->paginate(20);

        $this->assign('image',$image);

        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $data = $request->post();
        $rules = [
            'title' => 'require|max:60',
            'url'  => 'require|max:256',
            'pic'   => 'require',
            'order_num' => 'require|integer',
            '__token__' => 'token',
        ];

        $message = [
            'title.require'     =>   '标题必须填写',
            'title.max'         =>   '标题最大60个字符',
            'url.require'      =>   '跳转链接必须填写',
            'url.max'          =>   '跳转链接最大256个字符',
            'pic.require'       =>   '图片必须上传',
            'order_num.require' =>   '排序数字必须填写',
            'order_num.integer' =>   '排序数字必须为数字',
            '__token__.token'   =>   '不能重复提交'
        ];

        $validate = new Validate($rules,$message);

        if (!$validate->check($data)){

            return json(['code'=>0,'msg'=>$validate->getError()]);

        }

        $insert = [
            'title'     => $data['title'],
            'url'      => $data['url'],
            'img'       => $data['pic'],
            'order_num'   => $data['order_num'],
            'type'      => $this->picType,
        ];

        ImageModel::insert($insert);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $img_id = $request->param('img_id');

        $image = ImageModel::find($img_id);

        $this->assign('image',$image);

        return $this->fetch();
    }

    public function update(Request $request)
    {
        $data = $request->post();
        $rules = [
            'id'    => 'require',
            'title' => 'require|max:60',
            'url'  => 'require|max:256',
            'order_num' => 'require|integer',
            '__token__' => 'token',
        ];

        $message = [
            'title.require'     =>   '标题必须填写',
            'title.max'         =>   '标题最大60个字符',
            'url.require'      =>   '跳转链接必须填写',
            'url.max'          =>   '跳转链接最大256个字符',
            'order_num.require' =>   '排序数字必须填写',
            'order_num.integer' =>   '排序数字必须为数字',
            '__token__.token'   =>   '不能重复提交'
        ];

        $validate = new Validate($rules,$message);

        if (!$validate->check($data)){

            return json(['code'=>0,'msg'=>$validate->getError()]);

        }

        $update = [
            'title'     => $data['title'],
            'url'      => $data['url'],
            'order_num'   => $data['order_num'],
            'type'      => $this->picType,
        ];
        if ($data['pic']) $update['img'] = $data['pic'];

        ImageModel::where(['id'=>$data['id']])->update($update);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function delete(Request $request)
    {
        $img_id = $request->post('img_id');

        (new ImageModel())->where(['id'=>$img_id])->delete();

        return ['code'=>1,'msg'=>'success'];
    }
}
