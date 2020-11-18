<?php
/**
 * 消费、收益明细
 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class DetailController extends HomebaseController {
	
	function index(){       
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        
       /*  $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		} */
        
		$this->assign("uid",$uid);
		$this->assign("token",$token);
		//消费明细
		$list=Db::name('user_coinrecord')->where(["uid"=>$uid, 'action' =>[1,2,3,4]])->order("addtime desc")->limit(0,50)->select()->toArray();
		
		foreach($list as $k=>$v){
			$record_thumb="";
			$record_name="";
			$record_typename="";//消费类型：下单、退回、退款、送礼
			$action =$v['action'];
			if($action=='1'){
				$record_typename="下单";
			}else if($action=='2'){
				$record_typename="退回";
			}else if($action=='3'){
				$record_typename="送礼";
			}else if($action=='4'){
				$record_typename="退款";
			}
			if($v['uid']==$v['touid']){
				unset($list[$k]);
				continue;
			}
			if($v['action']=='3'){//礼物
				$giftinfo=Db::name('gift')->field("giftname,gifticon")->where("id={$v['actionid']}")->find();
				if(!$giftinfo){
					$giftinfo=array(
						"giftname"=>'礼物已删除',
						"gifticon"=>"/default.png"
					);
				}
				if($v['nums']>=100){
					$giftinfo['giftname']=$this->subtext($giftinfo['giftname'],2);
				}
				$record_thumb=get_upload_path($giftinfo['gifticon']);
				$record_name=$giftinfo['giftname']." x ".$v['nums'];
			}else{
				$orderinfo=Db::name("orders")->field("skillid")->where("id='{$v['actionid']}'")->find();
				if($orderinfo){
					$skillinfo=Db::name("skill")->field("thumb,name")->where("id='{$orderinfo['skillid']}'")->find();
					if(!$skillinfo){
						$skillinfo=array(
							"name"=>'技能已删除',
							"thumb"=>"/default.png"
						);
					}
					$record_thumb=get_upload_path($skillinfo['thumb']);
					$record_name=$skillinfo['name'];
				}
				
			}
		
			$list[$k]['record_thumb']=$record_thumb;
			$list[$k]['record_name']=$record_name;
			$list[$k]['record_typename']=$record_typename;
			
			$userinfo=getUserInfo($v['touid']);
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list[$k]['userinfo']=$userinfo;
		}
		$list=array_values($list);
		
		$this->assign("list",$list);
		
		//收益明细
		
		$list_coin=Db::name('user_votesrecord')->where("uid={$uid} ")->order("addtime desc")->limit(0,50)->select()->toArray();
		
		foreach($list_coin as $k=>$v){
			$record_thumb="";
			$record_name="";
			$record_typename="";//消费类型：下单、退回、退款、送礼
			$action =$v['action'];
			if($action=='1'){
				$record_typename="接单";
			}else if($action=='2'){
				$record_typename="退回";
			}else if($action=='3'){
				$record_typename="收礼";
			}else if($action=='4'){
				$record_typename="退款";
			}
			
			if($v['action']=='3'){//礼物
				if($v['uid']==$uid){
					$userinfo=getUserInfo($v['fromid']);
					$giftinfo=Db::name('gift')->field("giftname,gifticon")->where("id={$v['actionid']}")->find();
					if(!$giftinfo){
						$giftinfo=array(
							"giftname"=>'礼物已删除',
							"gifticon"=>"/default.png"
						);
					}
					if($v['nums']>=100){
						$giftinfo['giftname']=$this->subtext($giftinfo['giftname'],2);
					}
					$record_thumb=get_upload_path($giftinfo['gifticon']);
					$record_name=$giftinfo['giftname']." x ".$v['nums'];
				}else{
					unset($list_coin[$k]);
					continue;
				}
				
			
			}else{
				$userinfo=getUserInfo($v['fromid']);
				
				$orderinfo=Db::name("orders")->field("skillid")->where("id='{$v['actionid']}'")->find();
				if($orderinfo){
					$skillinfo=Db::name("skill")->field("thumb,name")->where("id='{$orderinfo['skillid']}'")->find();
					if(!$skillinfo){
						$skillinfo=array(
							"name"=>'技能已删除',
							"thumb"=>"/default.png"
						);
					}
					$record_thumb=get_upload_path($skillinfo['thumb']);
					$record_name=$skillinfo['name'];
				}
				
			}
		
			$list_coin[$k]['record_thumb']=$record_thumb;
			$list_coin[$k]['record_name']=$record_name;
			$list_coin[$k]['record_typename']=$record_typename;
			
			
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list_coin[$k]['userinfo']=$userinfo;
		}
		$list_coin=array_values($list_coin);
		
		$this->assign("list_coin",$list_coin);
		
		return $this->fetch();
	    
	}
	function subtext($text, $length)
	{
		if(mb_strlen($text, 'utf8') > $length) {
			return mb_substr($text, 0, $length, 'utf8').'...';
		} else {
			return $text;
		}
	 
	}

	//更多消费记录
	public function voteslist_more()
	{
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $p=isset($data['page']) ? $data['page']: '1';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $p=checkNull($p);
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
	
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		
		$pnums=50;
		$start=($p-1)*$pnums;
		
		$list=Db::name('user_coinrecord')->where(["uid"=>$uid, 'action' =>[1,2,3,4]])->order("addtime desc")->limit($start,$pnums)->select()->toArray();
		foreach($list as $k=>$v){
			$record_thumb="";
			$record_name="";
			$record_typename="";//消费类型：下单、退回、退款、送礼
			$action =$v['action'];
			if($action=='1'){
				$record_typename="下单";
			}else if($action=='2'){
				$record_typename="退回";
			}else if($action=='3'){
				$record_typename="送礼";
			}else if($action=='4'){
				$record_typename="退款";
			}
			if($v['uid']==$v['touid']){
				unset($list[$k]);
				continue;
			}
			
			if($v['action']=='3'){//礼物
				$giftinfo=Db::name('gift')->field("giftname,gifticon")->where("id={$v['actionid']}")->find();
				if(!$giftinfo){
					$giftinfo=array(
						"giftname"=>'礼物已删除',
						"gifticon"=>"/default.png"
					);
				}
				if($v['nums']>=100){
					$giftinfo['giftname']=$this->subtext($giftinfo['giftname'],2);
				}
				$record_thumb=get_upload_path($giftinfo['gifticon']);
				$record_name=$giftinfo['giftname']." x ".$v['nums'];
			}else{
				$orderinfo=Db::name("orders")->field("skillid")->where("id='{$v['actionid']}'")->find();
				if($orderinfo){
					$skillinfo=Db::name("skill")->field("thumb,name")->where("id='{$orderinfo['skillid']}'")->find();
					if(!$skillinfo){
						$skillinfo=array(
							"name"=>'技能已删除',
							"thumb"=>"/default.png"
						);
					}
					$record_thumb=get_upload_path($skillinfo['thumb']);
					$record_name=$skillinfo['name'];
				}
			}
		
			$list[$k]['record_thumb']=$record_thumb;
			$list[$k]['record_name']=$record_name;
			$list[$k]['record_typename']=$record_typename;
			
			$userinfo=getUserInfo($v['uid']);
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list[$k]['userinfo']=$userinfo;
		}
		$list=array_values($list);
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
	//更多收益记录
	public function coinlist_more()
	{
		$data = $this->request->param();
        $uid=isset($data['uid']) ? $data['uid']: '';
        $token=isset($data['token']) ? $data['token']: '';
        $p=isset($data['page']) ? $data['page']: '1';
        $uid=(int)checkNull($uid);
        $token=checkNull($token);
        $p=checkNull($p);
		
		$result=array(
			'data'=>array(),
			'nums'=>0,
			'isscroll'=>0,
		);
	
		if(checkToken($uid,$token)==700){
			echo json_encode($result);
			exit;
		} 
		
		$pnums=50;
		$start=($p-1)*$pnums;
		
		$list_coin=Db::name('user_coinrecord')->where(["touid"=>$uid, 'action' =>[1,2,3,4]])->order("addtime desc")->limit(0,50)->select()->toArray();
		foreach($list_coin as $k=>$v){
			$list_coin[$k]['addtime']=date('Y/m/d',$v['addtime']);
			$record_thumb="";
			$record_name="";
			$record_typename="";//消费类型：下单、退回、退款、送礼
			$action =$v['action'];
			if($action=='1'){
				$record_typename="接单";
			}else if($action=='2'){
				$record_typename="退回";
			}else if($action=='3'){
				$record_typename="收礼";
			}else if($action=='4'){
				$record_typename="退款";
			}
			
			if($v['action']=='3'){//礼物
				$giftinfo=Db::name('gift')->field("giftname,gifticon")->where("id={$v['actionid']}")->find();
				if(!$giftinfo){
					$giftinfo=array(
						"giftname"=>'礼物已删除',
						"gifticon"=>"/default.png"
					);
				}
				if($v['nums']>=100){
					$giftinfo['giftname']=$this->subtext($giftinfo['giftname'],2);
				}
				$record_thumb=get_upload_path($giftinfo['gifticon']);
				$record_name=$giftinfo['giftname']." x ".$v['nums'];
			
			}else{
				$orderinfo=Db::name("orders")->field("skillid")->where("id='{$v['actionid']}'")->find();
				if($orderinfo){
					$skillinfo=Db::name("skill")->field("thumb,name")->where("id='{$orderinfo['skillid']}'")->find();
					if(!$skillinfo){
						$skillinfo=array(
							"name"=>'技能已删除',
							"thumb"=>"/default.png"
						);
					}
					$record_thumb=get_upload_path($skillinfo['thumb']);
					$record_name=$skillinfo['name'];
				}
				
			}
		
			$list_coin[$k]['record_thumb']=$record_thumb;
			$list_coin[$k]['record_name']=$record_name;
			$list_coin[$k]['record_typename']=$record_typename;
			
			$userinfo=getUserInfo($v['touid']);
			if(!$userinfo){
				$userinfo=array(
					"user_nicename"=>'用户已删除'
				);
			}
			$list_coin[$k]['userinfo']=$userinfo;
		}

		$nums=count($list_coin);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
		
		$result=array(
			'data'=>$list_coin,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

		echo json_encode($result);
		exit;
	}
	

}