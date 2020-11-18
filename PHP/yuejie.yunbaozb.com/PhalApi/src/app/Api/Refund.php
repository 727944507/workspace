<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Refund as Domain_Refund;

/**
 * 退款管理
 */
 
class Refund extends Api {

	public function getRules() {
		return array(
            'setRefund' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '接单主播ID'),
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单ID'),
				'content' => array('name' => 'content', 'type' => 'string', 'desc' => '内容'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token  touid content'),
			),
			'getRefundinfo' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单ID'),
			),
			'setRefundStatus' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单ID'),
				'status' => array('name' => 'status', 'type' => 'int', 'desc' => '状态：4：拒绝退款；5：同意退款；6：退款申诉：等待平台退款'),
			),
		);
	}

	
	/**
	 * 申请退款 
	 * @desc 用于 用户申请退款
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function setRefund() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        $orderid=\App\checkNull($this->orderid);
        $content=\App\checkNull($this->content);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $touid < 1 || $content == ''  || $sign=='' || $orderid==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'touid'=>$touid,
            'content'=>$content,
        );
        
       /*  $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        } */
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Refund();
		$res = $domain->setRefund($uid,$touid,$orderid,$content);
        
		return $res;
	}
	
	/**
	 * 退款信息
	 * @desc 用于 用户退款信息
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string msg 提示信息
	 */
	public function getRefundinfo() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        
        if($uid<1 || $token=='' || $orderid==''){
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
        
        
        $domain = new Domain_Refund();
		$res = $domain->getRefundinfo($uid,$orderid);
        
		return $res;
	}
	
	/**
	 * 退款理由列表
	 * @desc 用于 获取 退款理由列表
	 * @return int code 操作码，0表示成功
	 * @return array info
	 * @return string info[].name 内容
	 * @return string msg 提示信息
	 */
	public function getRefundcat() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $domain = new Domain_Refund();
		$list = $domain->getRefundcat();
        
        $rs['info']=$list;
		return $rs;
	}
	
	
	/**
	 * 更新退款状态
	 * @desc 用于 接单后：更新退款状态
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setRefundStatus() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        $status=\App\checkNull($this->status);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_Refund();
		$info = $domain->setRefundStatus($uid,$orderid,$status);
        
		return $info;
	}
	
	


}
