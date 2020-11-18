<?php
namespace App\Domain;

use App\Model\Orders as Model_Orders;
use App\Domain\Skill as Domain_Skill;
use App\Domain\Comment as Domain_Comment;

class Orders {
    public function handelInfo($uid,$info){
        $overtime=$info['overtime'];
		$nowtime=time();
		$info['ishideok']="0";
		if($overtime>$nowtime){
			$info['ishideok']="1";
		}
		
        $info['svctm']=\App\handelsvctm($info['svctm']);
        
        /* 费用 */
        $info['fee']= $info['fee']==0? '0':'-'.$info['fee'];
		
        if($info['status']=='1'){
			//订单超时剩余时间
			$remainingtime=$info['addtime']+15*60-$nowtime;
			$info['remainingtime']=$remainingtime;
		}else{
			$info['remainingtime']=0;
		}
        
        if($uid==$info['uid']){
            $userinfo=\App\getUserInfo($info['liveuid']);
        }else{
            $userinfo=\App\getUserInfo($info['uid']);
        }
        unset($userinfo['birthday']);
        $info['userinfo']=$userinfo;
        
        /* 技能 */
        $Domain_Skill = new Domain_Skill();
        $skillinfo=$Domain_Skill->getSkill($info['skillid']);
        $skill=[
            'id'=>isset($skillinfo['id']) ? $skillinfo['id'] : '0',
            'name'=>isset($skillinfo['name']) ? $skillinfo['name'] : \PhalApi\T('已移除'),
            'method'=>isset($skillinfo['method']) ? $skillinfo['method'] : '',
            'thumb'=>isset($skillinfo['thumb']) ? $skillinfo['thumb'] : '',
        ];
        
        $info['skill']=$skill;
        
        /* 技能配置 */
        $auth=[
            'switch'=>'0',
            'coin'=>'0',
        ];
        $where=[
            'uid'=>$info['liveuid'],
            'skillid'=>$info['skillid'],
            'status'=>'1',
            'switch'=>'1',
        ];
        $authlist= $Domain_Skill->getSkillAuth($where);
        if($authlist){
            $auth['switch']='1';
            $auth['coin']=$authlist[0]['coin'];
        }
        
        $info['auth']=$auth;
        
        $info['skill']['coin']=$auth['coin'];
        
        $iscommnet='0';
        $comment=(object)[];
        if($info['status']==-2){
            $Domain_Comment=new Domain_Comment();
            $where=[
                'orderid'=>$info['id']
            ];
            
            $commentinfo=$Domain_Comment->getComment(1,$where);
            if($commentinfo){
                $iscommnet='1';
                $comment=$commentinfo[0];
            }
        }
        
        $info['iscommnet']=$iscommnet;
        $info['comment']=$comment;
        /* 主播给用户评价 */
        $isevaluate='0';
        $evaluate=(object)[];
        if($info['status']==-2){
            $Domain_Comment=new Domain_Comment();
            $where=[
                'orderid'=>$info['id']
            ];
            
            $evaluateinfo=$Domain_Comment->getEvaluate($where);
            if($evaluateinfo){
                $isevaluate='1';
                $evaluate=$evaluateinfo;
            }
        }
        
        $info['isevaluate']=$isevaluate;
        $info['evaluate']=$evaluate;
        
        unset($info['type']);
        unset($info['ambient']);
        unset($info['paytime']);
        unset($info['receipttime']);
        unset($info['oktime']);
        unset($info['canceltime']);
        unset($info['orderno']);
        unset($info['trade_no']);
        
        return $info;
    }
    /* 进行订单 */
	public function getOrdersing($uid) {
        
        $where=[
            'status > ?'=>'0',
			'status != ?'=>'5',
        ];
        $where['uid=? or liveuid=?']=[$uid,$uid];

        $model = new Model_Orders();
        $list= $model->getOrdersing($where);
        
        foreach($list as $k=>$v){
            $v=$this->handelInfo($uid,$v);
            $list[$k]=$v;
        }

		return $list;
	}
 
