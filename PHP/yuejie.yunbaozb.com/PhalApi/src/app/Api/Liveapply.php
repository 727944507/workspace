<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Liveapply as Domain_Liveapply;

/**
 * 申请主持
 */
 
class Liveapply extends Api {

	public function getRules() {
		return array(
            'apply' => array(
				'voice' => array('name' => 'voice', 'type' => 'string', 'desc' => '语音链接'),
				'length' => array('name' => 'length', 'type' => 'string', 'desc' => '时长(s)'),
			),
		);
	}
    
	/**
	 * 获取申请信息 
	 * @desc 用于获取多用户信息
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[0].status 状态，-1未申请0审核中1通过2拒绝
	 * @return string info[0].reason 原因
	 * @return string info[0].tips 公告
	 * @return string msg 提示信息
	 */
	public function getInfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        $configpri=\App\getConfigPri();
        $info=[
            'status'=>'-1',
            'reason'=>'',
            'tips'=>$configpri['liveapply_tips'],
        ];
        $domain = new Domain_Liveapply();
		$res = $domain->getInfo($uid);
        if($res){
            $info['status']=$res['status'];
            $info['reason']=$res['reason'];
        }
        
        $rs['info'][0]=$info;
        
		return $rs;
	}


	/**
	 * 申请 
	 * @desc 用于用户提交申请主持资格
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function apply() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $voice=\App\checkNull($this->voice);
        $length=\App\checkNull($this->length);
        
        if($uid<0 || $token=='' || $voice=='' || $length<=0){
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
            'voice'=>$voice,
            'length'=>$length,
        ];
        $domain = new Domain_Liveapply();
		$res = $domain->apply($uid,$data);
        
		return $res;
	}   


}
