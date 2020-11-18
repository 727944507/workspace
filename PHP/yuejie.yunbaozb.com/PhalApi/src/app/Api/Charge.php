<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Charge as Domain_Charge;

/**
 * 充值
 */
 
class Charge extends Api {

	public function getRules() {
		return array(
            'getBalance' => array(
				'type' => array('name' => 'type', 'type' => 'int','desc' => 'APP类型，1是安卓，2是IOS'),
				'version' => array('name' => 'version', 'type' => 'string','desc' => '版本号'),
			),
			'getOrder' => array(
				'changeid' => array('name' => 'changeid', 'type' => 'string',  'desc' => '充值规则ID'),
				'coin' => array('name' => 'coin', 'type' => 'string',  'desc' => '钻石'),
				'money' => array('name' => 'money', 'type' => 'string', 'desc' => '充值金额'),
				'type' => array('name' => 'type', 'type' => 'int', 'desc' => '充值方式ID'),
			),
            
            
            /* 'getTestOrder' => array(
				'changeid' => array('name' => 'changeid', 'type' => 'string',  'desc' => '充值规则ID'),
				'coin' => array('name' => 'coin', 'type' => 'string',  'desc' => '钻石'),
				'money' => array('name' => 'money', 'type' => 'string', 'desc' => '充值金额'),
			), */
		);
	}
    
    /**
	 * 充值规则
	 * @desc 用于获取充值规则
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string info[].id 规则ID
	 * @return string info[].money 金额
	 * @return string info[].coin 非苹果支付钻石数
	 * @return string info[].coin_ios 苹果支付钻石数
	 * @return string info[].product_id 规则ID
	 * @return string info[].give 赠送
	 * @return string msg 提示信息
	 */
	public function getChargeRules() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$domain = new Domain_Charge();
		$info = $domain->getChargeRules();
        
        $rs['info'] =$info;
        
