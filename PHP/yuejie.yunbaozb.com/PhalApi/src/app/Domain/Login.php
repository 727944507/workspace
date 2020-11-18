<?php

namespace App\Domain;

use App\Model\Login as Model_Login;

class Login {

    public function userLogin($user_login,$source) {
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        

        $where=[
            'user_login = ?'=>$user_login,
        ];
        
        $model = new Model_Login();
        $info = $model->userLogin($where);
        
        if(!$info){
            /* 注册 */
            $nickname='';
            $data=array(
                'user_login' => $user_login,
                'user_nickname' => $nickname,
                "source"=>$source,
                "mobile"=>$user_login,
            );
            $model = new Model_Login();
            $info = $model->userReg($data);		
        }
        
        
        $info=$this->handleInfo($info);
  
        return $info;

    }
    
    

    public function reg($user_login,$user_pass,$source) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('注册成功'), 'info' => array());
        /* 注册 */
        //$nickname=\PhalApi\T('陪玩用户').substr($user_login,-3);
        $nickname='';
        $data=array(
            'user_login' => $user_login,
            'user_pass' => $user_pass,
            'user_nickname' => $nickname,
            "source"=>$source,
            "user_email"=>$user_login,
        );
        $model = new Model_Login();
        $info = $model->userReg($data);
        
		$info=$this->handleInfo($info);
  
        return $info;
        
    }
    public function userLoginByThird($openid,$type,$nickname,$avatar,$source) {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $model = new Model_Login();
        
        $nowtime=time();
        
        $where=[
            'openid = ?'=>$openid,
            'login_type = ?'=>$type,
        ];
        
        $info = $model->userLogin($where);
        
        if(!$info){
            /* 注册 */
            $type_a=['web','qq','wx','sina','facebook','twitter'];
			$user_login=$type_a[$type].'_'.$nowtime.rand(100,999);

			if(!$nickname){
				//$nickname=\PhalApi\T('陪玩用户').substr($openid,-3);
				$nickname='';
			}

			$data=array(
				'user_login' => $user_login,
				'user_nickname' =>$nickname,
				"source"=>$source,
				"openid"=>$openid,
				"login_type"=>$type,
			);
            
            if($avatar){
				$avatar=htmlspecialchars_decode($avatar);
                $avatar_thumb=$avatar;
                
                $data['avatar']=$avatar;
                $data['avatar_thumb']=$avatar_thumb;
			}
            
            $info = $model->userReg($data);
        }
        
        
		$info=$this->handleInfo($info);
  
        return $info;

    }
    
    public function forget($user_login,$user_pass) {
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        /* 注册 */
        $data=array(
            'user_pass' => $user_pass,
        );
        $model = new Model_Login();
        $info = $model->forget($user_login,$data);

  
        return $rs;
        
    }
    
    protected function handleInfo($info){
        $rs = array('code' => 0, 'msg' => \PhalApi\T('登录成功'), 'info' => array());
        
		if($info['user_status']=='0'){
			$rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('该账号已被禁用');
            return $rs;	
        }
		if($info['user_status']=='3'){
			
			$rs['code'] = 1005;
            $rs['msg'] = \PhalApi\T('该账号已注销');
            return $rs;	
		}
        
		unset($info['user_status']);
		
		$info['isreg']='0';
		if(!$info['last_login_time']){
			$info['isreg']='1';
		}
        unset($info['last_login_time']);

		$info=\App\handleUser($info);
        
        \App\delcache('userinfo_'.$info['id']);
        
        $model = new Model_Login();
        $token=md5(md5($info['id'].$info['user_nickname'].time()));
		$info['token']=$token;
		$model->updateToken($info['id'],$token);
        
        $usersig=\App\setSig($info['id']);
		$info['usersig']=$usersig;
        
        $rs['info'][0]=$info;
        return $rs;
    }
	public function getCancelCondition($uid) {
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
       
        $model = new Model_Login();
       
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
    	$userinfo=$model->getUseryue($uid);

    	//获取用户映票提现未处理记录
    	$votes_cashlist=$model->getcashRecord($uid);
    
    	//钻石小于100，映票小于100，余额为0
    	if($userinfo['coin']<100 && $userinfo['votes']<100 && $userinfo['votes_gift']<100 && !$votes_cashlist ){
    		$list[0]['is_ok']='1';
    	}

    	//获取用户作为买家的交易记录
    	$order_orderlist=$model->getOrderlist($uid);
		

    	//获取用户作为卖家的交易记录
    	$drip_orderlist=$model->getDriplist($uid);
		


    	if(!$order_orderlist && !$drip_orderlist){
    		$list[1]['is_ok']='1';
    	}

    	if($list[0]['is_ok']==1&&$list[1]['is_ok']==1){
    		$res['can_cancel']='1';
    	}

    	$res['list']=$list;
		$rs['info'][0]=$res;
        return $rs;
    }

	
	public function cancelAccount($uid) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->cancelAccount($uid);

        return $rs;
    }
    

}
