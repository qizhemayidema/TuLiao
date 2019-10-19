<?php

namespace app\index\controller;

use app\common\typeCode\InformationCate;
use app\common\model\Article;
use app\common\model\Category as CateModel;
use app\common\model\Goods;
use app\common\model\Image as Img;
use app\common\typeCode\GoodsCate as GoodsCateController;
use app\common\typeCode\InformationCate as InformationCateController;
use app\common\typeCode\Information as InformationController;
use app\common\model\Goods as GoodsModel;


class Index extends Base
{
    public function index()
    {
        //轮播图
        $roll_pic = Img::where(['type'=>1])->order('order_num','desc')->order('id','desc')->select();

        //资讯轮播图
        $information_roll_pic = Img::where(['type'=>2])->order('order_num','desc')->order('id','desc')->select();

        //资讯二维码
        $information_qr_code_1 = $this->getConfig('index_information_qrCode_1');
        $information_qr_code_2 = $this->getConfig('index_information_qrCode_2');


        //商品分类
        $gc = new GoodsCateController();
        $goods_cate = (new CateModel())->getList($gc->cacheName,$gc->cateType);


        //热门产品
        $hot_product = Article::where(['type'=>1,'status'=>1,'delete_time'=>0])->order('click','desc')
            ->limit(10)->field('id,title')->select();


        //资讯排行前五的文章取出五个
        //资讯分类
        $ic = new InformationCateController();
        $information_cate = CateModel::where(['type'=>$ic->cateType])->order('order_num','desc')
        ->limit(5)->select()->toArray();

        foreach ($information_cate as $key => $value){
            $information_cate[$key]['article'] = Article::where(['type'=>(new InformationController())->articleType])
                ->where(['cate_id'=>$value['id']])->where(['delete_time'=>0,'status'=>1])->limit(5)->select();
        }

        //前8热门商品
        $goods = GoodsModel::where(['status'=>1,'delete_time'=>0])->field('id,title,real_price,pic')
            ->order('click','desc')->limit(8)->select();

        //合作企业
        $business_parters = explode(',',$this->getConfig('business_parters'));

        $this->assign('roll_pic',$roll_pic);
        $this->assign('information_roll_pic',$information_roll_pic);
        $this->assign('information_qr_code_1',$information_qr_code_1);
        $this->assign('information_qr_code_2',$information_qr_code_2);
        $this->assign('goods_cate',$goods_cate);
        $this->assign('hot_product',$hot_product);
        $this->assign('information_cate',$information_cate);
        $this->assign('goods',$goods);
        $this->assign('business_parters',$business_parters);

        return $this->fetch();
    }
}
