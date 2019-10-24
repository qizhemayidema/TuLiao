<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Route;
use app\common\model\Article as ArticleModel;
use app\common\model\Category as CategoryModel;

class Information extends Base
{
    public $articleType = 2;

    public $articleCateType = 2;

    public $articleCacheName = 'informationCate';

    //详情页
    public function info(Request $request)
    {
        $id = $request->param('id');

        //热门产品
        $hot_article = ArticleModel::where(['status'=>1,'delete_time'=>0])
            ->where(['type'=>(new Product())->articleType])->order('click','desc')
            ->limit(8)->field('id,pic,title')->select();

        //推荐
        $tj = ArticleModel::where(['status'=>1,'delete_time'=>0])
            ->where(['type'=>$this->articleCateType])->order('click','desc')
            ->limit(8)->field('id,pic,title')->select();

        //合作企业
        $business_parters = explode(',',$this->getConfig('business_parters'));

        //分类
        $cate = (new CategoryModel())->getList($this->articleCacheName,$this->articleCateType);

        //banner
        $banner = $this->getConfig('information_banner');

        //详情
        $article = ArticleModel::find($id);
        if ($article) ArticleModel::where(['id'=>$request->param('id')])->setInc('click');

        $now_cate = [];

        foreach ($cate as $key => $value){
            if ($value['id'] == $article['cate_id']){
                $now_cate = $value;
            }
        }

        $this->assign('hot_article',$hot_article);
        $this->assign('business_parters',$business_parters);
        $this->assign('cate',$cate);
        $this->assign('banner',$banner);
        $this->assign('article',$article);
        $this->assign('now_cate',$now_cate);
        $this->assign('tj',$tj);

        return $this->fetch();
    }

    public function category(Request $request)
    {
        $cate_id = $request->param('id');

        //热门产品
        $hot_article = ArticleModel::where(['status'=>1,'delete_time'=>0])
            ->where(['type'=>(new Product())->articleType])->order('click','desc')
            ->limit(8)->field('id,pic,title')->select();

        //推荐
        $tj = ArticleModel::where(['status'=>1,'delete_time'=>0])
            ->where(['type'=>$this->articleCateType])->order('click','desc')
            ->limit(8)->field('id,pic,title')->select();

        //合作企业
        $business_parters = explode(',',$this->getConfig('business_parters'));

        //分类
        $cate = (new CategoryModel())->getList($this->articleCacheName,$this->articleCateType);

        if (!$cate_id){
            if (isset($cate[0])){
                $cate_id = $cate['0']['id'];
            }else{
                $cate_id = 0;
            }
        }
        //banner
        $banner = $this->getConfig('information_banner');

        //详情
        $article_list = (new ArticleModel())->allowData()->where(['cate_id'=>$cate_id])
            ->field('id,title,desc,pic')->order('id','desc')->paginate(5);

        $now_cate = [];

        foreach ($cate as $key => $value){
            if ($value['id'] == $cate_id){
                $now_cate = $value;
                break;
            }
        }

        $this->assign('hot_article',$hot_article);
        $this->assign('business_parters',$business_parters);
        $this->assign('cate',$cate);
        $this->assign('banner',$banner);
        $this->assign('article_list',$article_list);
        $this->assign('now_cate',$now_cate);
        $this->assign('tj',$tj);

        return $this->fetch();
    }
}