    /* 订单 */
	public function getOrders($uid,$p) {

        $where=[
            'status < ?'=>'0'
        ];
        $where['uid=? or liveuid=?']=[$uid,$uid];
        
        $model = new Model_Orders();
        $list= $model->getOrders($p,$where);
        
        foreach($list as $k=>$v){
            $v=$this->handelInfo($uid,$v);
            $list[$k]=$v;
        }

		return $list;
	}
    
    /* 订单状态 */
    public function checkOrder($data){
                
        $model = new Model_Orders();
		$info = $model->getOrderInfo($data);
        
        return $info;
    }

    /* 生成订单 */
	public function checkset($data) {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('下单成功'), 'info' => array());
        
        $uid=$data['uid'];
        $liveuid=$data['liveuid'];
        $type=$data['type'];
        $svctm=$data['svctm'];
        $paytype=$data['paytype'];
        $des=$data['des'];
        unset($data['svctm']);
        unset($data['type']);
        unset($data['paytype']);
        $data['type']=$paytype;
        
        if($uid==$liveuid){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('不能给自己下单');
			return $rs;
        }
        
        $svctminfo=\App\treatsvctm($type,$svctm);
        if($svctminfo['code']!=0){
            return $svctminfo;
        }
        $data['svctm']=$svctminfo['info']['svctm'];
        
        $nowtime=time();
        
        if(mb_strlen($des) > 50){
            $rs['code']=1005;
            $rs['msg']=\PhalApi\T('备注不能超过50字');
            return $rs;
        }
        
        $data['order_type']='0';
        
        $res=$this->setOrder($data);

