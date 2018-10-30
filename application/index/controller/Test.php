<?php
namespace app\index\controller;
use think\Controller;

class Test extends  BaseController
{
    public function page1()
    {
        return $this->fetch('page1');
    }

    public function page2()
    {
        return $this->fetch('page2');
    }

    public function page3()
    {
        return $this->fetch('page3');
    }

    public function page4()
    {
        return $this->fetch('page4');
    }

}
