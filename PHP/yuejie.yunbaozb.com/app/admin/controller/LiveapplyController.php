<?php

/* 聊天室申请 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiveapplyController extends AdminBaseController
{
    
    protected function getStatus($k=''){
        $status=array("0"=>"审核中","1"=>"已通过",'2'=>'已拒绝');
        if($k==''){
            return $status;
        }
        return $status[$k];
    }

    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
        if($data['status']!=''){
            $map[]=['status','=',$data['status']];
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

        
        $list = Db::name('live_apply')
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
            $v['userinfo']= getUserInfo($v['uid']);
           
            $v['voice']=get_upload_path($v['voice']);

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
        $uid = $this->request->param('uid', 0, 'intval');
        if(!$uid){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        $reason = $this->request->param('reason');
        
        $result=DB::name("live_apply")->where("uid={$uid}")->find();
        if(!$result){
            $this->error("数据传入失败！");
        }
        
        if($result['status']!=0 && $result['status'] == $status){
            $this->error("操作失败");
        }
        
        $nowtime=time();
        
        $rs=DB::name("live_apply")->where("uid={$uid}")->update(['status'=>$status,'reason'=>$reason,'uptime'=>$nowtime]);
        if(!$rs){
            $this->error("操作失败");
        }
        
        
        $this->success("操作成功");        
    }


}