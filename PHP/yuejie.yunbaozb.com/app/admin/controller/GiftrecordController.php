<?php

/**
 * 礼物消费、收益记录
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class GiftrecordController extends AdminbaseController {
    
    protected function getTypes($k=''){
        $type=array(
            '0'=>'支出',
            '1'=>'收入',
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    protected function getAction($k=''){
        $action=array(
            '1'=>'余额下单',
            '2'=>'订单退回',
            '3'=>'赠送礼物',
        );
        if($k===''){
            return $action;
        }
        
        return isset($action[$k]) ? $action[$k]: '未知';
    }
    //礼物消费明细
    function coinrecord(){
        $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }
        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
          $map[]=['uid','=',$uid];
        }
        
        $touid=isset($data['touid']) ? $data['touid']: '';
        if($touid!=''){
            $map[]=['touid','=',$touid];
        }
        $map[]=['type','=',0];//支出明细
		$map[]=['action','=',3];//赠送礼物
        $lists = Db::name("user_coinrecord")
            ->where($map)
			->order("id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
			$v['touserinfo']=getUserInfo($v['touid']);
            
            $action=$v['action'];
            if($action=='3'){
                $giftinfo=Db::name("gift")->field("giftname,gifticon")->where("id='{$v['actionid']}'")->find();
				$giftinfo['gifticon']=get_upload_path($giftinfo['gifticon']);
            }else{
                $giftinfo['giftname']=$this->getAction($action);
				$giftinfo['gifticon']=get_upload_path("/default.png");
            }
			
            $v['giftinfo']= $giftinfo;
		
            return $v;           
        });
    	
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign('action', $this->getAction());
        $this->assign('type', $this->getTypes());
        
    	return $this->fetch();
    }
		
	//礼物收益明细
    function votesrecord(){
        $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }
        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
          $map[]=['uid','=',$uid];
        }
        
        $touid=isset($data['fromid']) ? $data['fromid']: '';
        if($touid!=''){
            $map[]=['fromid','=',$touid];
        }
        $map[]=['type','=',1];//支出明细
		$map[]=['action','=',3];//赠送礼物
        $lists = Db::name("user_votesrecord")
            ->where($map)
			->order("id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['userinfo']=getUserInfo($v['uid']);
			$v['touserinfo']=getUserInfo($v['fromid']);
            
            $action=$v['action'];
            if($action=='3'){
                $giftinfo=Db::name("gift")->field("giftname,gifticon")->where("id='{$v['actionid']}'")->find();
				$giftinfo['gifticon']=get_upload_path($giftinfo['gifticon']);
            }else{
                $giftinfo['giftname']=$this->getAction($action);
				$giftinfo['gifticon']=get_upload_path("./default.png");
            }
            $v['giftinfo']= $giftinfo;
            return $v;           
        });
    	
        $lists->appends($data);
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
        $this->assign('action', $this->getAction());
        $this->assign('type', $this->getTypes());
        
    	return $this->fetch();
    
    }
    function del(){
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('user_coinrecord')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
                    
        $this->success("删除成功！");
        							  			
    }    	
}