		return $res;
	}
    /* 下单-支付 */
    public function setOrder($data){
        
        // $rs = array('code' => 0, 'msg' => \PhalApi\T('下单成功'), 'info' => array());
        $rs = array('code' => 0, 'msg' =>"", 'info' => array());
        $uid=$data['uid'];
        $liveuid=$data['liveuid'];
        $skillid=$data['skillid'];
        $nums=$data['nums'];
        
		$t2u = \App\isBlack($liveuid,$uid);
		if($t2u){
			$rs['code']=1010;
            $rs['msg']=\PhalApi\T('对方已将您拉黑，不能下单');
            return $rs;
		}
		
        $Domain_Skill=new Domain_Skill();
        
        $where=[
            'uid'=>$liveuid,
            'skillid'=>$skillid,
            'status'=>'1',
            'switch'=>'1',
        ];
        $order='id desc';
        $auth=$Domain_Skill->getSkillAuth($where,$order);
        if(!$auth){
            $rs['code']=1006;
            $rs['msg']=\PhalApi\T('该技能对方未认证或未开启');
            return $rs;
        }
        
        $authinfo=$auth[0];
        
        $total=$authinfo['coin'] * $nums;
        
        if($total<=0){
            $rs['code']=1007;
            $rs['msg']=\PhalApi\T('信息错误');
            return $rs;
        }
        
        $coinid=$authinfo['coinid'];
        

        $coininfo=$Domain_Skill->getCoin($coinid);
        
        $fee_base=isset($coininfo['fee']) ? $coininfo['fee'] : '0';
        
        $fee=$fee_base * $nums;
        
        $data['total']=$total;
        $data['fee']=$fee;
        $data['profit']=$total - $fee;
        
        
        $nowtime=time();
        
        $orderno=$uid.'_'.date('ymdHis').rand(100,999);
        $data['orderno']=$orderno;
        $data['addtime']=$nowtime;
		
		$skillinfo=$Domain_Skill->getSkill($skillid);
        $overtime=$data['svctm']+$skillinfo['methodminutes']*60;
        $data['overtime']=$overtime;//订单陪玩结束时间
        $paytype=$data['type'];
        
        
        if($paytype==0){
            /* 余额支付 */
            $res=\App\upCoin($uid,$total);
            if(!$res){
                $rs['code']=1008;
                $rs['msg']=\PhalApi\T('余额不足');
                return $rs;
            }
            
            $data['status']='1';
            if($data['order_type']==1){
                $data['status']='2';
            }
            $data['paytime']=$nowtime;
            
        }else if($paytype==1){
            /* 支付宝 */
        }else if($paytype==2){
            /* 微信 */
        }
        
		$model = new Model_Orders();
		$res = $model->setOrder($data);
        
        $ali=[
            'partner'=>'',
            'seller_id'=>'',
            'key'=>'',
        ];
        $configpri = \App\getConfigPri();
		
		$time2 = time();
		$sign = "";
		$noceStr = md5(rand(100,1000).time());//获取随机字符串
        $wx=[
            'appid'=>$configpri['wx_appid'],
            'noncestr'=>$noceStr,
            'package'=>'Sign=WXPay',
            'partnerid'=>$configpri['wx_mchid'],
            'prepayid'=>"",
            'timestamp'=>(string)$time2,
        ];
		$wx["sign"] =\App\sign($wx,$configpri['wx_key']);//生成签名
       
        if($paytype==0){
            $record=[
                'type'=>'0',
                'action'=>'1',
                'uid'=>$uid,
                'touid'=>$liveuid,
                'actionid'=>$res['id'],
                'nums'=>$nums,
                'total'=>$total,
                'addtime'=>$nowtime,
            ];
            \App\addCoinRecord($record);
            
            /* 余额支付 下单即支付 立即发送IM*/
            $imdata=$this->handelInfo($liveuid,$res);
            $userinfo=\App\getUserInfo($uid);

            $imdata['tips']=$userinfo['user_nickname'].'给你下了订单';
            $imdata['tips_en']=$userinfo['user_nickname'].' placed an order for you';
            $this->sendImOrder($liveuid,$imdata);
            
            if($data['order_type']!=1){
                $msg=\PhalApi\T('订单已收到，会尽快确认');
                $this->sendIm($liveuid,$uid,$msg);
            }
            
        }else if($paytype==1){
            /* 支付宝 */
			if($configpri['aliapp_partner']=='' || $configpri['aliapp_seller_id']=='' || $configpri['aliapp_key']==''){
				$rs['code']=1011;
				$rs['msg']=\PhalApi\T('支付宝未配置');
				return $rs;
			}
            $ali=[
                'partner'=>$configpri['aliapp_partner'],
                'seller_id'=>$configpri['aliapp_seller_id'],
                'key'=>$configpri['aliapp_key'],
            ];
            
        }else if($paytype==2){
            /* 微信 */
            $configpri = \App\getConfigPri();
            
            $url=\App\get_upload_path('/appapi/orderback/notify_wx');
            $body='订单支付';
            
            $res1=\App\wxPay($orderno,$total,$url,$body);
            if($res1['code']!=0){
                return $res;
				/* $info['orderno']=$orderno;
				$info['orderid']=$res['id'];
				$info['total']=(string)$total;
				$info['ali']=$ali;
				$info['wx']=$wx;
				$rs['info'][0]=$info;
				return $rs; */
            }
			$res1['info']['timestamp']=(string)$res1['info']['timestamp'];
            $wx=$res1['info'];
        }
	
        
        $info['orderno']=$orderno;
        $info['orderid']=$res['id'];
        $info['total']=(string)$total;
        $info['ali']=$ali;
        $info['wx']=$wx;
        
        $rs['info'][0]=$info;

		return $rs;
    }

	/**
	* sign拼装获取
	*/
	protected function sign($param,$key){
		$sign = "";
		foreach($param as $k => $v){
			$sign .= $k."=".$v."&";
		}
		$sign .= "key=".$key;
		$sign = strtoupper(md5($sign));
		return $sign;
	
	}
	
    /* 获取取消原因 */
    public function getCancelList(){
        $key='getCancelList';
        $list=\App\getcaches($key);
        if(!$list){
            $model = new Model_Orders();
            $list=$model->getCancelList();
            if($list){
                \App\setcaches($key,$list);
            }
        }
        
        foreach($list as $k=>$v){
            if(\PhalApi\DI()->lang=='en' && $v['name_en']!=''){
                $v['name']=$v['name_en'];
            }
            unset($v['name_en']);
            $list[$k]=$v;
        }

        return $list;
    }
    
    /* 取消订单 */
    public function cancelOrder($uid,$orderid,$reasonid){
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        $where=[
            'id'=>$orderid
        ];
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
        if(!$info){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        if($info['uid']!=$uid){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        $nowtime=time();
        $status=$info['status'];
        
        if($status!=1 && $status!=0 ){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('该订单不能取消');
			return $rs;
        }
        $reason='';
        /* 原因处理 */
        if($reasonid!=''){
            $reasonid_a=preg_split('/，|,/',$reasonid);
            $reasonid_a=array_filter($reasonid_a);

            $list = $this->getCancelList();
            
            foreach($reasonid_a as $k=>$v){
                foreach($list as $k1=>$v1){
                    if($v==$v1['id']){
                        $reason.=$v1['name'].';';
                    }
                }
            }
        }
        
        if($reason==''){
            $rs['code']=1005;
			$rs['msg']=\PhalApi\T('请选择原因');
			return $rs;
        }
        
        $tips='订单：您取消了一个订单';
        $tips_en='Order: you canceled an order';
        if($status==1){
            /* 已付款 退回 */
            $total=$info['total'];
            
            $res=\App\addCoin($uid,$total);
            $record=[
                'type'=>'1',
                'action'=>'2',
                'uid'=>$uid,
                'touid'=>$uid,
                'actionid'=>$info['id'],
                'nums'=>$info['nums'],
                'total'=>$total,
                'addtime'=>$nowtime,
            ];
            \App\addCoinRecord($record);
            
            $tips='订单：您取消了一个订单，费用'.$total.'已退回';
            $tips_en='Order: you canceled an order and the fee '.$total.' has been returned';
        }
        
        /* 更新订单 */
        $data=[
            'status'=>-1,
            'canceltime'=>$nowtime,
            'reason'=>$reason,
        ];
        
        $where=[
            'id'=>$info['id'],
        ];
        
        $res = $model->upOrder($where,$data);
        
        
        /* 发送IM*/
        /* 发给自己 */
        $info['status']='-1';
        $imdata=$this->handelInfo($uid,$info);
        $imdata['tips']=$tips;
        $imdata['tips_en']=$tips_en;
        $this->sendImOrder($uid,$imdata);
        /* 发给对方 */
        $imdata2=$this->handelInfo($info['liveuid'],$info);
        $imdata2['tips']='订单：很抱歉，用户取消了您的订单哦～';
        $imdata2['tips_en']='Order: sorry, the user canceled your order~';
        
        $this->sendImOrder($info['liveuid'],$imdata2);
        
        
        return $rs;
    }
    
    /* 获取订单详情 */
    public function getOrderDetail($uid,$orderid){
        
        $where=[
            'id=?'=>$orderid,
            'uid=? or liveuid=?'=>[$uid,$uid]
        ];
        
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
        
        if($info){
            $info=$this->handelInfo($uid,$info);
        }
        
        return $info;
    }
	/* 获取服务状态的订单详情 */
    public function getReceptOrderDetail($uid,$orderid){
        
        $where=[
            'id=?'=>$orderid,
            'uid=? or liveuid=?'=>[$uid,$uid]
        ];
        
        $model = new Model_Orders();
		$info = $model->getSomeOrderInfo($where);
        
        if($info){
			$info['svctm']=\App\handelsvctm($info['svctm']);
        }
        
        return $info;
    }
    
    
    /* 获取订单信息 */
    public function getOrderInfo($where){
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
        
        return $info;
    }
    
    /* 接单 */
    public function receiptOrder($uid,$orderid){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $where=[
            'id'=>$orderid
        ];
        
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
        
        if(!$info){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        if($info['liveuid']!=$uid){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        $nowtime=time();
        $status=$info['status'];
        
        if($status!=1  ){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('该订单未付款，无法接单');
			return $rs;
        }
        
        /* 更新订单 */
        $data=[
            'status'=>2,
            'receipttime'=>$nowtime,
        ];
        
        $where=[
            'id'=>$info['id'],
        ];
        
        $res = $model->upOrder($where,$data);
        
        /* 发送IM*/
        $info['status']='2';
        $tips='大神通过了您的订单，快去让大神带起飞吧';
        $tips_en='The master passed your order, go and let the master carry you';
        $imdata=$this->handelInfo($info['uid'],$info);
        $imdata['tips']=$tips; 
        $imdata['tips_en']=$tips_en; 
        $this->sendImOrder($info['uid'],$imdata);
        
        return $rs;
        
    }

    /* 拒单 */
    public function refuseOrder($uid,$orderid){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $where=[
            'id'=>$orderid
        ];
        
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
        
        if(!$info){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        if($info['liveuid']!=$uid){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        $nowtime=time();
        $status=$info['status'];
        
        if($status!=1){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('订单已处理，无法操作');
			return $rs;
        }
        
        $tips='订单：很抱歉，大神没通过订单哦';
        $tips_en="Order: I'm sorry, the master didn't pass the order";
        if($status==1){
            /* 已付款 拒绝-退回 */
            $total=$info['total'];
            
            $res=\App\addCoin($info['uid'],$total);
            $record=[
                'type'=>'1',
                'action'=>'2',
                'uid'=>$info['uid'],
                'touid'=>$info['uid'],
                'actionid'=>$info['id'],
                'nums'=>$info['nums'],
                'total'=>$total,
                'addtime'=>$nowtime,
            ];
            \App\addCoinRecord($record);
			$recordv=[
				'type'=>'0',
				'action'=>'2',
				'uid'=>$info['uid'],
				'fromid'=>$info['uid'],
				'actionid'=>$info['id'],
				'nums'=>$info['nums'],
				'total'=>$total,
				'addtime'=>$nowtime,
			];
			\App\addVotesRecord($recordv);
            $tips='订单：很抱歉，大神没通过订单哦，费用'.$total.'已退回';
            $tips_en="Order: I'm sorry, great god didn't pass the order. the fee {$total} has been returned";
        }
        
        /* 更新订单 */
        $data=[
            'status'=>-3,
            'receipttime'=>$nowtime,
        ];
        
        $where=[
            'id'=>$info['id'],
        ];
        
        $res = $model->upOrder($where,$data);
        
        /* 发送IM*/
        $info['status']='-3';
        $imdata=$this->handelInfo($info['uid'],$info);
        $imdata['tips']=$tips;
        $imdata['tips_en']=$tips_en;
        $this->sendImOrder($info['uid'],$imdata);
        
        return $rs;
        
    }
    
    /* 完成订单 */
    public function completeOrder($uid,$orderid){
        file_put_contents(API_ROOT.'/runtime/orderfinsh_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 orderid=======:'.json_encode($orderid)."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/runtime/orderfinsh_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid=======:'.json_encode($uid)."\r\n",FILE_APPEND);
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $where=[
            'id'=>$orderid
        ];
        
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
        
        if(!$info){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        if($info['uid']!=$uid){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        $nowtime=time();
        $status=$info['status'];
        
        if($status !=2 ){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('对方还未接单，无法完成');
			return $rs;
        }
        

        /* 主播收益 */
        $profit=$info['profit'];
        
        $res=\App\addVotes($info['liveuid'],$profit);
        $record=[
            'type'=>'0',
            'action'=>'1',
            'uid'=>$info['liveuid'],
            'fromid'=>$uid,
            'actionid'=>$info['id'],
            'nums'=>$info['nums'],
            'total'=>$profit,
            'addtime'=>$nowtime,
        ];
        \App\addVotesRecord($record);

        
        /* 更新订单 */
        $data=[
            'status'=>-2,
            'oktime'=>$nowtime,
        ];
        
        $where=[
            'id'=>$info['id'],
        ];
        file_put_contents(API_ROOT.'/runtime/orderfinsh_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 where=============:'.json_encode($where)."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/runtime/orderfinsh_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 data==============:'.json_encode($data)."\r\n",FILE_APPEND);
		
        $res = $model->upOrder($where,$data);
        
        /* 更新接单数 */
        $where=[
            'uid'=>$info['liveuid'],
            'skillid'=>$info['skillid'],
        ];
        $Domain_Skill = new Domain_Skill();
        $skilllist= $Domain_Skill->upOrsers($where,1);
        
        /* 分销 */
        \App\setAgentProfit($uid,$info['total']);
        
        /* 发送IM*/
        $info['status']='-2';
        $tips='接单：订单已经结束了，收入'.$profit.'，您可以给用户评价哦';
        $tips_en='Receipt: the order has been completed and the income is '.$profit.'. You can give make a comment on the user';
        $imdata=$this->handelInfo($info['liveuid'],$info);
        $imdata['tips']=$tips;
        $imdata['tips_en']=$tips_en;
        $this->sendImOrder($info['liveuid'],$imdata);
        
        return $rs;
        
    }
    
    /* 更新订单 */
	public function upOrder($where,$data) {
        
        $model = new Model_Orders();
        $order= $model->upOrder($where,$data);

		return $info;
	}

    /* 获取用户间进行中的订单 */
	public function getOrdering($uid,$touid) {
        
        $where=[
            '(uid=? and liveuid=? and (`status`=2 or `status`=1 ) ) or (uid=? and liveuid=? and (`status`=2 or `status`=1 ) )  or  (uid=? and liveuid=? and `status`=-2 and iscommnet=0) or  (uid=? and liveuid=? and `status`=-2 and isevaluate=0) ' =>[$uid,$touid,$touid,$uid,$uid,$touid,$touid,$uid],
        ];
        
        $info=[
            'isexist'=>'0',
            'order'=>(object)[],
        ];
        $model = new Model_Orders();
        $order= $model->getOrderInfo($where);
        
        if($order){
            $order=$this->handelInfo($uid,$order);
            
            $info['isexist']='1';
            $info['order']=$order;
        }
        
		return $info;
	}
    
    /* 发送订单消息 */
    protected function sendImOrder($touid,$data){

        /* IM */
        /* $ext=[
            'method'=>'orders',
        ]; */
        $data['method']='orders';

        $ext=$data;

        #构造高级接口所需参数
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
            'MsgType' => 'TIMCustomElem',       //自定义类型
            'MsgContent' => array(
                'Data' => json_encode($ext),
                'Desc' => '',
                //  'Ext' => $ext,
                //  'Sound' => '',
            )
        );
        //将创建的元素$msg_content_elem, 加入array $msg_content
        array_push($msg_content, $msg_content_elem);
        
        $account_id=(string)0;
        $receiver=(string)$touid;
        $api=\App\getTxRestApi();
        $ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,2);
        
        file_put_contents(API_ROOT.'/runtime/sendImOrder'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ext:'.json_encode($ext)."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/runtime/sendImOrder'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
        /* IM */
		
		return 1;     
    }
    
    /* 发送私信消息 */
    protected function sendIm($uid,$touid,$msg){

        /* IM */
        #构造高级接口所需参数
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
            'MsgType' => 'TIMTextElem',       //文本消息
            'MsgContent' => array(
                "Text"=>$msg
            )
        );
        //将创建的元素$msg_content_elem, 加入array $msg_content
        array_push($msg_content, $msg_content_elem);
        
        $account_id=(string)$uid;
        $receiver=(string)$touid; 
        $api=\App\getTxRestApi();
        $ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,2);
        
        file_put_contents(API_ROOT.'/runtime/sendIm'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/runtime/sendIm'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 touid:'.json_encode($touid)."\r\n",FILE_APPEND);
        file_put_contents(API_ROOT.'/runtime/sendIm'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
        /* IM */
		
		return 1;     
    }
	
	/* 接单后：接单者：更新立即服务状态 */
    public function upReceptOrder($uid,$orderid){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('已向对方发起服务申请'), 'info' => array());
        
        $where=[
            'id'=>$orderid
        ];
        
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
   
        if(!$info){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        if($info['liveuid']!=$uid){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        $nowtime=time();
		
        $status=$info['status'];
        $recept_status=$info['recept_status'];
        file_put_contents(API_ROOT.'/runtime/aaa_order'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 recept_status=========:'.json_encode($recept_status)."\r\n",FILE_APPEND);
        if($status!=2  ){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('该订单未接单或已完成，无法发起服务申请');
			return $rs;
        }
		if($recept_status=='-1'  ){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('已申请立即服务');
			return $rs;
        }
        
        /* 更新订单 */
        $data=[
            'recept_status'=>'-1',
        ];
        $where=[
            'id'=>$info['id'],
        ];
        $res = $model->upOrder($where,$data);
        
        /* 发送IM*/
        $info['status']='2';
        $info['recept_status']='-1';
        $info['method']="nowserver"; 
		$info['svctm']=\App\handelsvctm($info['svctm']);
		unset($info['type']);
        unset($info['ambient']);
        unset($info['paytime']);
        unset($info['receipttime']);
        unset($info['oktime']);
        unset($info['canceltime']);
        unset($info['orderno']);
        unset($info['trade_no']);
		// file_put_contents(API_ROOT.'/runtime/zzupReceptOrder_'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 info====:'.json_encode($info)."\r\n",FILE_APPEND);
        \App\sendImCustom($uid,$info['uid'],$info,0,1);
        return $rs;
        
    }
	/* 接单后：接单者：更新服务状态 */
    public function upReceptStatus($uid,$orderid,$recept_status){
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('已拒绝'), 'info' => array());
        
        $where=[
            'id'=>$orderid
        ];
        
        $model = new Model_Orders();
		$info = $model->getOrderInfo($where);
   
        if(!$info){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
        }
        
        $nowtime=time();
        $status=$info['status'];
        
        if($status!=2  ){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('该订单未接单或已完成，无法发起服务申请');
			return $rs;
        }
        
        /* 更新订单 */
        $data=[
            'recept_status'=>$recept_status,
        ];
		if($recept_status=='2'){//同意立即服务
			$Domain_Skill = new Domain_Skill();
			$skillinfo=$Domain_Skill->getSkill($info['skillid']);
			
			$data['overtime']=$nowtime+$skillinfo['methodminutes']*60;
		}
	
        $where=[
            'id'=>$info['id'],
        ];
        $res = $model->upOrder($where,$data);
        
        /* 发送IM*/
        $info['method']="upreceptstatus"; 
        $info['action']=$recept_status; 
        $info['recept_status']=$recept_status; 
		$info['svctm']=\App\handelsvctm($info['svctm']);
		unset($info['type']);
        unset($info['ambient']);
        unset($info['paytime']);
        unset($info['receipttime']);
        unset($info['oktime']);
        unset($info['canceltime']);
        unset($info['orderno']);
        unset($info['trade_no']);
		
        \App\sendImSysCustom($uid,$info,3);
        \App\sendImSysCustom($info['liveuid'],$info,3);
		// file_put_contents(API_ROOT.'/runtime/aaupReceptStatus'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 info:'.json_encode($info)."\r\n",FILE_APPEND);
		if($recept_status=='2'){
			$rs['msg']=\PhalApi\T('已同意');
			$ext=['action'=>'2','method'=>'orderstart','uid'=>$uid,'liveuid'=>$info['liveuid'],'tip_title'=>'订单开始','tip_des'=>'订单已开始,愿本次体验愉快','tip_des2'=>'对方已同意立即服务,订单已开始,愿本次体验愉快'];
			/* file_put_contents(API_ROOT.'/runtime/aaupReceptStatus'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 recept_status:'.json_encode($recept_status)."\r\n",FILE_APPEND);
			file_put_contents(API_ROOT.'/runtime/aaupReceptStatus'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ext:'.json_encode($ext)."\r\n",FILE_APPEND); */
			\App\sendImCustom($uid,$info['liveuid'],$ext,0,1);//下单用户同意后，下单方发送订单开始IM
		}
        return $rs;
        
    }
}
