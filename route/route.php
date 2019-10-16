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
        Route::get('/:id','index/Information/info')->name('informationInfo');
    });
});