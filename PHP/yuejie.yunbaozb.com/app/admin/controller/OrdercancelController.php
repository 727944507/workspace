<?php

/* 订单取消原因 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class OrdercancelController extends AdminBaseController
{

    public function index()
    {
        
        $list = Db::name('orders_cancel')
            ->order("list_order asc")
            ->paginate(20);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);

        return $this->fetch();
    }


    public function add()
    {
        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            
            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $map[]=['name','=',$name];
            $isexist = DB::name('orders_cancel')->where($map)->find();
            if($isexist){
                $this->error('同名已存在');
            }

            $id = DB::name('orders_cancel')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            $this->resetcache();
            $this->success("添加成功！");
        }
    }

    public function edit()
    {
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('orders_cancel')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();

            $id=$data['id'];
            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $map[]=['name','=',$name];
            $map[]=['id','<>',$id];
            $isexist = DB::name('orders_cancel')->where($map)->find();
            if($isexist){
                $this->error('同名已存在');
            }

            $rs = DB::name('orders_cancel')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
            $this->resetcache();
            $this->success("保存成功！");
        }
    }
    
    public function listOrder()
    {
        $model = DB::name('orders_cancel');
        parent::listOrders($model);
        $this->resetcache();
        $this->success("排序更新成功！");
    }

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('orders_cancel')->where('id',$id)->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        $this->resetcache();
        $this->success("删除成功！",url("ordercancel/index"));
    }


    protected function resetcache(){
        $key='getCancelList';

        $level=DB::name('orders_cancel')
                ->order("list_order asc")
                ->select();
        if($level){
            setcaches($key,$level);
        }
    }
}