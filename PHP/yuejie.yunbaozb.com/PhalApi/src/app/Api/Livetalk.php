<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Livetalk as Domain_Livetalk;

/**
 * 私信：音视频聊天
 */
 
class Livetalk extends Api {

	public function getRules() {
		return array(
            'start' => array(
				'roomuid' => array('name' => 'roomuid', 'type' => 'int', 'desc' => '音视频房间主播ID'),
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方主播ID'),
				'type' => array('name' => 'type', 'type' => 'int', 'desc' => '类型：1：视频聊天；2：音频聊天'),
			),
            
            'stop' => array(
				'roomuid' => array('name' => 'roomuid', 'type' => 'int', 'desc' => '音视频房间主播ID'),
			),
            
            'changeLive' => array(
				'type' => array('name' => 'type', 'type' => 'int', 'desc' => '状态，0关播1直播'),
			),
            
		);
	}
    
	/**
	 * 开启聊天室 
	 * @desc 用于开启聊天室
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].stream 流名
	 * @return string info[0].showid 直播标识
	 * @return string info[0].chatserver socket地址
	 * @return string info[0].votestotal 总映票
	 * @return string info[0].agentcode 邀请码
	 * @return string msg 提示信息
	 */
	public function start() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->roomuid);
        $touid=\App\checkNull($this->touid);
        $type=\App\checkNull($this->type);
       
       
        if($uid<1 || $touid==''){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        $nowtime=time();
        $data=[
            'uid'=>$uid,
            'touid'=>$touid,
            'addtime'=>$nowtime,
            'showid'=>$nowtime,
            'islive'=>"1",
            'type'=>$type,
        ];
       
        $domain = new Domain_Livetalk();
		$res = $domain->start($data);
        
		return $res;
	}

	
	/**
	 * 关闭聊天室 
	 * @desc 用于关闭聊天室
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[0].title 标题
	 * @return string info[0].length 时长
	 * @return string info[0].nums 累计人数
	 * @return string msg 提示信息
	 */
	public function stop() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=\App\checkNull($this->roomuid);//房间主播ID
        if($uid<1 ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
	
        $domain = new Domain_Livetalk();
		$res = $domain->stop($uid);
        
		return $res;
	}

	/**
	 * 修改直播状态 
	 * @desc 用于开启聊天室
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function changeLive() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);//房间主播ID
        $token=\App\checkNull($this->token);
        
        $type=\App\checkNull($this->type);
        
        if($uid<1 || $token==''){
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
        
        $domain = new Domain_Livetalk();
		$res = $domain->changeLive($uid,$type);
	
        
		return $res;
	}


}
