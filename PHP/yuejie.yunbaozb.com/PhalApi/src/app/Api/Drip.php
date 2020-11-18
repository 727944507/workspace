<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Drip as Domain_Drip;
use App\Domain\User as Domain_User;

/**
 * 滴滴快速下单
 */
 
class Drip extends Api {

	public function getRules() {
		return array(
            'setDrip' => array(
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
				'levelid' => array('name' => 'levelid', 'type' => 'int', 'desc' => '段位levelid，0不限'),
				'sex' => array('name' => 'sex', 'type' => 'int', 'desc' => '性别，0不限1男2女'),
				'type' => array('name' => 'type', 'type' => 'int', 'desc' => '时间类型，0今天1明天2后天'),
				'svctm' => array('name' => 'svctm', 'type' => 'string',  'desc' => '时间 H:i'),
				'nums' => array('name' => 'nums', 'type' => 'int',  'desc' => '数量'),
				'des' => array('name' => 'des', 'type' => 'string', 'desc' => '备注'),
			),
			'getMyDrip' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'getLiveid' => array(
				'dripid' => array('name' => 'dripid', 'type' => 'int', 'desc' => '订单ID'),
				'lastid' => array('name' => 'lastid', 'type' => 'int', 'desc' => '最后一条记录ID'),
			),
            
            'selectLive' => array(
				'dripid' => array('name' => 'dripid', 'type' => 'int', 'desc' => '订单ID'),
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'paytype' => array('name' => 'paytype', 'type' => 'int', 'default'=>'0', 'desc' => '支付方式，0余额1支付宝2微信3苹果4google '),
			),
            
            'getDripList' => array(
				'lastid' => array('name' => 'lastid', 'type' => 'int', 'desc' => '最后一条记录ID'),
			),
            
            'grapDrip' => array(
				'dripid' => array('name' => 'dripid', 'type' => 'int', 'desc' => '订单ID'),
			),
            
            'cancelDrip' => array(
				'dripid' => array('name' => 'dripid', 'type' => 'int', 'desc' => '订单ID'),
				'content' => array('name' => 'content', 'type' => 'string', 'desc' => '内容'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名，uid  token dripid content'),
			),
		);
	}
    
	/**
	 * 检测 
	 * @desc 用于用户检测是否已下单
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string info[0].status 是否已下单，0否1是
	 * @return string msg 提示信息
	 */
	public function checkDrip() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Drip();
		$status = $domain->checkDrip($uid);

        $rs['info'][0]['status']=$status;
		return $rs;
	}
    
	/**
	 * 滴滴快速下单 
	 * @desc 用于滴滴快速下单
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setDrip() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $skillid=\App\checkNull($this->skillid);
        $levelid=\App\checkNull($this->levelid);
        $sex=\App\checkNull($this->sex);
        $type=\App\checkNull($this->type);
        $svctm=\App\checkNull($this->svctm);
        $nums=\App\checkNull($this->nums);
        $des=\App\checkNull($this->des);
        
        if($uid<1 || $token=='' || $skillid<1 || $type<0 || $type>2 || $sex<0 || $sex>2 || $svctm=='' || $nums<1 ){
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
		
		$islimit=\App\getBanstatus($uid,"2");
		
        if($islimit && $islimit['isbanorder']=='1'){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('取消次数已达上限,'.$islimit['endtime'].'前禁止下单');
			return $rs;
        }
		
		
		$livelimit=\App\getBanstatus($uid,"0");
        if($livelimit  && $livelimit['isbanorder']=='1'){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('截止到'.$livelimit['endtime'].'您已被禁止接单');
			return $rs;
        }
        
        $data=[
            'uid'=>$uid,
            'skillid'=>$skillid,
            'levelid'=>$levelid,
            'sex'=>$sex,
            'type'=>$type,
            'svctm'=>$svctm,
            'nums'=>$nums,
            'des'=>$des,
        ];
        
        $domain = new Domain_Drip();
		$res = $domain->setDrip($data);

		return $res;
	}

	/**
	 * 滴滴订单 
	 * @desc 用于获取我的滴滴订单
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return object info[].skill 技能信息
	 * @return string info[].count 抢单人数
	 * @return string info[].addtime 时间
	 * @return string info[].level 等级
     * @return string info[].svctm 服务时间 计时用
	 * @return string info[].datesvctm 格式化服务时间
	 * @return string info[].status 状态，0抢单中，1已接单，-1已取消，-2已超时
	 * @return string msg 提示信息
	 */
	public function getMyDrip() {
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
        
        
        $domain = new Domain_Drip();
		$list = $domain->getMyDrip($uid,$p);

        $rs['info']=$list;
		return $rs;
	}
    
