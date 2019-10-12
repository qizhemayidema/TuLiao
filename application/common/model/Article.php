<?php

namespace app\common\model;

use think\Model;

class Article extends Model
{
    public function changeStatus($article_id)
    {
        $article = $this->where(['id'=>$article_id])->find();

        $new_status = $article->status ? 0 : 1;

        $this->where(['id'=>$article_id])->update(['status'=>$new_status]);

    }
}
