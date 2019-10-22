<?php

namespace app\common\model;

use app\common\typeCode\Forum;
use think\Model;

class Comment extends Model
{
    public function allowData()
    {
        return $this->where([
            'is_show'   => 1,
        ]);
    }

    public function changeStatus($comment_id)
    {
        $comment = $this->where(['id'=>$comment_id])->find();

        $new_status = $comment->is_show ? 0 : 1;

        if ($comment['type'] == ((new Forum())->commentType)){
            if ($new_status){
                Article::where(['id'=>$comment['public_id']])->setInc('comment_sum');
            }else{
                Article::where(['id'=>$comment['public_id']])->setDec('comment_sum');
            }
        }

        $this->where(['id'=>$comment_id])->update(['is_show'=>$new_status]);

    }
}
