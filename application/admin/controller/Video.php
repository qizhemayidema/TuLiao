<?php

namespace app\admin\controller;

use app\common\controller\Upload;
use app\common\model\Category;
use think\Controller;
use think\Request;
use app\common\model\Article as ArticleModel;
use think\Validate;

class Video extends Base
{
    public function index()
    {
        $articleModel = (new ArticleModel());
        $videoType = (new \app\common\typeCode\Video())->articleType;
        $video = $articleModel->alias('article')
            ->join('category cate','article.cate_id = cate.id and article.type = '.$videoType)->where([
            'article.delete_time'=>0,
        ])->order('article.id','desc')
            ->field('article.*,cate.name cate_name')->paginate(15);

        $this->assign('video',$video);
        return $this->fetch();
    }

    public function add()
    {
        $videoCate = (new \app\common\typeCode\VideoCate());

        $cateType =  $videoCate->cateType;
        $cacheName = $videoCate->cacheName;

        $cate = (new Category())->getList($cacheName,$cateType);

        $this->assign('cate',$cate);

        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post = $request->post();
        $rules = [
            'pic'   => 'require',
            'title' => 'require|max:30',
            'desc'  => 'require',
            'source_url' => 'require',
            'cate_id'   => 'require',
        ];
        $messages = [
            'pic.require'   => '封面图必须上传',
            'title.require' => '标题必须填写',
            'title.max'     => '标题最大长度为30字符',
            'source_url.require'    => '必须上传视频',
            'desc.require'  => '简介必须填写',
            'cate_id.require'   => '必须选择一个分类',
        ];

        $validate = new Validate($rules,$messages);
        if (!$validate->check($post)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $insert = [
            'cate_id'   => $post['cate_id'],
            'title'   => $post['title'],
            'source_url'   => $post['source_url'],
            'pic'   => $post['pic'],
            'type'  => (new \app\common\typeCode\Video())->articleType,
            'content' => "",
            'desc'  => $post['desc'],
            'create_time' => time(),
        ];

        (new ArticleModel())->insert($insert);

        return json(['code'=>1,'msg'=>'success']);

    }

    public function edit(Request $request)
    {
        $id = $request->param('id');
        $videoCate = (new \app\common\typeCode\VideoCate());

        $cateType =  $videoCate->cateType;
        $cacheName = $videoCate->cacheName;

        $cate = (new Category())->getList($cacheName,$cateType);

        $video = (new ArticleModel())->where(['id'=>$id])->find();

        $this->assign('cate',$cate);

        $this->assign('video',$video);


        return $this->fetch();
    }

    public function update(Request $request)
    {
        $post = $request->post();
        $rules = [
            'id'    => 'require',
            'pic'   => 'require',
            'title' => 'require|max:30',
            'desc'  => 'require',
            'source_url' => 'require',
            'cate_id'   => 'require',
        ];
        $messages = [
            'pic.require'   => '封面图必须上传',
            'title.require' => '标题必须填写',
            'title.max'     => '标题最大长度为30字符',
            'source_url.require'    => '必须上传视频',
            'desc.require'  => '简介必须填写',
            'cate_id.require'   => '必须选择一个分类',
        ];


        $validate = new Validate($rules,$messages);
        if (!$validate->check($post)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $update = [
            'cate_id'   => $post['cate_id'],
            'title'   => $post['title'],
            'source_url'   => $post['source_url'],
            'pic'   => $post['pic'],
            'desc'  => $post['desc'],
        ];

        (new ArticleModel())->where(['id'=>$post['id']])->update($update);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function delete(Request $request)
    {
        $id = $request->param('id');

        (new ArticleModel())->where(['id'=>$id])
            ->update(['delete_time'=>time()]);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function uploadVideo()
    {
        return (new Upload())->uploadOneVideo('video/');
    }

}