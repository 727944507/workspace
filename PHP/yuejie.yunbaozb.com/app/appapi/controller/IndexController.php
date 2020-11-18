<?php


namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class IndexController extends HomeBaseController
{
    public function index()
    {
        return $this->fetch(':index');
    }

    public function ws()
    {
        return $this->fetch(':ws');
    }
}
