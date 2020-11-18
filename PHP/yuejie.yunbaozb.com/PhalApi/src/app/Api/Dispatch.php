<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Dispatch as Domain_Dispatch;

/**
 * 派单
 */
 
class Dispatch extends Api {

	public function getRules() {
		return array(
            'send' => array(
				'stream' => array('name' => 'stream', 'type' => 'string', 'desc' => '流名'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
				'levelid' => array('name' => 'levelid', 'type' => 'int', 'defaulf'=>0, 'desc' => '段位levelid，0不限'),
				'sex' => array('name' => 'sex', 'type' => 'int', 'defaulf'=>0, 'desc' => '性别，0不限1男2女'),
				'age' => array('name' => 'age', 'type' => 'int', 'defaulf'=>0, 'desc' => '年龄，0不限，1-70，2-80，3-90，4-00，5-10'),
				'coin' => array('name' => 'coin', 'type' => 'int', 'defaulf'=>0, 'desc' => '价格，0不限'),
			),
            
            'upStatus' => array(
				'roomnum' => array('name' => 'roomnum', 'type' => 'int', 'desc' => '房间号'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),
		);
	}
    
	/**
	 * 主持人派单
	 * @desc 用于主持人派单
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function send() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $stream=\App\checkNull($this->stream);
        $skillid=\App\checkNull($this->skillid);
        $levelid=\App\checkNull($this->levelid);
        $sex=\App\checkNull($this->sex);
        $age=\App\checkNull($this->age);
        $coin=\App\checkNull($this->coin);
        
        if($uid<0 || $token=='' || $stream=='' || $skillid<1 ){
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
        
        $data=[
            'stream'=>$stream,
            'skillid'=>$skillid,
            'levelid'=>$levelid,
            'sex'=>$sex,
            'age'=>$age,
            'coin'=>$coin,
        ];
        
        $domain = new Domain_Dispatch();
		$res = $domain->send($uid,$data);
        
		return $res;
	}   

	/**
	 * 更新派单状态
	 * @desc 用于更新派单状态
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function upStatus() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $roomnum=\App\checkNull($this->roomnum);
        $sign=\App\checkNull($this->sign);
        
        if($roomnum<0 || $sign=='' ){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $checkdata=array(
            'roomnum'=>$roomnum,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $where=[
            'uid'=>$roomnum,
            'status'=>0,
        ];
        
        $data=['status'=>1];
        
        $domain = new Domain_Dispatch();
		$res = $domain->upInfo($where,$data);
        
		return $rs;
	}   
    

}
