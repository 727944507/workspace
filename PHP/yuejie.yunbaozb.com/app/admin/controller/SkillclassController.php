<?php

/* 技能分类 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SkillclassController extends AdminBaseController
{

    public function index()
    {
        
        $list = Db::name('skill_class')
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
            $isexist = DB::name('skill_class')->where($map)->find();
            if($isexist){
                $this->error('同名分类已存在');
            }

            $id = DB::name('skill_class')->insertGetId($data);
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
        
        $data=Db::name('skill_class')
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
            $isexist = DB::name('skill_class')->where($map)->find();
            if($isexist){
                $this->error('同名分类已存在');
            }

            $rs = DB::name('skill_class')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
            $this->resetcache();
            $this->success("保存成功！");
        }
    }
    
    public function listOrder()
    {
        $model = DB::name('skill_class');
        parent::listOrders($model);
        $this->resetcache();
        $this->success("排序更新成功！");
    }

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $isok=DB::name('skill')->where("classid",$id)->find();
        if($isok){
            $this->error("该分类下已有技能，不能删除");
        }
        
        $rs = DB::name('skill_class')->where('id',$id)->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        $this->resetcache();
        $this->success("删除成功！",url("Skillclass/index"));
    }


    protected function resetcache(){
        $key='getSkillclass';

        $level=DB::name('skill_class')
                ->order("list_order asc")
                ->select();
        if($level){
            setcaches($key,$level);
        }
    }
}