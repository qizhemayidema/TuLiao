<?php

namespace app\admin\controller;

use app\common\model\Article;
use app\common\model\Comment;
use think\Controller;
use think\Request;
use app\common\model\User as UserModel;

class ForumComment extends Base
{
    public function index()
    {
        $comment = (new Comment())->alias('comment')
            ->join('article article','comment.public_id = article.id and comment.type = 1')
            ->field('article.title,comment.*')
            ->order('comment.id','desc')->paginate(15);

        $this->assign('comment',$comment);

        return $this->fetch();
//            ->where(['type'=>$forumType])
//            ->where(['delete_time'=>0])
    }


    public function changeStatus(Request $request)
    {
        $article_id = $request->post('comment_id');

        (new Comment())->changeStatus($article_id);

        return ['code'=>1,'msg'=>'success'];
    }


}
