<?php
namespace app\index\controller;
use app\index\model\Access;
use app\index\model\User;
use think\Collection;
use think\Controller;

use think\Request;
use think\Config;
use think\Session;
use app\index\model\User as UserModel;
use app\index\model\User_Role;
use app\index\model\Role_Access;
use app\index\model\Role_Menu;
use app\index\model\App_Access_Log;

class BaseController extends  Controller
{


    protected $beforeActionList = [
        'beforeLogin'=> ['except'=>'forbidden,login_handle,login,logout,tree']
    ];

    protected $ignore_urls=['error/forbidden','user/login_handle','user/login','user/logout','access/tree'];

    /**
     * 登录前检查用户权限
     * @return boolean
     */
    public function beforeLogin(){

        if(!$this->is_login()){
            if(Request::instance()->isAjax()){
                $this->renderJSON(false,"未登陆，请返回登录~~",[],302,0);
            }else{
                $this->redirect('user/login',[]);
            }
        }

        $controller_name = strtolower(Request::instance()->controller());
        $action_name = strtolower(Request::instance()->action());
        $current_url = "/".$controller_name."/".$action_name;
        $user = current_user();
        $user = User::find($user["uid"])->toArray();

        if(in_array($current_url,$this->ignore_urls)){

        }else if($user["is_admin"]){

        }else{
            $user_role = User_Role::find(['uid'=>$user["id"]]);
            $related_role_id = $user_role['role_id'];
            $access_urls = $this->getUserAccess($related_role_id);
            if(!in_array($current_url,$access_urls)){
                if(Request::instance()->isAjax() || Request::instance()->param('is_ajax')){
                    $this->renderJSON(false,"你暂无此权限~~",[],403,0);
                }else{
                    $this->redirect('error/forbidden',[]);
                }
            }
        }


    }

    protected function getUserMenu($related_role_id){
        $menu = [];
        if($related_role_id){
            //菜单中的权限
            $role_menu_list = Role_Menu::all(['role_id'=>$related_role_id]);
            $related_menu_ids = array_column(Collection($role_menu_list)->toArray(),'access_id');
            $nodes = Collection(Access::all($related_menu_ids))->toArray();
            foreach($nodes as &$node){
                $node["url"]=substr($node["url"],1);
            }
            $menu = $this->generateTree($nodes);
        }
        return $menu;
    }

    protected function generateTree($array){
        //第一步 构造数据
        $items = array();
        foreach($array as $value){
            $items[$value['id']] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $tree = array();
        foreach($items as $key => $value){
            if(isset($items[$value['parent_id']])){
                $items[$value['parent_id']]['children'][] = &$items[$key];
            }else{
                $tree[] = &$items[$key];
            }
        }
        return $tree;
    }

    /**
     * 获取当前登陆用户权限
     * @return boolean
     */
    private function getUserAccess($related_role_id){
        $access_urls = [];
        if($related_role_id){
            $role_access_list = Role_Access::all(['role_id'=>$related_role_id]);
            $related_access_ids1 = array_column(Collection($role_access_list)->toArray(),'access_id');
            //菜单中的权限
            $role_menu_list = Role_Menu::all(['role_id'=>$related_role_id]);
            $related_access_ids2 = array_column(Collection($role_menu_list)->toArray(),'access_id');
            $related_access_ids = array_unique(array_merge($related_access_ids1,$related_access_ids2));
            $access_list = Access::all($related_access_ids);
            foreach($access_list as $access){
                if(!empty($access["url"])){
                    $access_urls[]=$access["url"];
                }
            }
        }
        return $access_urls;
    }


    /**
     * 统一返回json数据
     * @access protected
     */
    protected function renderJSON($success,$message ,$rows,$code=200,$total){
        echo json_encode([
            "success"=>$success,
            "message"=>$message,
            "rows"=>$rows,
            "code"=>$code,
            "total"=>$total,
            "req_id"=>uniqid()
        ]);exit();
    }

    /**
     * 检测用户是否登录
     * @access protected
     * @return mixed
     */
    protected function is_login(){
        $user = $this->sessionGet('user');
        if (empty($user)) {
            return false;
        } else {
            return  $this->sessionGet('user_sign') == $this->data_auth_sign($user) ? $user : false;
        }
    }




    /**
     * 数据签名认证
     * @access protected
     * @param  array  $data 被认证的数据
     * @return string       签名
     */
    protected  function data_auth_sign($data) {
        $code = http_build_query($data); //url编码并生成query字符串
        $sign = sha1($code); //生成签名
        return $sign;
    }

    /**
     * 读取session
     * @access protected
     * @param  string  $path 被认证的数据
     * @return mixed
     */
    protected function sessionGet($path =''){
        $session_prefix = Config::get('rbac.session_prefix');
        $user           = Session::get($session_prefix.$path);
        return $user;
    }

}
