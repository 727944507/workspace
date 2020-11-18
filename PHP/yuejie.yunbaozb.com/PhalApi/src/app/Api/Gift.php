<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Gift as Domain_Gift;

/**
 * 礼物
 */
 
class Gift extends Api {

	public function getRules() {
		return array(
            'sendGift' => array(
				'touid' => array('name' => 'touid', 'type' => 'string', 'desc' => '用户ID，多个以逗号分割'),
				'giftid' => array('name' => 'giftid', 'type' => 'int', 'desc' => '礼物ID'),
				'nums' => array('name' => 'nums', 'type' => 'int', 'desc' => '数量'),
			),
            
            'sendGiftHome' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '用户ID'),
				'giftid' => array('name' => 'giftid', 'type' => 'int', 'desc' => '礼物ID'),
				'nums' => array('name' => 'nums', 'type' => 'int', 'desc' => '数量'),
			),
		);
	}
    
	/**
	 * 礼物列表 
	 * @desc 用于获取礼物列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return array info[0].list 礼物列表
	 * @return string info[0].coin 用户余额
	 * @return string msg 提示信息
	 */
	public function getList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Gift();
		$list = $domain->getList();
        
        $usercoin=\App\getUserCoin($uid);

        $rs['info'][0]['list']=$list;
        $rs['info'][0]['coin']=$usercoin['coin'];
        
		return $rs;
	}   

    /**
	 * 赠送礼物 
	 * @desc 用于用户赠送礼物
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].coin 用户余额
	 * @return string info[0].gifttoken 礼物标识
	 * @return string msg 提示信息
	 */
	public function sendGift() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $giftid=\App\checkNull($this->giftid);
        $nums=\App\checkNull($this->nums);
        
        if($uid<0 || $token=='' || $touid=='' || $giftid<1 || $nums<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
		$touid_a=array_filter(preg_split('/,|，/',$touid));
        if(!$touid_a){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Gift();
		$res = $domain->sendGift($uid,$touid_a,$giftid,$nums);
        
		return $res;
	}

    /**
	 * 个人主页赠送礼物 
	 * @desc 用于用户在个人主页赠送礼物
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].coin 用户余额
	 * @return string msg 提示信息
	 */
	public function sendGiftHome() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $giftid=\App\checkNull($this->giftid);
        $nums=\App\checkNull($this->nums);
        
        if($uid<0 || $token=='' || $touid<1 || $giftid<1 || $nums<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Gift();
		$res = $domain->sendGiftHome($uid,$touid,$giftid,$nums);
        
		return $res;
	}

}
