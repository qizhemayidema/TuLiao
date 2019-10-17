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
        Route::get('/:id','index/Product/info')->name('productInfo');

    });
    Route::group('information',function(){
        Route::get('/category/:id','index/Information/category')->name('informationCate');
        Route::get('/:id','index/Information/info')->name('informationInfo');
    });

    Route::group('goods',function(){
        Route::get('/category/[:one_cate]/[:two_cate]/[:order]','index/Goods/category')->name('goodsCate');
        Route::get('/:id','index/Goods/info')->name('goodsInfo');
    });
});