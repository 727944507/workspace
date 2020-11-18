<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Foot as Domain_Foot;

/**
 * 足迹
 */
 
class Foot extends Api {

	public function getRules() {
		return array(
			'getVisit' => array(
				'lasttime' => array('name' => 'lasttime', 'type' => 'int', 'desc' => '最后一条的时间addtime'),
			),
            'getView' => array(
				'lasttime' => array('name' => 'lasttime', 'type' => 'int', 'desc' => '最后一条的时间addtime'),
			),
		);
	}
    
	/**
	 * 最近来访 
	 * @desc 用于获取最近来访用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].nums 次数
	 * @return string info[].addtime 时间
	 * @return string info[].datetime 格式化时间
	 * @return object info[].userinfo 用户信息
	 * @return string msg 提示信息
	 */
	public function getVisit() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $lasttime=\App\checkNull($this->lasttime);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Foot();
		$list = $domain->getVisit($uid,$lasttime);

        $rs['info']=$list;
		return $rs;
	}

	/**
	 * 浏览足迹 
	 * @desc 用于获取浏览用户列表
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].addtime 时间
	 * @return string info[].datetime 格式化时间
	 * @return object info[].userinfo 用户信息
	 * @return string msg 提示信息
	 */
	public function getView() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $lasttime=\App\checkNull($this->lasttime);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Foot();
		$list = $domain->getView($uid,$lasttime);

        $rs['info']=$list;
		return $rs;
	}

	/**
	 * 清空浏览足迹 
	 * @desc 用于清空浏览列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function clearView() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Foot();
		$list = $domain->clearView($uid);
        
		return $rs;
	}
	
	/**
	 * 清空来访记录
	 * @desc 用于清空来访记录
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function clearVisit() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Foot();
		$list = $domain->clearVisit($uid);
        
		return $rs;
	}

}
