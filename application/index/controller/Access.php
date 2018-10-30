<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use app\index\model\Access as AccessModel;

class Access extends  BaseController
{
    public function index()
    {
        if(Request::instance()->isGet()){
            return $this->fetch('index');
        }else{
            $page=input('post.page');
            $rows = input('post.rows');
            $parent_id = input('post.parent_id');
            $offset=($page-1)*$rows;
            $model = new AccessModel();
            if(empty($parent_id)){
                $rows = [];
                $total=0;
            }else{
                $rows = $model->where('status', 1)->where("parent_id=$parent_id")->limit($rows,$offset)->select()->toArray();
                $total = Collection(AccessModel::all())->count();
            }
            $this->renderJSON(true,"ok",$rows,200,$total);
        }
    }

    public function tree(){
        $model = new AccessModel();
        $list = $model->where('status', 1)->where("(type=1 OR type=2)")->field("id,parent_id,title as text")->select()->toArray();
        $tree = $this->generateTree($list);
        echo json_encode($tree);
    }

    //添加
    public function create(){
        $data= input("post.");
        $data["status"]=1;
        $data["created_time"]=date("Y-m-d H:i:s");
        $access = new AccessModel();
        $a = $access->where('title', $data["title"])->find();
        if(!empty($a)){
            $this->renderJSON(false,"名字已存在~~",[],-1,0);
        }

        $a = $access->where('url', $data["url"])->find();
        if(!empty($a)){
            $this->renderJSON(false,"路径已存在~~",[],-1,0);
        }
        $access->data($data);
        $access->save();
        $this->renderJSON(true,"操作成功~~",[],200,0);
    }
    //修改
    public function edit(){
        $id= input("post.id");
        $title = input("post.title");
        $url = input("post.url");
        $type = input("post.type");
        $data["title"]=$title;
        $data["url"]=$url;
        $data["type"]=$type;
        $data["updated_time"]=date("Y-m-d H:i:s");
        $access = new AccessModel();
        $access->allowField(true)->save($data,['id' => $id]);
        $this->renderJSON(true,"操作成功~~",[],200,0);
    }




}
