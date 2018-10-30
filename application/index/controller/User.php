<?php
namespace app\index\controller;

use app\index\model\Access;
use app\index\model\User_Role;
use think\model\Collection;
use think\Request;
use think\Config;
use think\Session;
use app\index\model\User as UserModel;
use app\index\model\Role as RoleModel;
use think\Db;
use app\index\model\App_Access_Log;
class User extends  BaseController
{
    public function login()
    {
        if($this->is_login()){
            $user = current_user();
            $user = UserModel::find($user["uid"]);
            if($user["is_admin"]){
                $nodes = Collection(Access::all(["type"=>["in",[1,2]]]))->toArray();
                foreach($nodes as &$node){
                    $node["url"]=substr($node["url"],1);
                }
                $menu = $this->generateTree($nodes);
                $this->assign('menu',$menu);
            }else{
                $user_role = User_Role::find(['uid'=>$user["id"]]);
                $related_role_id = $user_role['role_id'];
                $menu = $this->getUserMenu($related_role_id);
                $this->assign('menu',$menu);
            }
            $app_access_log = new App_Access_Log();
            //记录登陆日志
            $data["uid"] = $user["id"];
            $data["ua"]  = isset($_SERVER['HTTP_USER_AGENT'] )?$_SERVER['HTTP_USER_AGENT']:'';
            $data["ip"] = isset($_SERVER['REMOTE_ADDR'] )?$_SERVER['REMOTE_ADDR']:'';
            $data["created_time"]=date("Y-m-d H:i:s");
            $app_access_log->data($data);
            $app_access_log->save();
        }

        return $this->fetch('login');
    }


    /**
     * 用户登录
     */
    public function login_handle(){
        $name = Request::instance()->param('name');
        $password = Request::instance()->param('password');
        if(!$name){
            $this->renderJSON(false,"用户名不能为空",[],200,0);
        }
        if(!$password){
            $this->renderJSON(false,"密码不能为空",[],200,0);
        }
        $user_list = UserModel::all(["name"=>$name]);
        $user_array = Collection($user_list)->toArray();
        $user_info = $user_array[0];
        if(empty($user_info)){
            $this->renderJSON(true,"用户名或者密码错误",[],200,0);
        }

        if($user_info["password"]==md5($password)){
            $session_prefix = Config::get('rbac.session_prefix');
            $user           = [
                'uid'       => $user_info["id"],
                'name'  => $user_info["name"],
                'time'      => time()
            ];
            Session::set($session_prefix.'user',$user);
            Session::set($session_prefix.'user_sign',$this->data_auth_sign($user));
            $this->renderJSON(true,"登录成功",[],200,0);
        }else{
            $this->renderJSON(false,"用户名或者密码错误",[],200,0);
        }
    }

    public function logout(){
        $session_prefix = Config::get('rbac.session_prefix');
        Session::delete($session_prefix.'user');
        Session::set($session_prefix.'user_sign');
        $this->redirect('user/login',[]);
    }

    public function index()
    {
        if(Request::instance()->isGet()){
            $role_list = RoleModel::all();
            $this->assign('role_list',$role_list);
            return $this->fetch('index');
        }else{
            $page=input('post.page');
            $rows = input('post.rows');
            $offset=($page-1)*$rows;
            $model = new UserModel();
            $rows = $model->limit($rows,$offset)->select()->toArray();
            $total = Collection(UserModel::all())->count();

            foreach($rows as &$row){
                $user_role_list = User_Role::all(['uid'=>$row['id']]);
                $related_role_ids = join(',',array_column(Collection($user_role_list)->toArray(),'role_id'));
                $row['related_role_ids'] = $related_role_ids;
            }
            $this->renderJSON(true,"ok",$rows,200,$total);
        }
    }

    public function create(){
        //添加
        $data= input("post.");
        // 启动事务
        Db::startTrans();
        try{
            $user = new UserModel();
            $r = $user->where('name', $data["name"])->find();
            if(!empty($r)){
                $this->renderJSON(false,"用户名已存在~~",[],-1,0);
            }
            $user->data(["name"=>$data["name"],"email"=>$data["email"],"status"=>1,"is_admin"=>0,"created_time"=>date("Y-m-d H:i:s")]);
            $user->save();
            $user_role = new User_Role();
            $list = [];
            foreach($data["role_ids"] as $role_id){
                $list[] = ["uid"=>$user->id,"role_id"=>$role_id,"created_time"=>date("Y-m-d H:i:s")];
            }
            $user_role->saveAll($list);
            // 提交事务
            Db::commit();
            $this->renderJSON(true,"操作成功~~",[],200,0);

        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->renderJSON(false,"操作失败~~",[],-1,0);
        }
    }



    public function edit(){
        //修改
        $data = input("post.");
        $id= input("post.id");
        $data["updated_time"]=date("Y-m-d H:i:s");

        //print_r($data);die;
        // 启动事务
        Db::startTrans();
        try{

            $user = new UserModel();
            $user->allowField(true)->save($data,['id' => $id]);

            $user_role_list = User_Role::all(['uid'=>$id]);
            $array_A = array_column(Collection($user_role_list)->toArray(),'role_id');
            $array_B = empty($data["role_ids"])?array():$data["role_ids"];
            $array_C = array_diff($array_A,$array_B);
            $array_D = array_diff($array_B,$array_A);
            $user_role = new User_Role();
            if(!empty($array_C)){
                $user_role_ids =[];
                foreach($array_C as $role_id) {
                    $list = User_Role::all(['uid' => $id, "role_id" => $role_id]);
                    $ids = array_column(Collection($list)->toArray(), 'id');
                    $user_role_ids = array_merge($user_role_ids, $ids);
                }
                User_Role::destroy($user_role_ids);
            }

            if(!empty($array_D)){
                $list = [];
                foreach($array_D as $role_id){
                    $list[] = ["uid"=>$id,"role_id"=>$role_id,"created_time"=>date("Y-m-d H:i:s")];
                }
                $user_role->saveAll($list);
            }

            // 提交事务
            Db::commit();
            $this->renderJSON(true,"操作成功~~",[],200,0);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->renderJSON(false,"操作失败~~",[],-1,0);
        }
    }








}
