<?php
namespace App\Api;

use PhalApi\Api;
use App\Domain\Cash as Domain_Cash;
/**
 * 提现
 */

class Cash extends Api {

	public function getRules() {
        return array(
			'setCash' => array(
				'accountid' => array('name' => 'accountid', 'type' => 'int',  'desc' => '账号ID'),
				'cashvote' => array('name' => 'cashvote', 'type' => 'int', 'desc' => '提现的票数'),
			),
			'setCashgift' => array(
				'accountid' => array('name' => 'accountid', 'type' => 'int',  'desc' => '账号ID'),
				'cashvote' => array('name' => 'cashvote', 'type' => 'int', 'desc' => '提现的票数'),
				'cashmoney' => array('name' => 'cashmoney', 'type' => 'int', 'desc' => '提现金额'),
			),
            'setUserAccount' => array(
                'type' => array('name' => 'type', 'type' => 'int', 'desc' => '账号类型，1表示支付宝，2表示微信，3表示银行卡'),
                'account_bank' => array('name' => 'account_bank', 'type' => 'string', 'default' => '', 'desc' => '银行名称'),
                'account' => array('name' => 'account', 'type' => 'string', 'desc' => '账号'),
                'name' => array('name' => 'name', 'type' => 'string', 'default' => '', 'desc' => '姓名'),
			),
            
            'delUserAccount' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'desc' => '账号ID'),
			),
        );
	}
	/**
	 * 我的收益：订单收益
	 * @desc 用于获取用户收益，包括可体现金额，今日可提现金额
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].votes 可提取映票数
	 * @return string info[0].votestotal 总映票
	 * @return string info[0].cash_rate 映票兑换比例
	 * @return string info[0].total 可体现金额
	 * @return string info[0].tips 温馨提示
	 * @return string msg 提示信息
	 */
	public function getProfit() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);	
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		} 
		
		$domain = new Domain_Cash();
		$info = $domain->getProfit($uid);
	 
		$rs['info'][0]=$info;
		return $rs;
	}
	/**
	 * 我的收益：礼物收益
	 * @desc 用于获取用户收益，包括可体现金额，今日可提现金额
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].votes 可提取映票数
	 * @return string info[0].votestotal 总映票
	 * @return string info[0].cash_rate 映票兑换比例
	 * @return string info[0].total 可体现金额
	 * @return string info[0].tips 温馨提示
	 * @return string msg 提示信息
	 */
	public function getGiftProfit() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);	
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		} 
		
		$domain = new Domain_Cash();
		$info = $domain->getGiftProfit($uid);
	 
		$rs['info'][0]=$info;
		return $rs;
	}
	
	/**
	 * 用户提现：订单收益提现
	 * @desc 用于进行用户提现
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setCash() {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('提现成功'), 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);		
        $accountid=\App\checkNull($this->accountid);		
        $cashvote=\App\checkNull($this->cashvote);		
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        if(!$accountid){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('请选择提现账号');
			return $rs;
        }
        
        if($cashvote<=0){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('请输入有效的提现金额');
			return $rs;
        }
		
        $data=array(
            'uid'=>$uid,
            'accountid'=>$accountid,
            'cashvote'=>$cashvote,
        );
        
		$domain = new Domain_Cash();
		$info = $domain->setCash($data);
        
		return $info;
	}
	
	/**
	 * 获取用户提现账号 
	 * @desc 用于获取用户提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[].id 账号ID
	 * @return string info[].type 账号类型
	 * @return string info[].account_bank 银行名称
	 * @return string info[].account 账号
	 * @return string info[].name 姓名
	 * @return string msg 提示信息
	 */
	public function getUserAccountList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);

        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}        
    

        $domain = new Domain_Cash();
        $info = $domain->getUserAccountList($uid);

		$rs['info']=$info;

		return $rs;
	}

	/**
	 * 设置用户提现账号
	 * @desc 用于设置用户提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setUserAccount() {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('添加成功'), 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $type=\App\checkNull($this->type);
        $account_bank=\App\checkNull($this->account_bank);
        $account=\App\checkNull($this->account);
        $name=\App\checkNull($this->name);

        if($type==3){
            if($account_bank==''){
                $rs['code'] = 1001;
                $rs['msg'] = \PhalApi\T('银行名称不能为空');
                return $rs;
            }
        }
        
        if($account==''){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('账号不能为空');
            return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}        
        
        $data=array(
            'uid'=>$uid,
            'type'=>$type,
            'account_bank'=>$account_bank,
            'account'=>$account,
            'name'=>$name,
            'addtime'=>time(),
        );
        
        $domain = new Domain_Cash();
        $result = $domain->setUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('添加失败，请重试');
            return $rs;
        }
        
        $rs['info'][0]=$result;

		return $rs;
	}


	/**
	 * 删除用户提现账号 
	 * @desc 用于删除用户提现账号
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function delUserAccount() {
		$rs = array('code' => 0, 'msg' => '删除成功', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $id=\App\checkNull($this->id);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}        
        
        $data=array(
            'uid'=>$uid,
            'id'=>$id,
        );
        
        $domain = new Domain_Cash();
        $result = $domain->delUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('删除失败，请重试');
            return $rs;
        }

		return $rs;
	}
    /**
	 * 用户提现：礼物收益提现
	 * @desc 用于进行用户提现：礼物收益提现
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setCashgift() {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('提现成功'), 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);		
        $accountid=\App\checkNull($this->accountid);		
        $cashvote=\App\checkNull($this->cashvote);		
        $cashmoney=\App\checkNull($this->cashmoney);		
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        if(!$accountid){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('请选择提现账号');
			return $rs;
        }
        
        if($cashvote<=0){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('请输入有效的提取映票数');
			return $rs;
        }
		
        $data=array(
            'uid'=>$uid,
            'accountid'=>$accountid,
            'cashvote'=>$cashvote,
            'money'=>$cashmoney,
        );
        
		$domain = new Domain_Cash();
		$info = $domain->setCashgift($data);
        
		return $info;
	}
    
}
