<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Agent as Domain_Agent;

/**
 * 分享赚钱
 */
 
class Agent extends Api {

	public function getRules() {
		return array(
            'setAgent' => array(
				'code' => array('name' => 'code', 'type' => 'string', 'desc' => '邀请码'),
			),
		);
	}
    
	/**
	 * 检测 
	 * @desc 用于检测信息
	 * @return int code 操作码，0表示成功
	 * @return array info 
     * @return string info[0].ismust 是否必填，0否1是
	 * @return string info[0].isfill 是否已填，0否1是
	 * @return string msg 提示信息
	 */
	public function check() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Agent();
		$result = $domain->check($uid);
        
		return $result;
	}

	/**
	 * 填写邀请码 
	 * @desc 用于用户填写邀请码
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function setAgent() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $code=\App\checkNull($this->code);
        
        if($code==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('请填写邀请码');
            return $rs;	
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Agent();
		$result = $domain->setAgent($uid,$code);

		return $result;
	}


}
