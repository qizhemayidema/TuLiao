<?php

namespace app\Admin\controller;

use app\http\middleware\AdminLoginCheck;
use think\Controller;
use think\Request;

class Base extends Controller
{
    protected $middleware = [
        AdminLoginCheck::class,
    ];
}
