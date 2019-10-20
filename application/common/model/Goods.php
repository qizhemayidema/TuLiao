<?php

namespace app\common\model;

use think\Model;

class Goods extends Model
{
    public function changeStatus($goods_id)
    {
        $article = $this->where(['id'=>$goods_id])->find();

        $new_status = $article->status ? 0 : 1;

        if ($new_status){
            Category::where(['id'=>$goods_id])->setInc('data_sum');
        }else{
            Category::where(['id'=>$goods_id])->setDec('data_sum');
        }

        $this->where(['id'=>$goods_id])->update(['status'=>$new_status]);
    }

    public function allowData($alias = '')
    {
        if ($alias){
            return $this->where([
                $alias.'.delete_time'   => 0,
                $alias.'.status'        => 1,
            ]);
        }
        return $this->where([
            'delete_time'   => 0,
            'status'        => 1,
        ]);

    }
}
