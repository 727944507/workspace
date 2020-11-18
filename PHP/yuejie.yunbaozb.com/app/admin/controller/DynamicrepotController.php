<?php

/* 动态举报 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DynamicrepotController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=array("0"=>"审核中","1"=>"已处理");
        if($k==''){
            return $status;
        }
        return isset($status[$k])? $status[$k] : '' ;
    }
    
    
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
        
        $status=isset($data['status']) ? $data['status']: '';
        
        if($status!=''){
            $map[]=['status','=',$status];
        }

        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        if($data['uid']!=''){
            $map[]=['uid','=',intval($data['uid'])];
        }
        
        if($data['touid']!=''){
            $map[]=['touid','=',intval($data['touid'])];
        }
        
        if($data['did']!=''){
            $map[]=['did','=',intval($data['did'])];
        }
        
        
        $list = Db::name('dynamic_report')
            ->where($map)
            ->order("id desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           $v['touserinfo']= getUserInfo($v['touid']);
           
           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        $this->assign('status', $this->getStatus());

        return $this->fetch();
    }


    public function setstatus()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        
        $rs=DB::name("dynamic_report")->where("id={$id}")->update(['status'=>$status]);
        if($rs===false){
            $this->error("操作失败");
        }
        
        $this->success("操作成功");        
    }
    
    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        
        $result=DB::name("dynamic_report")->where("id={$id}")->delete();
        if(!$result){
            $this->error("删除失败！");
        }

        $this->success("删除成功！");
    }

}