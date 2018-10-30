<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Config;
use think\Session;
/**
 * 获取当前登录的前台用户的信息，未登录时，返回false
 * @return array|boolean
 */
function current_user()
{
    $session_prefix = Config::get('rbac.session_prefix');
    $user           = Session::get($session_prefix.'user');
    if (!empty($user)) {
        return $user;
    } else {
        return false;
    }
}

function parseUrls1($urls){
    $arr = json_decode($urls,true);
    if(count($arr)>1){
        return join("<br />",$arr);
    }else{
        return $arr[0];
    }
}

function parseUrls2($urls){
    $arr = json_decode($urls,true);
    if(count($arr)>1){
        return join("\n",$arr);
    }else{
        return $arr[0];
    }
}