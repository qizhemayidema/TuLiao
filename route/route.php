<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;


Route::group('/',function(){
    Route::get('/','index/Index/index');
    Route::group('product',function(){
        Route::get('/','index/Product/index')->name('index.product.index');
        Route::get('/:id','index/Product/info')->name('productInfo');

    });
    Route::group('information',function(){
        Route::get('/category/[:id]','index/Information/category')->name('informationCate');
        Route::get('/:id','index/Information/info')->name('informationInfo');
    });
    Route::group('goods',function(){
        Route::get('/category/[:one_cate]/[:two_cate]/[:order]','index/Goods/category')->name('goodsCate');
        Route::get('/:id','index/Goods/info')->name('goodsInfo');
    });
    Route::group('my',function(){
        Route::get('/','index/My/index')->name('my');
        Route::get('/info','index/My/info')->name('index.my.info');
        Route::get('/auth','index/My/auth')->name('index.my.auth');
        Route::get('/both','index/My/both')->name('index.my.both');
        Route::get('/fans','index/My/fans')->name('index.my.fans');
        Route::get('/order','index/My/order')->name('index.my.order');
        Route::get('/shopping','index/My/shopping')->name('index.my.shopping');

        Route::get('/address/create','index/My/addressCreate')->name('index.my.addressCreate');
        Route::get('/address/edit/:id','index/My/addressEdit')->name('index.my.addressEdit');
        Route::get('/address','index/My/address')->name('index.my.address');

        Route::get('/revisePassword','index/My/revisePassword')->name('index.my.revisePassword');
        Route::get('/article/create','index/My/articleCreate')->name('index.my.articleCreate');
        Route::get('/article','index/My/article')->name('index.my.article');
    });
    Route::group('order',function(){
        Route::get('/confirm','index/Order/confirm')->name('index.order.confirm');
    });
    Route::group('forum',function(){
        Route::get('/','index/Forum/index')->name('index.forum.index');
        Route::get('user/:id','index/Forum/user')->name('index.forum.user');
        Route::get('/:id','index/Forum/info')->name('index.forum.info');
    });
    Route::group('video',function(){
        Route::get('/cate/[:id]','index/Video/index')->name('index.video.index');
        Route::get('/:id','index/Video/info')->name('index.video.info');
    });
    Route::group('info',function(){
        Route::get('/:type','index/Footer_article/index')->name('index.footer_article.index');
    });
    Route::get('search','index/Search/index')->name('index.search.index');
});