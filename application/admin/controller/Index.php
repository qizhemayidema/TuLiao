<?php

namespace app\Admin\controller;

use think\Controller;
use think\Request;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function firstPage(){}
}
