<?php
/**
 * 订单回调
 */

namespace app\appapi\controller;
use cmf\controller\HomeBaseController;
use think\Db;

class OrderbackController extends HomebaseController {
	
    //支付宝 回调
	public function notify_ali() {
        $configpri=getConfigPri();
		require_once(CMF_ROOT."sdk/alipay_app/alipay.config.php");
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= $configpri['aliapp_partner'];
        
		require_once(CMF_ROOT."sdk/alipay_app/lib/alipay_core.function.php");
		require_once(CMF_ROOT."sdk/alipay_app/lib/alipay_rsa.function.php");
		require_once(CMF_ROOT."sdk/alipay_app/lib/alipay_notify.class.php");

		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		$this->logpay("支付宝 ali_data:".json_encode($_POST));
		if($verify_result) {//验证成功
			//商户订单号
			$out_trade_no = $_POST['out_trade_no'];
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			//交易状态
			$trade_status = $_POST['trade_status'];
			
			//交易金额
			$total_fee = $_POST['total_fee'];
			
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
					
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                
                //判断订单是否存在

                $type=1;
                $res=$this->upOrder($type,$out_trade_no,$trade_no);
                
                if(!$res){
                    echo "fail";
                    exit;
                }
                
                echo "success";		//请不要修改或删除	
                exit;                
			}
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			echo "fail";		//请不要修改或删除			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}else {
			$this->logpay("支付宝 验证失败");		
			//验证失败
			echo "fail";
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}			
		
	}
	/* 支付宝支付 */
	
	/* 微信支付 */
    private $wxDate = null;
	public function notify_wx(){
		$configpri=getConfigPri();
		//$xmlInfo = $GLOBALS['HTTP_RAW_POST_DATA'];
		$xmlInfo=file_get_contents("php://input"); 

		//解析xml
		$arrayInfo = $this -> xmlToArray($xmlInfo);
		$this -> wxDate = $arrayInfo;
		$this -> logpay("微信 wx_data:".json_encode($arrayInfo));//log打印保存
		if($arrayInfo['return_code'] == "SUCCESS"){
			if(isset($arrayInfo['return_msg']) && $arrayInfo['return_msg'] != null){
				echo $this -> returnInfo("FAIL","签名失败");
				$this -> logpay("微信 签名失败:".$sign);//log打印保存
				exit;
			}else{
				$wxSign = $arrayInfo['sign'];
				unset($arrayInfo['sign']);
				$arrayInfo['appid']  =  $configpri['wx_appid'];
				$arrayInfo['mch_id'] =  $configpri['wx_mchid'];
				$key =  $configpri['wx_key'];
				ksort($arrayInfo);//按照字典排序参数数组
				$sign = $this -> sign($arrayInfo,$key);//生成签名
				$this -> logpay("微信 数据打印测试签名signmy:".$sign.":::微信sign:".$wxSign);//log打印保存
				if($this -> checkSign($wxSign,$sign)){
					echo $this -> returnInfo("SUCCESS","OK");
					$this -> logpay("微信 签名验证结果成功:".$sign);//log打印保存
					$this -> orderServer();//订单处理业务逻辑
					exit;
				}else{
					echo $this -> returnInfo("FAIL","签名失败");
					$this -> logpay("微信 签名验证结果失败:本地加密：".$sign.'：：：：：三方加密'.$wxSign);//log打印保存
					exit;
				}
			}
		}else{
			echo $this -> returnInfo("FAIL","签名失败");
			$this -> logpay('微信 '.$arrayInfo['return_code']);//log打印保存
			exit;
		}			
	}
	
	private function returnInfo($type,$msg){
		if($type == "SUCCESS"){
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code></xml>";
		}else{
			return $returnXml = "<xml><return_code><![CDATA[{$type}]]></return_code><return_msg><![CDATA[{$msg}]]></return_msg></xml>";
		}
	}		
	
	//签名验证
	private function checkSign($sign1,$sign2){
		return trim($sign1) == trim($sign2);
	}
	/* 订单查询加值业务处理
	 * @param orderNum 订单号	   
	 */
	private function orderServer(){
		$info = $this -> wxDate;
		$this->logpay("微信 info:".json_encode($info));

        $out_trade_no=$info['out_trade_no'];
        $trade_no=$info['transaction_id'];
        
        $type=2;
        $res=$this->upOrder($type,$out_trade_no,$trade_no);
        
        return $res;        

	}		
	/**
	* sign拼装获取
	*/
	private function sign($param,$key){
		
		$sign = "";
		foreach($param as $k => $v){
			$sign .= $k."=".$v."&";
		}
	
		$sign .= "key=".$key;
		$sign = strtoupper(md5($sign));
		return $sign;
	
	}
	/**
	* xml转为数组
	*/
	private function xmlToArray($xmlStr){
		$msg = array(); 
		$postStr = $xmlStr; 
		$msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
		return $msg;
	}
	
	/* 微信支付 */
    
    
    /* 更新订单 */
    protected function upOrder($type,$out_trade_no,$trade_no){
        
        $orderinfo=Db::name('orders')->where("orderno='{$out_trade_no}' and type={$type}")->find();
        
        if(!$orderinfo){
            $this->logpay("orderno:".$out_trade_no.' 订单信息不存在');
            return false;
            exit;
        }
        
        if($orderinfo['status']!=0){
            $this->logpay("orderno:".$out_trade_no.' 已确认支付成功');
            return true;
            exit;
            
        }
        
        /* 更新 订单状态 */
        $status=1;
        if($orderinfo['order_type']==1){//type:0:普通订单；1：滴滴订单
            $status=2;
			//更新滴滴订单状态：已接单
			Db::name('drip')->where("uid='{$orderinfo['uid']}' and liveuid='{$orderinfo['liveuid']}' and skillid='{$orderinfo['skillid']}' and status=0")->update(array("status"=>'1'));
        }
        Db::name('orders')->where("id='{$orderinfo['id']}'")->update(array("status"=>$status,"trade_no"=>$trade_no,"paytime"=>time()));
        
        $this->logpay("orderno:".$out_trade_no.' 支付成功');

        
        /* 发送IM */
        $orderinfo['status']=$status;
        $this->sendorder($orderinfo);
        
        return true;
        exit;
    }
    
	/* 打印log */
	protected function logpay($msg){
		file_put_contents(CMF_ROOT.'log/paylog/orders_pay_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}
    
    protected function sendorder($orderinfo){
        
        $imdata=$this->handelInfo($orderinfo['liveuid'],$orderinfo);
        $userinfo=getUserInfo($orderinfo['uid']);
        $tips=lang('{:n}给你下了订单',['n'=>$userinfo['user_nickname']]);
        $imdata['tips']=$tips;
        $this->sendImOrder($orderinfo['liveuid'],$imdata);
        
        if($orderinfo['order_type']!=1){
            $msg=lang('订单已收到，会尽快确认');
            $this->sendIm($orderinfo['liveuid'],$orderinfo['uid'],$msg);
        }
    }

    protected function handelInfo($uid,$info){
        
        $info['svctm']=handelsvctm($info['svctm']);
        
        /* 费用 */
        $info['fee']= $info['fee']==0? '0':'-'.$info['fee'];
        
        
        if($uid==$info['uid']){
            $userinfo=getUserInfo($info['liveuid']);
        }else{
            $userinfo=getUserInfo($info['uid']);
        }
        unset($userinfo['birthday']);
        $info['userinfo']=$userinfo;
        
        /* 技能 */
        $skill=[
            'id'=>'0',
            'name'=>lang('已移除'),
            'method'=>'',
            'thumb'=>'',
        ];

        $skilllist= getSkillList();
        foreach($skilllist as $k=>$v){
            if($v['id']==$info['skillid']){
                $skill['id']=$v['id'];
                $skill['name']=$v['name'];
                $skill['method']=$v['method'];
                $skill['thumb']=$v['thumb'];
            }
        }
        
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
        $authlist= Db::name('skill_auth')
                ->where($where)
				->find();
        if($authlist){
            $auth['switch']='1';
            $auth['coin']=$authlist['coin'];
        }
        
        $info['auth']=$auth;
        
        $info['skill']['coin']=$auth['coin'];
        
        $iscommnet='0';
        $comment=(object)[];

        $info['iscommnet']=$iscommnet;
        $info['comment']=$comment;
        /* 主播给用户评价 */
        $isevaluate='0';
        $evaluate=(object)[];
        
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
        $api=getTxRestApi();
        $ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,2);
        
        file_put_contents(CMF_ROOT.'log/sendImOrder'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ext:'.json_encode($ext)."\r\n",FILE_APPEND);
        file_put_contents(CMF_ROOT.'log/sendImOrder'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
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
        $api=getTxRestApi();
        $ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,2);
        
        file_put_contents(CMF_ROOT.'log/sendIm'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        file_put_contents(CMF_ROOT.'log/sendIm'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 touid:'.json_encode($touid)."\r\n",FILE_APPEND);
        file_put_contents(CMF_ROOT.'log/sendIm'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
        /* IM */
		
		return 1;     
    }
}


