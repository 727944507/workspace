<?php

/* 技能 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SkillController extends AdminBaseController
{
    protected function getCLass(){
        $list = Db::name('skill_class')
            ->order("list_order asc")
            ->column('*','id');
        return $list;
    }
    
    protected function getMethod(){
        $list = [
            '半小时',
            '小时',
            '局',
            '张',
            '次',
            '幅',
            '部',
            '首',
        ];
        return $list;
    }
	protected function getMethodMinutes(){
        $list = [
            '10',
            '20',
            '40',
        ];
        return $list;
    }
    public function index()
    {
        $data = $this->request->param();
        $map=[];
        if($data['classid']!=''){
            $map[]=['classid','=',$data['classid']];
        }
        

        if($data['name']!=''){
            $map[]=['name','like','%'.$data['name'].'%'];
        }
        
        $list = Db::name('skill')
            ->where($map)
            ->order("list_order asc")
            ->paginate(20);
        $list->each(function($v,$k){
            $v['thumb']=get_upload_path($v['thumb']);
			$v['thumb2']=get_upload_path($v['thumb2']);
			$v['thumb3']=get_upload_path($v['thumb3']);
            return $v;
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        $this->assign('class', $this->getCLass());

        return $this->fetch();
    }


    public function add()
    {
        $this->assign('class', $this->getCLass());
        $this->assign('method', $this->getMethod());
		$this->assign('methodminutes', $this->getMethodMinutes());
        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            
            $classid=$data['classid'];
            if($classid < 1){
                $this->error('请选择分类');
            }
            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $map[]=['name','=',$name];
            $isexist=DB::name('skill')->where($map)->find();
            if($isexist){
                $this->error('同名技能已存在');
            }
            
            $thumb=$data['thumb'];
            if($thumb==''){
                $this->error('请上传封面');
            }
			
			$thumb=$data['thumb2'];
			if($thumb==''){
			    $this->error('请上传栏目封面');
			}
			
			$thumb=$data['thumb3'];
			if($thumb==''){
			    $this->error('请上传示例图');
			}

            $id = DB::name('skill')->insertGetId($data);
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
        
        $data=Db::name('skill')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        $this->assign('class', $this->getCLass());
        $this->assign('method', $this->getMethod());
		$this->assign('methodminutes', $this->getMethodMinutes());
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function editPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();

            $id=$data['id'];
            
			$method=$data['method'];
			if($method=="半小时" ){
				$data['methodminutes']=30;
			}else if($method=="小时"){
				$data['methodminutes']=60;
			}
			
            $classid=$data['classid'];
            if($classid < 1){
                $this->error('请选择分类');
            }
            
            $name=$data['name'];
            
            if($name == ''){
                $this->error('请填写名称');
            }
            
            $map[]=['name','=',$name];
            $map[]=['id','<>',$id];
            $isexist=DB::name('skill')->where($map)->find();
            if($isexist){
                $this->error('同名技能已存在');
            }
            
            $thumb=$data['thumb'];
            if($thumb==''){
                $this->error('请上传封面');
            }
			
			$thumb=$data['thumb2'];
			if($thumb==''){
			    $this->error('请上传栏目封面');
			}
			
			$thumb=$data['thumb3'];
			if($thumb==''){
			    $this->error('请上传技能示例图');
			}
            

            $rs = DB::name('skill')->update($data);

            if($rs === false){
                $this->error("保存失败！");
            }
            $this->resetcache();
            $this->success("保存成功！");
        }
    }
    
    public function listOrder()
    {
        $model = DB::name('skill');
        parent::listOrders($model);
        $this->resetcache();
        $this->success("排序更新成功！");
    }

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('skill')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        /* 删除技能等级 */
        DB::name('skill_level')->where("skillid={$id}")->delete();
        /* 删除技能认证 */
        DB::name('skill_auth')->where("skillid={$id}")->delete();
        /* 删除技能标签 */
        DB::name('label')->where("skillid={$id}")->delete();
        DB::name('label_count')->where("skillid={$id}")->delete();
        /* 删除技能评价 */
        DB::name('skill_comment')->where("skillid={$id}")->delete();
        
        
        $this->resetcache();
        $this->success("删除成功！",url("Skill/index"));
    }


    protected function resetcache(){
        $key='getSkilllist';

        $list=DB::name('skill')
                ->field('*')
                ->order("list_order asc")
                ->select();
        if($list){
            setcaches($key,$list);
        }
    }
}