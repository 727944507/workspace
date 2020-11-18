<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Orders as Domain_Orders;

/**
 * 订单
 */
 
class Orders extends Api {

	public function getRules() {
		return array(
            'getOrdersMore' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
			),
            
            'getPay' => array(
				'type' => array('name' => 'type', 'type' => 'int','desc' => 'APP类型，1是安卓，2是IOS'),
			),
			'checkOrder' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
			),
            
            'setOrder' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
				'type' => array('name' => 'type', 'type' => 'int', 'desc' => '时间类型，0今天1明天2后天'),
				'svctm' => array('name' => 'svctm', 'type' => 'string',  'desc' => '时间 H:i'),
				'nums' => array('name' => 'nums', 'type' => 'int',  'desc' => '数量'),
				'des' => array('name' => 'des', 'type' => 'string', 'desc' => '备注'),
				'paytype' => array('name' => 'paytype', 'type' => 'int', 'default'=>'0', 'desc' => '支付方式，0余额1支付宝2微信3苹果4google '),
			),
            
            'getOrderDetail' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
			),
            
            'getOrderDetails' => array(
				'orderids' => array('name' => 'orderids', 'type' => 'string', 'desc' => '订单号,多个用,拼接'),
			),
            
            'cancelOrder' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
				'reason' => array('name' => 'reason', 'type' => 'string', 'desc' => '原因ID,多个用,拼接'),
			),
            
            'receiptOrder' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
			),
            
            'refuseOrder' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
			),
            
            'completeOrder' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
			),
            
            'getOrdering' => array(
				'touid' => array('name' => 'touid', 'type' => 'int', 'desc' => '对方ID'),
			),
			'upReceptOrder' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
				// 'recept_status' => array('name' => 'recept_status', 'type' => 'int', 'desc' => '状态：：-1：已申请立即服务；0：默认倒计时陪玩状态;1：拒绝立即服务；2：同意立即服务'),
			),
			'upReceptStatus' => array(
				'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单号'),
				'recept_status' => array('name' => 'recept_status', 'type' => 'int', 'desc' => '状态：：-1：已申请立即服务；0：默认倒计时陪玩状态;1：拒绝立即服务；2：同意立即服务'),
			),
			'getReceptDetails' => array(
				'orderids' => array('name' => 'orderids', 'type' => 'string', 'desc' => '订单号,多个用,拼接'),
			),
		);
	}
    
    /**
	 * 订单列表
	 * @desc 用于获取订单列表
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return array info[0].listing 进行中的订单
	 * @return string info[0].listing[].id 订单ID
	 * @return string info[0].listing[].skillid 技能ID
	 * @return string info[0].listing[].total 总价
	 * @return string info[0].listing[].fee 手续费
	 * @return string info[0].listing[].profit 预计收益
	 * @return string info[0].listing[].des 订单备注
	 * @return string info[0].listing[].status 订单状态 -4 已超时 -3 已拒绝-2 完成 -1取消 0 待支付 1待接单 2已接单
	 * @return object info[0].listing[].userinfo 要显示的用户信息
	 * @return string info[0].listing[].iscommnet 是否评论0否1是
	 * @return object info[0].listing[].comment 评论
	 * @return string info[0].listing[].comment.content 评论内容
	 * @return string info[0].listing[].comment.star 星级
	 * @return array  info[0].listing[].comment.label_a 标签列表
	 * @return string info[0].listing[].comment.label_a[] 标签
     * @return object info[0].listing[].skill 技能
	 * @return string info[0].listing[].skill.id 技能ID，0表示已移除 不能再次下单
	 * @return string info[0].listing[].skill.name 名字
	 * @return string info[0].listing[].skill.method 单位
	 * @return string info[0].listing[].skill.thumb 图片
     * @return object info[0].listing[].auth 技能认证信息
	 * @return string info[0].listing[].auth.switch 开关，0关1开
	 * @return string info[0].listing[].auth.coin 价格
	 * @return array  info[0].list 历史订单
	 * @return string info[0].list[].id 订单ID
	 * @return string info[0].list[].skillid 技能ID
	 * @return string info[0].list[].total 总价
     * @return string info[0].list[].fee 手续费
	 * @return string info[0].list[].profit 预计收益
	 * @return string info[0].list[].des 订单备注
	 * @return string info[0].list[].status 订单状态 -4 已超时 -3 已拒绝-2 完成 -1取消 0 待支付 1待接单 2已接单
	 * @return object info[0].list[].userinfo 要显示的用户信息
	 * @return string info[0].list[].iscommnet 是否评论0否1是
	 * @return object info[0].list[].comment 评论
	 * @return string info[0].list[].comment.content 评论内容
	 * @return string info[0].list[].comment.star 星级
	 * @return array  info[0].list[].comment.label_a 标签列表
	 * @return string info[0].list[].comment.label_a[] 标签
     * @return object info[0].list[].skill 技能
	 * @return string info[0].list[].skill.id 技能ID，0表示已移除 不能再次下单
	 * @return string info[0].list[].skill.name 名字
	 * @return string info[0].list[].skill.method 单位
	 * @return string info[0].list[].skill.thumb 图片
     * @return object info[0].list[].auth 技能认证信息
	 * @return string info[0].list[].auth.switch 开关，0关1开
	 * @return string info[0].list[].auth.coin 价格
	 * @return string msg 提示信息
	 */
	public function getOrders() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
		$domain = new Domain_Orders();
		$listing = $domain->getOrdersing($uid);
        
		$list = $domain->getOrders($uid,1);
        
        $info['listing']=$listing;
        $info['list']=$list;
        
        $rs['info'][0] =$info;
        
		return $rs;
	}

    /**
	 * 历史订单列表
	 * @desc 用于获取历史订单列表(分页)
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string info[].id 订单ID
	 * @return string info[].skillid 技能ID
	 * @return string info[].total 总价
     * @return string info[].fee 手续费
	 * @return string info[].profit 预计收益
	 * @return string info[].des 订单备注
	 * @return string info[].status 订单状态 -4 已超时 -3 已拒绝-2 完成 -1取消 0 待支付 1待接单 2已接单
	 * @return object info[].userinfo 要显示的用户信息
	 * @return string info[].iscommnet 是否评论0否1是
	 * @return object info[].comment 评论
	 * @return string info[].comment.content 评论内容
	 * @return string info[].comment.star 星级
	 * @return array  info[].comment.label_a 标签列表
	 * @return string info[].comment.label_a[] 标签
     * @return object info[].skill 技能
	 * @return string info[].skill.id 技能ID，0表示已移除 不能再次下单
	 * @return string info[].skill.name 名字
	 * @return string info[].skill.method 单位
	 * @return string info[].skill.thumb 图片
     * @return object info[].auth 技能认证信息
	 * @return string info[].auth.switch 开关，0关1开
	 * @return string info[].auth.coin 价格
	 * @return string msg 提示信息
	 */
	public function getOrdersMore() {
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

		$domain = new Domain_Orders();
		$info = $domain->getOrders($uid,$p);
        
        $rs['info'] =$info;
        
		return $rs;
	}

    /**
	 * 获取支付
	 * @desc 用于获取下单页面支付列表
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string info[].id 订单ID
	 * @return string info[].skillid 技能ID
	 * @return string info[].total 总价
	 * @return string info[].des 订单备注
	 * @return string msg 提示信息
	 */
	public function getPay() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $type=\App\checkNull($this->type);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$userinfo=\App\getUserCoin($uid);

        $configpri=\App\getConfigPri();
        $aliapp_switch=$configpri['aliapp_switch'];
        $wx_switch=$configpri['wx_switch'];
        
        $paylist=[];
        
        if($aliapp_switch==1){
            $paylist[]=[
                'id'=>'1',
                'name'=>\PhalApi\T('支付宝支付'),
                'thumb'=>\App\get_upload_path("/static/app/pay/ali.png"),
            ];
        }
        
        if($wx_switch==1){
            $paylist[]=[
                'id'=>'2',
                'name'=>\PhalApi\T('微信支付'),
                'thumb'=>\App\get_upload_path("/static/app/pay/wx.png"),
            ];
        }

        if($type==1){
            /* Android */            
        }
        
        if($type==2){
            /* IOS */
        }
        
        $paylist[]=[
                'id'=>'0',
                'name'=>\PhalApi\T('余额支付'),
                'thumb'=>\App\get_upload_path("/static/app/pay/coin.png"),
            ];
        
        $userinfo['paylist'] =$paylist;
        
        
        
        $rs['info'][0] =$userinfo;
        
		return $rs;
	}

    /**
	 * 获取订单状态
	 * @desc 用于获取订单状态
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string info[0].status 订单状态，-4已超时-3拒绝-2已完成-1取消0待支付1已支付2已接单
	 * @return string msg 提示信息
	 */
	public function checkOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        if($orderid<1){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }

        $data=[
            'uid'=>$uid,
            'id'=>$orderid,
        ];
		$domain = new Domain_Orders();
		$res = $domain->checkOrder($data);
        if(!$res){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
        
        $info['status']=$res['status'];
        
        $rs['info'][0] = $info;
		return $rs;
	}

    /**
	 * 下单
	 * @desc 用于下单
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
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
	public function setOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $skillid=\App\checkNull($this->skillid);
        $type=\App\checkNull($this->type);
        $svctm=\App\checkNull($this->svctm);
        $nums=\App\checkNull($this->nums);
        $des=\App\checkNull($this->des);
        $paytype=\App\checkNull($this->paytype);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        if($liveuid<1 || $skillid<1 || $type<0 || $type>2 || $svctm=='' || $nums<1 || $paytype<0){
            $rs['code'] = 1001;
			$rs['msg'] = \PhalApi\T('信息错误');
			return $rs;
        }
		
		$islimit=\App\getBanstatus($uid,"1");
		
        if($islimit && $islimit['isbanorder']=='1'){
            $rs['code'] = 1002;
			// $rs['msg'] = \PhalApi\T('可用取消次数已达上限,截止到'.$islimit['endtime'].'禁止下单');
			$rs['msg'] = \PhalApi\T('取消次数已达上限,'.$islimit['endtime'].'前禁止下单');
			return $rs;
        }
		
		$livelimit=\App\getBanstatus($liveuid,"0");
        if($livelimit  && $livelimit['isbanorder']=='1'){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('对方截止到'.$livelimit['endtime'].'已被禁止接单');
			return $rs;
        }

        $data=[
            'uid'=>$uid,
            'liveuid'=>$liveuid,
            'skillid'=>$skillid,
            'type'=>$type,
            'svctm'=>$svctm,
            'nums'=>$nums,
            'des'=>$des,
            'paytype'=>$paytype,
        ];
		$domain = new Domain_Orders();
		$res = $domain->checkset($data);
        
		return $res;
	}
    
    /**
	 * 订单详情
	 * @desc 用于获取订单详情
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getOrderDetail() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        
        if($orderid<1){
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

		$domain = new Domain_Orders();
		$info = $domain->getOrderDetail($uid,$orderid);
        
        if(!$info){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('订单信息有误');
			return $rs;
        }
        
        $rs['info'][0]=$info;
        
		return $rs;
	}
    
    /**
	 * 多个订单详情
	 * @desc 用于获取订单详情,一次获取多个订单
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getOrderDetails() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderids=\App\checkNull($this->orderids);
        
        if($orderids<1){
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
        
        $list=[];
        
		$domain = new Domain_Orders();
        
        $orderids_a=preg_split('/，|,/',$orderids);
        $orderids_a=array_filter($orderids_a);
        foreach($orderids_a as $k=>$v){
            $info = $domain->getOrderDetail($uid,$v);
            if($info){
                $list[]=$info;
            }
        }
		
        
        $rs['info']=$list;
        
		return $rs;
	}
    
    /**
	 * 取消原因
	 * @desc 用于获取订单取消原因
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getCancelList() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

		$domain = new Domain_Orders();
		$list = $domain->getCancelList();
        
        $rs['info']=$list;
        
		return $rs;
	}

    /**
	 * 取消订单
	 * @desc 用于下单用户取消订单
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function cancelOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        $reason=\App\checkNull($this->reason);
        
        if($orderid<1 || $reason==''){
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
		$islimit=\App\cancel_order_limit($uid);
		
        if($islimit){
            $rs['code'] = 1002;
			$rs['msg'] = \PhalApi\T('可用取消次数已达上限');
			return $rs;
        }

		$domain = new Domain_Orders();
		$info = $domain->cancelOrder($uid,$orderid,$reason);
        
		return $info;
	}

    /**
	 * 接单
	 * @desc 用于接单
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function receiptOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_Orders();
		$info = $domain->receiptOrder($uid,$orderid);
        
		return $info;
	}

    /**
	 * 拒接
	 * @desc 用于拒绝接单
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function refuseOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_Orders();
		$info = $domain->refuseOrder($uid,$orderid);
        
		return $info;
	}

    /**
	 * 完成订单
	 * @desc 用于确认完成订单
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function completeOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_Orders();
		$info = $domain->completeOrder($uid,$orderid);
        
		return $info;
	}

    /**
	 * 获取进行中的订单
	 * @desc 用于获取用户间正在进行的订单信息
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string info[0].isexist 是否有订单，0否1是 
	 * @return object info[0].order 订单信息 
	 * @return string info[0].order.iscommnet 用户是否评论主播，0否1是
	 * @return object info[0].order.comment  评论内容
	 * @return string info[0].order.isevaluate  主播是否评价用户，0否1是
	 * @return object info[0].order.evaluate  评价内容
	 * @return string msg 提示信息
	 */
	public function getOrdering() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $touid=\App\checkNull($this->touid);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_Orders();
		$info = $domain->getOrdering($uid,$touid);
        
        
        $rs['info'][0]=$info;
        
		return $rs;
	}
	/**
	 * 接单后：点击立即服务状态
	 * @desc 用于 接单后：更新立即服务状态
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function upReceptOrder() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        // $recept_status=\App\checkNull($this->recept_status);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_Orders();
		$info = $domain->upReceptOrder($uid,$orderid);
        
		return $info;
	}
	/**
	 * 接单后：更改服务状态
	 * @desc 用于 接单后：更改服务状态
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function upReceptStatus() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderid=\App\checkNull($this->orderid);
        $recept_status=\App\checkNull($this->recept_status);
        
		$checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}

		$domain = new Domain_Orders();
		$info = $domain->upReceptStatus($uid,$orderid,$recept_status);
        
		return $info;
	}
	
	/**
	 * 多个已接单：服务状态订单详情
	 * @desc 用于获取 已接单：服务状态订单详情,一次获取多个订单
	 * @return int code 操作码，0表示成功， 1表示用户不存在
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getReceptDetails() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $orderids=\App\checkNull($this->orderids);
        
        if($orderids<1){
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
        
        $list=[];
        
		$domain = new Domain_Orders();
        
        $orderids_a=preg_split('/，|,/',$orderids);
        $orderids_a=array_filter($orderids_a);
        foreach($orderids_a as $k=>$v){
            $info = $domain->getReceptOrderDetail($uid,$v);
            if($info){
                $list[]=$info;
            }
        }
		
        
        $rs['info']=$list;
        
		return $rs;
	}
    

}
