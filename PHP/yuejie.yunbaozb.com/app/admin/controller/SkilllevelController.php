<?php

/* 技能等级 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SkilllevelController extends AdminBaseController
{

    public function index()
    {
        $data      = $this->request->param();
        
        $skillid=$data['skillid'];
        
        $map[]=['skillid','=',$skillid];
        
        $info=Db::name('skill')
            ->where(['id'=>$skillid])
            ->find();
        
        $list = Db::name('skill_level')
            ->where($map)
            ->order("levelid asc")
            ->select();

        $this->assign('list', $list);
        $this->assign('skillid', $skillid);
        $this->assign('info', $info);

        return $this->fetch();
    }


    public function add()
    {
        $data      = $this->request->param();
        
        $skillid=$data['skillid'];
        
         $this->assign('skillid', $skillid);
         
        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            
            $name=$data['name'];
            $skillid=$data['skillid'];
            $data['levelid']=intval($data['levelid']);
            $levelid=$data['levelid'];
            
            if($levelid == ''){
                $this->error('请填写等级');
            }
            
            if($levelid <1 ){
                $this->error('请填写正确等级');
            }
            $map2[]=['levelid','=',$levelid];
            $map2[]=['skillid','=',$skillid];
            $isexist=DB::name('skill_level')->where($map2)->find();
            if($isexist){
                $this->error('同等级已存在');
            }
            
            
            if($name == ''){
                $this->error('请填写名称');
            }
            $map[]=['name','=',$name];
            $map[]=['skillid','=',$skillid];
            $isexist=DB::name('skill_level')->where($map)->find();
            if($isexist){
                $this->error('同名信息已存在');
            }

            $id = DB::name('skill_level')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            $this->resetcache($data['skillid']);
            $this->success("添加成功！");
        }
    }

    public function edit()
    {
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('skill_level')
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
            $skillid=$data['skillid'];
            $data['levelid']=intval($data['levelid']);
            $levelid=$data['levelid'];
            
            if($levelid == ''){
                $this->error('请填写等级');
            }
            
            if($levelid < 1){
                $this->error('请填写正确等级');
            }
            
            $map2[]=['id','<>',$id];
            $map2[]=['levelid','=',$levelid];
            $map2[]=['skillid','=',$skillid];
            $isexist=DB::name('skill_level')->where($map2)->find();
            if($isexist){
                $this->error('同等级已存在');
            }
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $map[]=['id','<>',$id];
            $map[]=['name','=',$name];
            $map[]=['skillid','=',$skillid];
            
            $isexist=DB::name('skill_level')->where($map)->find();
            if($isexist){
                $this->error('同名信息已存在');
            }

            $rs = DB::name('skill_level')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
            $this->resetcache($data['skillid']);
            $this->success("保存成功！");
        }
    }
    
    public function listOrder()
    {
        $model = DB::name('skill_level');
        parent::listOrders($model);
        $this->resetcache($this->request->param('skillid', 0, 'intval'));
        $this->success("排序更新成功！");
    }

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('skill_level')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        $this->resetcache( $this->request->param('skillid', 0, 'intval') );
        $this->success("删除成功！");
    }


    protected function resetcache($skillid){
        $key='skillLevel_'.$skillid;

        $level=DB::name('skill_level')
                ->field('levelid,name,name_en')
                ->where(['skillid'=>$skillid])
                ->order("levelid asc")
                ->select();
        if($level){
            setcaches($key,$level);
        }
    }
}