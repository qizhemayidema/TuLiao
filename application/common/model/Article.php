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
}
