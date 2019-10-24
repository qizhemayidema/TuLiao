<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use app\common\model\Article as ArticleModel;

class Search extends Base
{
    public function index(Request $request)
    {
        $search = $request->param('search') ?? '';
        //热门产品 以及推荐
        $hot_article = ArticleModel::where(['status'=>1,'delete_time'=>0])
            ->where(['type'=>1])->order('click','desc')
            ->limit(8)->field('id,pic,title')->select();

        //合作企业
        $business_parters = explode(',',$this->getConfig('business_parters'));

        $article = ArticleModel::where(['status'=>1,'delete_time'=>0])
            ->where(['type'=>1])->where('title','like','%'.$search.'%')->order('id','desc')
            ->paginate(15,false,['query'=>request()->param() ]);

        $this->assign('hot_article',$hot_article);

        $this->assign('business_parters',$business_parters);

        $this->assign('article',$article);

        $this->assign('search',$search);

        return $this->fetch();
    }
}
