<?php

/* 身份认证 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AuthController extends AdminBaseController
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
        
        if($data['keyword']!=''){
            $map[]=['name|mobile','like',"%".$data['keyword']."%"];
        }

        
        $list = Db::name('user_auth')
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
            $v['userinfo']= getUserInfo($v['uid']);
           
            $v['front_view']=get_upload_path($v['front_view']);
            $v['back_view']=get_upload_path($v['back_view']);
            $v['handset_view']=get_upload_path($v['handset_view']);
            $v['mobile']=m_s($v['mobile']);
            $v['cer_no']=m_s($v['cer_no']);

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
        
        $result=DB::name("user_auth")->where("uid={$uid}")->find();
        if(!$result){
            $this->error("数据传入失败！");
        }
        
        if($result['status']!=0 && $result['status'] == $status){
            $this->error("操作失败");
        }
        
        $nowtime=time();
        
        $rs=DB::name("user_auth")->where("uid={$uid}")->update(['status'=>$status,'reason'=>$reason,'uptime'=>$nowtime]);
        if(!$rs){
            $this->error("操作失败");
        }
		if($status==1){
			DB::name("user")->where("id={$uid}")->update(['isauth'=>"1"]);
		}else{
			DB::name("user")->where("id={$uid}")->update(['isauth'=>"0"]);
		}
		
        
        $this->success("操作成功");        
    }


}