		return $rs;
	}
    
	/**
	 * 我的钻石
	 * @desc 用于获取用户余额,充值规则 支付方式信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].coin 用户余额
	 * @return array  info[0].rules 充值规则
	 * @return string info[0].rules[].id 规则ID
     * @return string info[0].rules[].money 金额
	 * @return string info[0].rules[].coin 非苹果支付钻石数
	 * @return string info[0].rules[].coin_ios 苹果支付钻石数
	 * @return string info[0].rules[].product_id 苹果项目ID
	 * @return string info[0].rules[].give 赠送钻石，为0时不显示赠送
     * @return array info[0].paylist 支付方式列表
     * @return string info[0].paylist[].id apple苹果
     * @return string info[0].paylist[].name 名称
     * @return string info[0].paylist[].thumb 图标
	 * @return object info[0].ali 支付宝信息
	 * @return string info[0].ali.partner 合作者ID
	 * @return string info[0].ali.seller_id  账号
	 * @return string info[0].ali.key  PKCS8密钥
	 * @return string msg 提示信息
	 */
	public function getBalance() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $type=\App\checkNull($this->type);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
		
        
		$domain = new Domain_Charge();
        
		$info = \App\getUserCoin($uid);
		
        $rules= $domain->getChargeRules();

		$info['rules'] =$rules;
		
		$configpri=\App\getConfigPri();
        $ios_switch=$configpri['ios_switch'];
        $aliapp_switch=$configpri['aliapp_switch'];
        $wx_switch=$configpri['wx_switch'];
        $google_switch=$configpri['google_switch'];
        
        $paylist=[];
        
        if($aliapp_switch==1){
            $paylist[]=[
                'id'=>'1',
                'name'=>\PhalApi\T('支付宝支付'),
                'thumb'=>\App\get_upload_path("/static/app/pay/ali.png"),
            ];
        }
        
        if($wx_switch==1){
            $paylist[]=[
                'id'=>'2',
                'name'=>\PhalApi\T('微信支付'),
                'thumb'=>\App\get_upload_path("/static/app/pay/wx.png"),
            ];
        }
        
        if($type==1){
            /* Android */
            if($google_switch==1){
                $paylist[]=[
                    'id'=>'4',
                    'name'=>\PhalApi\T('Google Pay'),
                    'thumb'=>\App\get_upload_path("/static/app/pay/google.png"),
                ];
            }
            
            /* $paylist[]=[
                    'id'=>'5',
                    'name'=>\PhalApi\T('Tset Pay'),
                    'thumb'=>\App\get_upload_path("/static/app/pay/google.png"),
                ]; */
            
        }

        if($type==2){
            /* IOS */
            if($ios_switch==1){
                $paylist[]=[
                    'id'=>'3',
                    'name'=>\PhalApi\T('苹果支付'),
                    'thumb'=>\App\get_upload_path("/static/app/pay/apple.png"),
                ];
            }
            
        }
        
        $info['paylist'] =$paylist;
        
        $ali=[
            'partner'=>$configpri['aliapp_partner'],
            'seller_id'=>$configpri['aliapp_seller_id'],
            'key'=>$configpri['aliapp_key'],
        ];
        $info['ali'] =$ali;
		$wx=[
            'wx_appid'=>$configpri['wx_appid'],
        ];
        $info['wx'] =$wx;
	 
		$rs['info'][0]=$info;
		return $rs;
	}
	
	/**
	 * 获取订单信息
	 * @desc 用于支付前获取订单信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].orderid 订单号
	 * @return object info[0].ali 支付宝信息
	 * @return string info[0].ali.partner 合作者ID
	 * @return string info[0].ali.seller_id  账号
	 * @return string info[0].ali.key  PKCS8密钥
	 * @return object info[0].wx 微信信息
	 * @return string info[0].wx.appid 微信Appid
	 * @return string info[0].wx.noncestr 随机数
	 * @return string info[0].wx.package 固定数据
	 * @return string info[0].wx.partnerid 商户ID
	 * @return string info[0].wx.prepayid 支付ID
	 * @return string info[0].wx.timestamp 时间戳
	 * @return string msg 提示信息
	 */
	public function getOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=\App\checkNull($this->uid);
		$token=\App\checkNull($this->token);
		$changeid=\App\checkNull($this->changeid);
		$coin=\App\checkNull($this->coin);
		$money=\App\checkNull($this->money);
		$type=\App\checkNull($this->type);

		if($uid<1 || $changeid<1 || $coin<=0 || $money<=0 || $type<1){
			$rs['code']=1002;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
		}
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $configpri = \App\getConfigPri();
        
        

        $orderid=$uid.'_'.date('ymdHis').rand(100,999);

        $ambient=0;
		if($configpub['ipa_ver']!=$configpub['ios_shelves']){
			$ambient=1;
		}
		
		$orderinfo=array(
			"uid"=>$uid,
			"touid"=>$uid,
			"money"=>$money,
			"coin"=>$coin,
			"orderno"=>$orderid,
			"type"=>$type,
			"status"=>0,
			"addtime"=>time(),
			"ambient"=>$ambient,
		);
		
		$domain = new Domain_Charge();
		$info = $domain->setOrder($changeid,$orderinfo);
		if(!$info){
			$rs['code']=1001;
			$rs['msg']=\PhalApi\T('订单生成失败');
            return $rs;	
		}else if($info['code']!=0){
            return $info;
		}
        
        $ali=[
            'partner'=>'',
            'seller_id'=>'',
            'key'=>'',
        ];
		
		$time2 = time();
		$sign = "";
		$noceStr = md5(rand(100,1000).time());//获取随机字符串
        $wx=[
            'appid'=>$configpri['wx_appid'],
            'noncestr'=>$noceStr,
            'package'=>'Sign=WXPay',
            'partnerid'=>$configpri['wx_mchid'],
            'prepayid'=>"",
            'timestamp'=>$time2,
        ];
		$wx["sign"] =$this -> sign($wx,$configpri['wx_key']);//生成签名
        
        if($type==1){
            /* 支付宝 */
			if($configpri['aliapp_partner']=='' || $configpri['aliapp_seller_id']=='' || $configpri['aliapp_key']==''){
				$rs['code']=1011;
				$rs['msg']=\PhalApi\T('支付宝未配置');
				return $rs;
			}
			
            $ali=[
                'partner'=>$configpri['aliapp_partner'],
                'seller_id'=>$configpri['aliapp_seller_id'],
                'key'=>$configpri['aliapp_key'],
            ];
        }else if($type==2){
            /* 微信 */
            $url=\App\get_upload_path('/appapi/pay/notify_wx');
            $res1=\App\wxPay($orderid,$money,$url);
            if($res1['code']!=0){
                return $res;
            }
            $wx=$res1['info'];
        }else if($type==3){
            /* 苹果支付 */
            
        }else if($type==4){
            /* 谷歌支付 */
            
        }

		$rs['info'][0]['orderid']=$orderid;
		$rs['info'][0]['ali']=$ali;
		$rs['info'][0]['wx']=$wx;
		return $rs;
	}
	
	/**
	* sign拼装获取
	*/
	protected function sign($param,$key){
		$sign = "";
		foreach($param as $k => $v){
			$sign .= $k."=".$v."&";
		}
		$sign .= "key=".$key;
		$sign = strtoupper(md5($sign));
		return $sign;
	
	}
    
	protected function getTestOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$uid=\App\checkNull($this->uid);
		$token=\App\checkNull($this->token);
		$changeid=\App\checkNull($this->changeid);
		$coin=\App\checkNull($this->coin);
		$money=\App\checkNull($this->money);

		if($uid<1 || $changeid<1 || $coin<=0 || $money<=0){
			$rs['code']=1002;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
		}
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $type=4;
        $orderid=$this->getOrderid($uid);
		
		$orderinfo=array(
			"uid"=>$uid,
			"touid"=>$uid,
			"money"=>$money,
			"coin"=>$coin,
			"orderno"=>$orderid,
			"type"=>$type,
			"status"=>1,
			"addtime"=>time(),
			"ambient"=>0
		);
		
		$domain = new Domain_Charge();
		$info = $domain->setOrder($changeid,$orderinfo);
		if(!$info){
			$rs['code']=1001;
			$rs['msg']=\PhalApi\T('订单生成失败');
            return $rs;	
		}else if($info['code']!=0){
            return $info;
		}
        
        \PhalApi\DI()->notorm->user
                ->where('id = ? ', $uid)
                ->update(array('coin' => new \NotORM_Literal("coin + {$coin}")) );    

		$rs['info'][0]['orderid']=$orderid;
		return $rs;
	}
	


}
