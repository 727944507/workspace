<?php

/* 动态管理 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DynamicController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=[
            '-1'=>'已拒绝',
            "0"=>"审核中",
            "1"=>"已通过", 
        ];
        if($k==''){
            return $status;
        }
        return isset($status[$k])? $status[$k] : '' ;
    }
    
    protected function getTypes($k=''){
        $type=[
            "0"=>"纯文字",
            "1"=>"图片",
            '2'=>'视频',
            '3'=>'语音',
        ];
        if($k==''){
            return $type;
        }
        return isset($type[$k])? $type[$k] : '' ;
    }
    
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
        $status=isset($data['status'])? $data['status']:'';
        if($status!=''){
            $map[]=['status','=',$status];
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
        
        
        $list = Db::name('dynamic')
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           $thumbs=preg_split('/,|，/',$v['thumbs']);
           $thumb=[];
           foreach($thumbs as $k1=>$v1){
               $thumb[]=get_upload_path($v1);
           }
           $v['thumb']=$thumb;
           $v['video_t']=get_upload_path($v['video_t']);
           $v['video']=get_upload_path($v['video']);
           $v['voice']=get_upload_path($v['voice']);

           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        $this->assign('status2', $status);
        $this->assign('status', $this->getStatus());
        $this->assign('type', $this->getTypes());

        return $this->fetch('index');
    }

    public function wait(){
        return $this->index();
    }
    public function nopass(){
        return $this->index();
    }
    
	public function edit(){
		$id = intval($this->request->param('id'));
		if($id<=0){
			$this->error("数据有误");
		}
		
		$dynamic = Db::name('dynamic')->where('id',$id)->find();
		$this->assign('data', $dynamic);
		return $this->fetch('edit');
	}
	
	public function editPost()
	{
		$data = $this->request->param('post');
		$id = intval($this->request->param('id'));
		
		if($id<=0){
			$this->error("数据有误");
		}
		
		$dynamicResult = Db::name('dynamic')->where('id',$id)->strict(false)->update($data);
		
		if(!$dynamicResult){
			$this->error("数据有误");
		}
		$this->success("保存成功！");
	}
	
	
    public function see()
    {
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('dynamic')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $data['video']=get_upload_path($data['video']);
        $data['voice']=get_upload_path($data['voice']);
           
        $this->assign('data', $data);
        return $this->fetch();
    }
    
    public function setstatus()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        
        $nowtime=time();
        
        $rs=DB::name("dynamic")->where("id={$id}")->update(['status'=>$status]);
        if(!$rs){
            $this->error("操作失败");
        }
        
        $this->success("操作成功");        
    }

    public function setrecom()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $recoms = $this->request->param('recoms', 0, 'intval');
        
        $nowtime=time();
        
        $rs=DB::name("dynamic")->where("id={$id}")->update(['recoms'=>$recoms]);
        if($rs===false){
            $this->error("操作失败");
        }
        
        $this->success("操作成功");        
    }
    
    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('dynamic')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $this->success("删除成功！");
    }

}