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
            switch ($cacheName){
                case 'goodsCate':
                    $res = $this->where(['type'=>$cateType])->order('order_num','desc')->select()->toArray();
                    $cache = $this->getMoreList($res);
                break;
                default :
                    $cache = $this->where(['type'=>$cateType])->order('order_num','desc')->select()->toArray();
                break;
            }
            $cacheObj->set($cacheName,$cache);
        }
        return $cache;
    }

    public function clearCache($cacheName)
    {
        (new Cache(['type'=>config('cache.type')]))->set($cacheName,null);
    }

    private function getMoreList($categorys,$pId = 0,$l = 0)
    {
        $list =array();

        foreach ($categorys as $k=>$v){

            if ($v['p_id'] == $pId){
                unset($categorys[$k]);
                if ($l < 2){
                    //小于三级
                    $v['children'] = $this->getMoreList($categorys,$v['id'],$l+1);
                }
                $list[] = $v;
            }
        }
        return $list;
    }
}