	/**
	 * 大神列表 
	 * @desc 用于获取抢单的大神列表
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return object info[].userinfo 用户信息
	 * @return object info[].authinfo 用户技能信息
	 * @return string info[].id 记录ID
	 * @return string msg 提示信息
	 */
	public function getLiveid() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $dripid=\App\checkNull($this->dripid);
        $lastid=\App\checkNull($this->lastid);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Drip();
		$res = $domain->getLiveid($uid,$dripid,$lastid);
        
		return $res;
	}

	/**
	 * 选择大神 
	 * @desc 用于选择大神进行下单
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[0].orderno 订单号
	 * @return string info[0].orderid 订单ID
	 * @return string info[0].total 支付价格
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
	public function selectLive() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $dripid=\App\checkNull($this->dripid);
        $liveuid=\App\checkNull($this->liveuid);
        $paytype=\App\checkNull($this->paytype);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Drip();
		$res = $domain->selectLive($uid,$dripid,$liveuid,$paytype);
        
		return $res;
	}

	/**
	 * 抢单大厅 
	 * @desc 用于获取抢单大厅订单列表
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return object info[].userinfo 用户信息
	 * @return object info[].skill 技能信息
	 * @return string info[].coin 单价
	 * @return string info[].total 总价
	 * @return string info[].fee 手续费
	 * @return string info[].profit 实际收益
	 * @return string info[].svctm 服务时间
	 * @return string info[].addtime 发布时间
     * @return string info[].datesvctm 格式化服务时间
	 * @return string info[].isgrap 是否抢单，0否1是
	 * @return string msg 提示信息
	 */
	public function getDripList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $lastid=\App\checkNull($this->lastid);
        
        
        $domain = new Domain_Drip();
		$list = $domain->getDripList($uid,$lastid);

        $rs['info']=$list;
		return $rs;
	}

	/**
	 * 抢单 
	 * @desc 用于获取抢单大厅订单列表
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function grapDrip() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $dripid=\App\checkNull($this->dripid);
        
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken!=0){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Drip();
		$res = $domain->grapDrip($uid,$dripid);
        
		return $res;
	}
    
    /**
	 * 取消原因 
	 * @desc 用于获取取消原因列表
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return array info[0].list 原因列表
	 * @return string info[0].list[].name 原因
	 * @return string info[0].tips 提示信息
	 * @return string msg 提示信息
	 */
	public function getDripCancel() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $domain = new Domain_Drip();
		$list = $domain->getDripCancel();
        $tips='';
        $configpri=\App\getConfigPri();
        if($configpri['drip_times']>0){
            $tips='每天仅有'.$configpri['drip_times'].'次取消快速下单的机会，请谨慎操作';
        }
        $rs['info'][0]['list']=$list;
        $rs['info'][0]['tips']=$tips;
        
		return $rs;
	}

    /**
	 * 取消订单
	 * @desc 用于用户取消订单
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function cancelDrip() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $dripid=\App\checkNull($this->dripid);
        $content=\App\checkNull($this->content);
        $sign=\App\checkNull($this->sign);
        
        if($content=='' || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'dripid'=>$dripid,
            'content'=>$content,
        );
        
       /*  $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        } */
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken!=0){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $islimit=\App\cancel_order_limit($uid);
		
        if($islimit){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('可用取消次数已达上限');
			return $rs;
        }
        
        $domain = new Domain_Drip();
		$res = $domain->cancelDrip($uid,$dripid,$content);
        
		return $res;
	}

    /**
	 * 滴滴订单提示
	 * @desc 用于滴滴订单提示信息
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
     * @return string info[0].tips 提示内容
     * @return string info[0].isauth 是否认证，0否1是
	 * @return string msg 提示信息
	 */
	public function getDripTips() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken!=0){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $Domain_User = new Domain_User();
		$isauth = $Domain_User->isauth($uid);
        
        $info['isauth']=$isauth;
        
        $domain = new Domain_Drip();
		$res = $domain->getDripTips($uid);
        
        $tips='';
        if($res){
            $tips='有大神抢到了您的订单';
        }
        
        $info['tips']=$tips;
        
        $rs['info'][0]=$info;
        
		return $rs;
	}




}
