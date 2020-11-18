<?php

/* 动态评论 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DynamiccomController extends AdminBaseController
{

    
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
        
        $did=isset($data['did'])? $data['did'] : '0' ;
        if($did!=0){
            $map[]=['did','=',$did];
        }

        $start_time=$data['start_time'];
        $end_time=$data['end_time'];
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        if($data['uid']!=''){
            $map[]=['uid','=',intval($data['uid'])];
        }
        
        
        $list = Db::name('dynamic_comment')
            ->where($map)
            ->order("id desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
        $this->assign("did", $did);
            
        $this->assign('list', $list);

        return $this->fetch();
    }
	
	public function add(){
		$did = $this->request->param('did');
		$this->assign('did', $did);
		return $this->fetch();
	}
	
	public function addPost(){
		
		$data = $this->request->param('post');
		$did = $this->request->param('did', 0, 'intval');
		$id = $this->request->param($data['id'], 0, 'intval');
		
		if($id<0 && $did<0){
			$this->error("数据有误！");
		}
		$data['did']=$did;
		$data['addtime']=time();
		$result = Db::name('dynamic_comment')->insert($data);
		$dynamicResult = Db::name('dynamic')->where('id',$did)->setInc('comments');
		
		if(!$result){
			$this->error("数据有误！");
		}
		
		if(!$dynamicResult){
			$this->error("数据有误！");
		}
		
		$this->success("保存成功");
		
	}
	
	public function edit(){
		
		$id = $this->request->param('id', 0, 'intval');
		if($id<1){
			$this->error("数据有误！");
		}
		
		$data = Db::name('dynamic_comment')->where('id',$id)->find();
		$this->assign('data', $data);
		return $this->fetch();
	}
	
	public function editPost(){
		
		$data = $this->request->param('post');
		$id = $this->request->param('id', 0, 'intval');
		$dynamic_comment = Db::name('dynamic_comment')->where('id',$id)->find();
		if(!$dynamic_comment){
			$this->error("数据有误！");
		}
		
		$dynamic_commentResult = Db::name('dynamic_comment')->where('id',$id)->strict(false)->update($data);
		if(!$dynamic_commentResult){
			$this->error("数据有误！");
		}
		
		$this->success("保存成功");
	}
	

    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $info=DB::name('dynamic_comment')->where("id={$id}")->find();
        $rs = DB::name('dynamic_comment')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        DB::name('dynamic')
                ->where("id={$info['did']} and comments>=1")
                ->setDec('comments','1');
        
        $this->success("删除成功！");
    }


}