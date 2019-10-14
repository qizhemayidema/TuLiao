<?php

namespace app\admin\controller;

use app\common\controller\Upload;
use app\common\model\Article as ArticleModel;
use app\common\model\Category as CateModel;
use think\Controller;
use think\Request;
use think\Validate;

class Information extends Base
{
    public $articleType = '2';

    public function index()
    {

       $article = ArticleModel::alias('article')
            ->join('category cate','article.cate_id = cate.id')
            ->where(['article.type'=>$this->articleType,'article.delete_time'=>0])
            ->field('cate.name cate_name,article.id,article.title,article.click,article.create_time')
            ->field('article.pic,article.status,article.like_sum')
            ->paginate(20);

        $this->assign('article',$article);
        return $this->fetch();
    }

    public function add()
    {
        $cateController = new InformationCate();

        $cate = (new CateModel())->getList($cateController->cacheName,$cateController->cateType);

        $this->assign('cate',$cate);

        return $this->fetch();
    }

    public function save(Request $request)
    {
        $data = $request->post();
        $rules = [
            'cate_id'   => 'require',
            'title' => 'require|max:60',
            'desc'  => 'require|max:300',
            'pic'   => 'require',
            'content' => 'require',
            '__token__' => 'token',
        ];

        $message = [
            'cate_id.require'   =>   '必须选择分类',
            'title.require'     =>   '标题必须填写',
            'title.max'         =>   '标题最大60个字符',
            'desc.require'      =>   '介绍必须填写',
            'desc.max'          =>   '介绍最大300个字符',
            'pic.require'       =>   '封面图必须上传',
            'content.require'   =>   '内容必须填写',
            '__token__.token'   =>   '不能重复提交'
        ];

        $validate = new Validate($rules,$message);

        if (!$validate->check($data)){

            return json(['code'=>0,'msg'=>$validate->getError()]);

        }

        $insert = [
            'cate_id'   => $data['cate_id'],
            'title'     => $data['title'],
            'desc'      => $data['desc'],
            'pic'       => $data['pic'],
            'content'   => $data['content'],
            'type'      => $this->articleType,
            'create_time' => time(),
        ];

        ArticleModel::insert($insert);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $article_id = $request->param('article_id');

        $article = ArticleModel::find($article_id);

        $cateController = new InformationCate();

        $cate = (new CateModel())->getList($cateController->cacheName,$cateController->cateType);

        $this->assign('cate',$cate);

        $this->assign('article',$article);

        return $this->fetch();
    }

    public function update(Request $request)
    {
        $data = $request->post();

        $rules = [
            'id'    => 'require',
            'cate_id'   => 'require',
            'title' => 'require|max:60',
            'desc'  => 'require|max:300',
            'content' => 'require',
            '__token__' => 'token',
        ];

        $message = [
            'id.require'        =>   '非法操作',
            'cate_id.require'   =>   '必须选择一个分类',
            'title.require'     =>   '标题必须填写',
            'title.max'         =>   '标题最大60个字符',
            'desc.require'      =>   '介绍必须填写',
            'desc.max'          =>   '介绍最大300个字符',
            'content.require'   =>   '内容必须填写',
            '__token__.token'   =>   '不能重复提交'
        ];

        $validate = new Validate($rules,$message);

        if (!$validate->check($data)){

            return json(['code'=>0,'msg'=>$validate->getError()]);

        }

        $update = [
            'cate_id'   => $data['cate_id'],
            'title'     => $data['title'],
            'desc'      => $data['desc'],
            'content'   => $data['content'],
            'type'      => $this->articleType,
            'create_time' => time(),
        ];
        if ($data['pic']) $update['pic'] = $data['pic'];

        ArticleModel::where(['id'=>$data['id']])->update($update);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function delete(Request $request)
    {
        $article_id = $request->post('article_id');

        ArticleModel::where(['id'=>$article_id])->update(['delete_time'=>time()]);

        return ['code'=>1,'msg'=>'success'];
    }

    public function changeStatus(Request $request)
    {
        $article_id = $request->post('article_id');

        (new ArticleModel())->changeStatus($article_id);

        return ['code'=>1,'msg'=>'success'];
    }

    //上传课程封面
    public function uploadPic()
    {
        $path = 'information/';
        return (new Upload())->uploadOnePic($path);
    }

}
