<?php

/* 技能认证 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SkillauthController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=array("0"=>"审核中","1"=>"已通过",'2'=>'已拒绝');
        if($k==''){
            return $status;
        }
        return $status[$k];
    }
    
    protected function getSkill(){
        $list = Db::name('skill')
            ->order("list_order asc")
            ->column('*','id');
        return $list;
    }
    
    protected function getLevel(){
        
        $list1=$this->getSkill();
        
        $list2 = Db::name('skill_level')
            ->order("levelid asc")
            ->column('*','id');
            
        foreach($list1 as $k=>$v){
            $list=[];
            foreach($list2 as $k2=>$v2){
                
                if($v['id']==$v2['skillid']){
                    $list[$v2['levelid']]=$v2;
                }
            }
            $list1[$k]=$list;
        }
        return $list1;
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
        
        if($data['skillid']!=''){
            $map[]=['skillid','=',intval($data['skillid'])];
        }
        
        
        $list = Db::name('skill_auth')
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           
           $v['thumb']=get_upload_path($v['thumb']);

           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        $this->assign('status', $this->getStatus());
        $this->assign('skill', $this->getSkill());
        $this->assign('level', $this->getLevel());

        return $this->fetch();
    }


    public function setstatus()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        $reason = $this->request->param('reason');
        
        $result=DB::name("skill_auth")->where("id={$id}")->find();
        if(!$result){
            $this->error("数据传入失败！");
        }
        
        if($result['status']!=0 && $result['status'] == $status){
            $this->error("操作失败");
        }
        
        $nowtime=time();
        
        $rs=DB::name("skill_auth")->where("id={$id}")->update(['status'=>$status,'reason'=>$reason,'uptime'=>$nowtime]);
        if(!$rs){
            $this->error("操作失败");
        }
        
        if($status==1){
            setAgentAward($result['uid']);
        }
        
        $this->success("操作成功");        
    }

}