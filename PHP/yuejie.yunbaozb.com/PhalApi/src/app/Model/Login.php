<?php

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class Login extends NotORM {

	protected $fields='id,user_nickname,avatar,avatar_thumb,sex,signature,coin,votes,birthday,user_status,login_type,last_login_time,profession,school,hobby,voice,voice_l,addr,stars,star_nums,orders';

	/* 会员登录 */   	
    public function userLogin($where) {

		$info=\PhalApi\DI()->notorm->user
				->select($this->fields)
				->where('user_type=2')
                ->where($where)
				->fetchOne();
                
        return $info;
    }	
	
	/* 会员注册 */
    public function userReg($data=[]) {
        
        $nowtime=time();
        $user_pass='yuewan'.$nowtime;
        $user_pass=\App\setPass($user_pass);

        $avatar='/default.png';
        $avatar_thumb='/default_thumb.png';
		$configpri=\App\getConfigPri();
		$reg_reward=$configpri['reg_reward'];
		
        $default=array(
            'user_pass' =>$user_pass,
            'signature' =>'',
            'avatar' =>$avatar,
            'avatar_thumb' =>$avatar_thumb,
            'last_login_ip' =>\PhalApi\Tool::getClientIp(),
            'create_time' => $nowtime,
            'user_status' => 1,
            "user_type"=>2,//会员
			"coin"=>$reg_reward,
        );
        
        if(isset($data['user_pass'])){
            $data['user_pass']=\App\setPass($data['user_pass']);
        }
        $insert=array_merge($default,$data);
		$rs=\PhalApi\DI()->notorm->user->insert($insert);
          
        $id=$rs['id'];
		if($reg_reward>0){
			$insert=array("type"=>'1',"action"=>'11',"uid"=>$id,"touid"=>$id,"actionid"=>0,"nums"=>1,"total"=>$reg_reward,"addtime"=>time() );
			\PhalApi\DI()->notorm->user_coinrecord->insert($insert);
		}
        
        $info=\PhalApi\DI()->notorm->user
				->select($this->fields)
				->where('id=?',$id)
				->fetchOne();
                
		return $info;
    }

	/* 忘记密码 */
    public function forget($user_login,$data=[]) {
        
        if(!$data){
            return !1;
        }
        
        if(isset($data['user_pass'])){
            $data['user_pass']=\App\setPass($data['user_pass']);
        }

        
        $info=\PhalApi\DI()->notorm->user
				->where('user_login=?',$user_login)
				->update( $data );
                
		return $info;
    }
		

	/* 更新token 登陆信息 */
    public function updateToken($uid,$token,$data=array()) {
        
        $nowtime=time();
		$expiretime=$nowtime+60*60*24*150;

		\PhalApi\DI()->notorm->user
			->where('id=?',$uid)
			->update(array('last_login_time' => $nowtime, "last_login_ip"=>\PhalApi\Tool::getClientIp() ));

		$token_info=array(
			'user_id'=>$uid,
			'token'=>$token,
			'expire_time'=>$expiretime,
			'create_time'=>$nowtime,
		);
        $isexist=\PhalApi\DI()->notorm->user_token
			->where('user_id=?',$uid)
			->update( $token_info );
        if(!$isexist){
            \PhalApi\DI()->notorm->user_token
                ->insert( $token_info );
        }
		
		\App\hMSet("token_".$uid,$token_info);		
        
		return 1;
    }

public function getUseryue($uid){
		$userinfo=\PhalApi\DI()->notorm->user
						->select("coin,votes,votes_gift")
						->where("id=?",$uid)
						->fetchOne();
		return $userinfo;
	}
	public function getcashRecord(){
		$recordlist=\PhalApi\DI()->notorm->cash_record
					->where("uid=? and status=0",$uid)
					->fetchAll();
		return $recordlist;
	}
	
	public function getOrderlist($uid){
		$list=\PhalApi\DI()->notorm->orders
					->where("(uid=? or liveuid=?) and status in(0,2,3,4,6)",$uid,$uid)
					->select("id,uid,status")
					->fetchAll(); //0待支付；2：已接单；3:等待退款；4：拒绝退款；6：退款申诉
		return $list;
	}
	public function getDriplist($uid){
		$list=\PhalApi\DI()->notorm->drip
				->where("(uid=? or liveuid=?)  and status in(0,1)",$uid,$uid)
				->select("id,status")
				->fetchAll();//状态，0抢单中，1已接单
		return $list;
	}
	
	//注销账号
    public function cancelAccount($uid){
    	$condition=$this->getCancelCondition($uid);
		
    	if(!$condition['can_cancel']){
    		return 1001;
    	}
    	$now=time();
		//修改用户昵称
		\PhalApi\DI()->notorm->user->where("id=?",$uid)->update(array('user_nickname'=>'用户已注销','user_status'=>3));
		//订单处理
        \App\delcache("userinfo_".$uid);
		return 1;
    }
	//获取注销账号条件
    public function getCancelCondition($uid){

    	$res=array('list'=>array(),'can_cancel'=>'0');

    	$list=array(
    		'0'=>array(
    				'title'=>'1、账号内无大额未消费或未提现的财产',
    				'content'=>'你账号内无未结清的欠款、资金和虚拟权益，无正在处理的提现记录；注销后，账户中的虚拟权益等将作废无法恢复。',
    				'is_ok'=>'0'
    			),
    		'1'=>array(
    				'title'=>'2、账号无其它正在进行中的业务及争议纠纷',
    				'content'=>'本账号内已无其它正在进行中的经营性业务、未完成的交易、无任何未处理完成的纠纷（比如退款申请、退款中、待收货等）',
    				'is_ok'=>'0'
    			)
    	);

		//获取用户的映票、钻石、余额
    	$userinfo=$this->getUseryue($uid);

    	//获取用户映票提现未处理记录
    	$votes_cashlist=$this->getcashRecord($uid);
		
    	//钻石小于100，映票小于100，余额为0
    	if($userinfo['coin']<100 && $userinfo['votes']<100 && $userinfo['votes_gift']<100 && !$votes_cashlist ){
    		$list[0]['is_ok']='1';
    	}

		//获取用户作为买家的交易记录
    	$order_orderlist=$this->getOrderlist($uid);

    	//获取用户作为卖家的交易记录
    	$drip_orderlist=$this->getDriplist($uid);
		
    	if(!$order_orderlist && !$drip_orderlist){
    		$list[1]['is_ok']='1';
    	}

    	if($list[0]['is_ok']==1 && $list[1]['is_ok']==1){
    		$res['can_cancel']='1';
    	}
		
    	$res['list']=$list;

    	return $res;

    }	

}
