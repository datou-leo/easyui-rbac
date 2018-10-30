<?php
namespace app\index\controller;
use app\index\model\Access;
use think\Controller;
use think\Request;
use app\index\model\Role as RoleModel;
use app\index\model\Access as AccessModel;
use app\index\model\Role_Access;
use app\index\model\Role_Menu;
use think\Db;

class Role extends  BaseController
{
    public function index()
    {
        if(Request::instance()->isGet()){
            return $this->fetch('index');
        }else{
            $page=input('post.page');
            $rows = input('post.rows');
            $offset=($page-1)*$rows;
            $model = new RoleModel();
            $rows = $model->limit($rows,$offset)->select()->toArray();
            $total = Collection(RoleModel::all())->count();

            $model = new AccessModel();
            $list = $model->where('status', 1)->field("id,parent_id,title as text")->select()->toArray();

            foreach($rows as &$row){
                $role_access_list = Role_Access::all(['role_id'=>$row['id']]);
                $related_access_ids = array_column(Collection($role_access_list)->toArray(),'access_id');
                $list_=[];
                foreach($list as $l){
                    $l["checked"]=in_array($l['id'],$related_access_ids)?true:false;
                    $list_[] = $l;
                }
                $access_array = $this->generateTree($list_);
                $access_json = json_encode($access_array);
                $row["access_json"]= $access_json;
            }
            $this->renderJSON(true,"ok",$rows,200,$total);
        }
    }

    public function create(){
        //添加
        $data= input("post.");
        $data["status"]=1;
        $data["created_time"]=date("Y-m-d H:i:s");
        $role = new RoleModel();
        $r = $role->where('name', $data["name"])->find();
        if(!empty($r)){
            $this->renderJSON(false,"角色名已存在~~",[],-1,0);
        }
        $role->data($data);
        $role->save();
        $this->renderJSON(true,"操作成功~~",[],200,0);
    }


    public function edit(){
        //修改
        $name = input("post.name");
        $id =input("post.id");
        $data["name"]=$name;
        $data["updated_time"]=date("Y-m-d H:i:s");
        $role = new RoleModel();
        $r = $role->where('name', $data["name"])->where("id!=$id")->find();
        if(!empty($r)){
            $this->renderJSON(false,"角色名已存在~~",[],-1,0);
        }
        $role->allowField(true)->save($data,['id' => $id]);
        $this->renderJSON(true,"操作成功~~",[],200,0);
    }

    public function access(){
        // 启动事务
        Db::startTrans();
        try{
            $data = input("post.");
            $id= input("post.id");

            $role_access_list = Role_Access::all(['role_id'=>$id]);

            $array_A = array();
            if(!empty($role_access_list)){
                $array_A = array_column(Collection($role_access_list)->toArray(),'access_id');
            }
            $array_B = empty($data["access_ids"])?array():explode(',',$data["access_ids"]);
            $array_C = array_diff($array_A,$array_B);
            $array_D = array_diff($array_B,$array_A);
            $role_access = new Role_Access();

            if(!empty($array_C)){
                $role_access_ids =[];
                foreach($array_C as $access_id) {
                    $list = Role_Access::all(['role_id' => $id, "access_id" => $access_id]);
                    $ids = array_column(Collection($list)->toArray(), 'id');
                    $role_access_ids = array_merge($role_access_ids, $ids);
                }
                Role_Access::destroy($role_access_ids);
            }

            if(!empty($array_D)){
                $list = [];
                foreach($array_D as $access_id){
                    $list[] = ["role_id"=>$id,"access_id"=>$access_id,"created_time"=>date("Y-m-d H:i:s")];
                }
                $role_access->saveAll($list);
            }

            //设置菜单
            $role_menu_list = Role_Menu::all(["role_id"=>$id]);
            $arr_A = array();
            if(!empty($role_access_list)){
                $arr_A = array_column(Collection($role_menu_list)->toArray(),'access_id');
            }
            //过滤掉仅为权限的节点
            $model = new AccessModel();
            $list = $model->where('status', 1)->where("(type=1 OR type=2)")->field("id,parent_id,title as text")->select()->toArray();

            $arr=empty($data["menu_access_ids"])?array():explode(',',$data["menu_access_ids"]);
            $all_menu_ids = array_column($list,'id');
            $arr_B = array_intersect($all_menu_ids,$arr);

            $arr_C = array_diff($arr_A,$arr_B);
            $arr_D = array_diff($arr_B,$arr_A);
            $role_menu = new Role_Menu();

            if(!empty($arr_C)){
                $role_menu_ids =[];
                foreach($arr_C as $access_id) {
                    $list = Role_Menu::all(['role_id' => $id, "access_id" => $access_id]);
                    $ids = array_column(Collection($list)->toArray(), 'id');
                    $role_menu_ids = array_merge($role_menu_ids, $ids);
                }
                Role_Menu::destroy($role_menu_ids);
            }

            if(!empty($arr_D)){
                $list = [];
                foreach($arr_D as $access_id){
                    $list[] = ["role_id"=>$id,"access_id"=>$access_id,"created_time"=>date("Y-m-d H:i:s")];
                }
                $role_menu->saveAll($list);
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
