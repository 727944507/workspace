<?php

namespace app\appapi\controller;
use cmf\controller\HomeBaseController;
use think\Db;

class MonitorController extends HomebaseController {


	public function index() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$p = $this->request->param('p');

		if(!$p || $p<1){
            $p=1;
        }
		$pnum=8;
		$start=($p-1)*$pnum;
		
		$list = Db::name('live')
            ->where('islive=1')
			->order('starttime desc')
			->limit($start,$pnum)
            ->select();
		
		$list=$list->all();
		foreach($list as $k=>$v){
			$v['userinfo']= getUserInfo($v['uid']);
			$v['pull_user']=PrivateKeyA('http',$v['uid']);
			$cha=time()-$v['starttime'];
			$v['length']=getLength($cha);
			$list[$k]=$v;
			
		}

        		
		$count=Db::name('live')
				->where('islive=1')
				->count();
		if(!$count){
			$count=0;
		}	
		$info=array(
			'count'=>$count,
			'list'=>$list,
		);
		
		$rs['info']=$info;

		echo json_encode($rs);
        die;
		
	}
	
	public function indextalk() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$p = $this->request->param('p');

		if(!$p || $p<1){
            $p=1;
        }
		$pnum=8;
		$start=($p-1)*$pnum;
		//聊天室
		$list = Db::name('live')
            ->where('islive=1')
			->order('starttime desc')
			->limit($start,$pnum)
            ->select();
		
		$list=$list->all();
		foreach($list as $k=>$v){
			$v['userinfo']= getUserInfo($v['uid']);
			$v['pull_user']=PrivateKeyA('http',$v['uid']);
			$cha=time()-$v['starttime'];
			$v['length']=getLength($cha);
			$list[$k]=$v;
			
		}

        		
		$count=Db::name('live')
				->where('islive=1')
				->count();
		if(!$count){
			$count=0;
		}	
		
		//音视频聊天
		$list1 = Db::name('live_talk')
            ->where('islive=1')
			->order('addtime desc')
			->limit($start,$pnum)
            ->select();
		
		$list1=$list1->all();
		
		foreach($list1 as $k=>$v){
			$v['userinfo']= getUserInfo($v['uid']);
			$v['liveinfo']= getUserInfo($v['touid']);
			$v['pull_user']=PrivateKey_tx_talk('http',$v['uid'],$v['uid']);
			$v['pull_live']=PrivateKey_tx_talk('http',$v['uid'],$v['touid']);
			$cha=time()-$v['starttime'];
			$v['length']=getLength($cha);
			$list1[$k]=$v;
		}	
		$count1=Db::name('live_talk')
				->where('islive=1')
				->count();
		if(!$count1){
			$count1=0;
		}	
		$info=array(
			'count'=>$count,
			'list'=>$list,
			'count1'=>$count1,
			'list1'=>$list1,
		);
		
		$rs['info']=$info;

		echo json_encode($rs);
        die;
		
	}
	
}


