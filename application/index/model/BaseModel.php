<?php
/**
 * Created by PhpStorm.
 * User: chen-------------
 * Date: 2018/10/21
 * Time: 20:27
 */
namespace app\index\model;
use think\Model;

class BaseModel extends Model{

    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    protected $resultSetType = 'collection';//设置返回数据集


}