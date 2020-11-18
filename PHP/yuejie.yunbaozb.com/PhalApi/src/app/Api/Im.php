<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Im as Domain_Im;

/**
 * 私信
 */
 
class Im extends Api {

	public function getRules() {
		return array(
            'getMultiInfo' => array(
				'uids' => array('name' => 'uids', 'type' => 'string', 'desc' => '用户ID，多个以逗号分割'),
			),
			'getSysNotice' => array(
				'p' => array('name' => 'p', 'type' => 'string', 'desc' => '页码'),
			),
			'getStatus' => array(
			),
		);
	}
    
	/**
	 * 获取多用户信息 
	 * @desc 用于获取多用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function getMultiInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $uids=\App\checkNull($this->uids);
        if($uids==''){
            //$rs['code'] = 1001;
			//$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
		$uids_a=preg_split('/,|，/',$uids);
        
        $list=[];
		foreach ($uids_a as $k=>$userId) {
            if($userId > 0){
				$userinfo= \App\getUserInfo($userId);
				$userinfo['u2t']=\App\isAttent($uid,$userId);
				$userinfo['isblack']=\App\isBlack($uid,$userId);
                $list[]=$userinfo;
			}
		}

        $rs['info']=$list;
		return $rs;
	}


	/**
	 * 系统通知 
	 * @desc 用于获取系统通知
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].content 信息内容
	 * @return string info[].addtime 时间
	 * @return string msg 提示信息
	 */
	public function getSysNotice() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $p=\App\checkNull($this->p);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Im();
		$list = $domain->getSysNotice($uid,$p);

        $rs['info']=$list;
		return $rs;
	}   
	/**
	 * 系统通知 :读取状态
	 * @desc 用于获取系统通知
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].content 信息内容
	 * @return string info[].addtime 时间
	 * @return string msg 提示信息
	 */
	public function getStatus() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
/* 
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		} */
        
        $domain = new Domain_Im();
		$info = $domain->getStatus($uid);

        $rs['info'][0]=$info;
		return $rs;
	}


}
