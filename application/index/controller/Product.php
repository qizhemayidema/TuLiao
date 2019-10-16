<?php

namespace app\index\controller;

use app\common\model\Article;
use think\Controller;
use think\Request;
use app\common\model\Article as ArticleModel;

class Product extends Base
{
    public $articleType = 1;

    public function info(Request $request)
    {
        //热门产品 以及推荐
        $hot_article = ArticleModel::where(['status'=>1,'delete_time'=>0])
            ->where(['type'=>$this->articleType])->order('click','desc')
            ->limit(8)->field('id,pic,title')->select();

        //合作企业
        $business_parters = explode(',',$this->getConfig('business_parters'));

        //详情
        $article = Article::find($request->param('id'));

        $this->assign('hot_article',$hot_article);

        $this->assign('business_parters',$business_parters);

        $this->assign('article',$article);

        return $this->fetch();
    }
}
