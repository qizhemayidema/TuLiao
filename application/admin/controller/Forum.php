<?php

namespace app\admin\controller;

use app\common\model\Article;
use app\common\model\User as UserModel;
use think\Controller;
use think\Request;

class Forum extends Base
{
    public function index()
    {
        $forumType = new \app\common\typeCode\Forum();
        $article = (new Article())->alias('article')
            ->join('user user','user.id = article.author_id and article.type='.$forumType->articleType)
            ->join('category cate','cate.id = article.cate_id')
            ->where(['article.delete_time'=>0])
            ->field('article.*,cate.name cate_name,user.avatar_url,user.nickname,user.id user_id')
            ->order('article.id','desc')->paginate(15);

        $this->assign('article',$article);

        return $this->fetch();
//            ->where(['type'=>$forumType])
//            ->where(['delete_time'=>0])
    }

    public function delete(Request $request)
    {
        $article_id = $request->post('article_id');

        $articleModel = new Article();

        $article = $articleModel->findOrFail($article_id);

        $articleModel->where(['id'=>$article_id])->update(['delete_time'=>time()]);

        (new UserModel())->where(['id'=>$article['author_id']])->setDec('forum_article_sum');

        return ['code'=>1,'msg'=>'success'];
    }


    public function changeStatus(Request $request)
    {
        $article_id = $request->post('article_id');

        (new Article())->changeStatus($article_id);

        return ['code'=>1,'msg'=>'success'];
    }


}
