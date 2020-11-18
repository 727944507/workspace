<?php

/* 用户举报 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class UserrepotController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=array("0"=>"审核中","1"=>"已处理");
        if($k==''){
            return $status;
        }
        return isset($status[$k])? $status[$k] : '' ;
    }
	
	protected function getBanlong($k=''){
        $banlong=array("0"=>"0.5","1"=>"3",'2'=>'6','3'=>'12');
        if($k==''){
            return $banlong;
        }
        return $banlong[$k];
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
        
        
        $list = Db::name('user_report')
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
		$this->assign('banlong', $this->getBanlong());

        return $this->fetch();
    }


    public function setstatus()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        
        $rs=DB::name("user_report")->where("id={$id}")->update(['status'=>$status]);
        if($rs!==false){
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
        
        $result=DB::name("user_report")->where("id={$id}")->delete();
        if(!$result){
            $this->error("删除失败！");
        }

        $this->success("删除成功！");
    }
	
	//禁止接单列表
    public function setbanorder(){
    	
    	$data = $this->request->param();
    	$uid=$data['touid'];
    	$_selectBanval=$data['_selectBanval'];
    	
		$map['uid']=$uid;
		
		
		$baninfo = Db::name('user_banorder')
            ->where($map)
            ->find();
		
    	$this->assign("baninfo",$baninfo);
		$this->assign('banlong', $this->getBanlong());
		$this->assign('_selectBanval', $_selectBanval);
    	return $this->fetch();

    }
	//禁止时长设置
	public function setBan(){
		$res=array("code"=>0,"msg"=>"设置成功","info"=>array());
		$data = $this->request->param();
    	$uid=$data['touid'];
    	$reportid=$data['reportid'];
    	$selectBanval=$data['selectBanval'];
		
		$nowtime=time();
		
    	$endtime=$nowtime+$selectBanval*60*60;
		$map['uid']=$uid;
		$map['type']="0";
		
		$isexist = Db::name('user_banorder')
            ->where($map)
            ->find();
		if($isexist){
			$result = Db::name('user_banorder')
				->where($map)
				->update(["starttime"=>$nowtime,'endtime'=>$endtime,"banlong"=>$selectBanval]);
		}else{
			$result=Db::name("user_banorder")->insert(["uid"=>$uid,"banlong"=>$selectBanval,"starttime"=>$nowtime,'endtime'=>$endtime]);
		}
		//更新举报信息状态
		DB::name("user_report")->where("id={$reportid}")->update(['status'=>"1"]);
		
		if($result===false){
			$res['code']=1001;
    		$res['msg']="设置失败";
    		echo json_encode($res);
		}
	
    	echo json_encode($res);
    	exit;

	}

}