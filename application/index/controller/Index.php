<?php
namespace app\index\controller;
use think\Controller;

class Index extends  BaseController
{
    public function index()
    {
        return $this->fetch('index');
    }
}
