<?php

namespace app\common\model;

use think\Cache;
use think\Model;

class Category extends Model
{
    public function getList($cacheName,$cateType)
    {
        $cacheObj = (new Cache(['type'=>config('cache.type')]));
        $cache = $cacheObj->get($cacheName);
        if (!$cache){
            $cache = $this->where(['type'=>$cateType])->order('order_num','desc')->select();
            $cacheObj->set($cacheName,$cache);
        }
        return $cache;
    }

    public function clearCache($cacheName)
    {
        (new Cache(['type'=>config('cache.type')]))->set($cacheName,null);
    }
}
