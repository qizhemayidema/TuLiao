<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Information extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}
