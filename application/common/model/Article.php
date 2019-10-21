<?php

namespace app\common\model;

use think\Model;

class Article extends Model
{
    public function changeStatus($article_id)
    {
        $article = $this->where(['id'=>$article_id])->find();

        $new_status = $article->status ? 0 : 1;

        if ($new_status){
            Category::where(['id'=>$article_id])->setInc('data_sum');
        }else{
            Category::where(['id'=>$article_id])->setDec('data_sum');
        }

        $this->where(['id'=>$article_id])->update(['status'=>$new_status]);

    }

    /**
     * 符合正常查看所需的条件
     * @return \think\db\Query
     */
    public function allowData($alias = '')
    {
        if ($alias){
            return $this->where([
                $alias.'.delete_time' => 0,
                $alias.'.status'    => 1,
            ]);
        }
        return $this->where([
            'delete_time' => 0,
            'status'    => 1,
        ]);
    }
}
