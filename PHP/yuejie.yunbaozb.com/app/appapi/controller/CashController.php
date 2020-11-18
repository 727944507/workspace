<?php
/**
 * 提现记录
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class CashController extends HomebaseController {
    
    protected function getSstatus(){
        $status=array(
            '0'=>lang('审核中'),
            '1'=>lang('成功'),
            '2'=>lang('失败'),
        );
        return $status;
    }

	function index(){
        
		$data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
		$votestype=checkNull($data['votestype']);
		
		if( !$uid || !$token || checkToken($uid,$token)==700 ){
			$reason=lang('您的登陆状态失效，请重新登陆！');
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		} 
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		
        $this->status=$this->getSstatus();

		$list=Db::name("cash_record")->where(["uid"=>$uid,"votes_type"=>$votestype])->order("addtime desc")->paginate(50);
        
		$list->each(function($v,$k){
            $v['addtime']=date('Y.m.d',$v['addtime']);
			$v['status_name']=$this->status[$v['status']];
            
            return $v;
        });
		
		$this->assign("list",$list);
		$this->assign("votestype",$votestype);
		
		return $this->fetch();
	    
	}
	
	public function getlistmore()
	{
		$data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
	
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		$this->status=$this->getSstatus();

        $list=Db::name("cash_record")->where(["uid"=>$uid])->order("addtime desc")->paginate(50);
        
		$list->each(function($v,$k){
            $v['addtime']=date('Y.m.d',$v['addtime']);
			$v['status_name']=$this->status[$v['status']];
            
            return $v;
        });
		
		$nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
		
		$result=array(
			'data'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

		echo json_encode($result);
		exit;
	}

}