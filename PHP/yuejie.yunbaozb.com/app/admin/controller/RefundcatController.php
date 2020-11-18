<?php

/* 退款分类 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class RefundcatController extends AdminBaseController
{

    public function index()
    {
        
        $list = Db::name('refundcat')
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
            $isexist = DB::name('refundcat')->where($map)->find();
            if($isexist){
                $this->error('同名已存在');
            }

            $id = DB::name('refundcat')->insertGetId($data);
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
        
        $data=Db::name('refundcat')
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
            $isexist = DB::name('refundcat')->where($map)->find();
            if($isexist){
                $this->error('同名已存在');
            }

            $rs = DB::name('refundcat')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
            $this->resetcache();
            $this->success("保存成功！");
        }
    }
    
    public function listOrder()
    {
        $model = DB::name('refundcat');
        parent::listOrders($model);
        $this->resetcache();
        $this->success("排序更新成功！");
    }

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('refundcat')->where('id',$id)->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        $this->resetcache();
        $this->success("删除成功！");
    }


    protected function resetcache(){
        /* $key='getdynamicreportcat';

        $level=DB::name('refundcat')
                ->order("list_order asc")
                ->select();
        if($level){
            setcaches($key,$level);
        } */
    }
}