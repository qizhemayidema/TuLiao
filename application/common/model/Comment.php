<?php

namespace app\common\model;

use think\Model;

class Comment extends Model
{
    public function allowData()
    {
        return $this->where([
            'is_show'   => 1,
        ]);
    }
}
