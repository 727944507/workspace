<?php
/**
 * 支付回调
 */

namespace app\appapi\controller;
use cmf\controller\HomeBaseController;
use think\Db;

class GoogleController extends HomebaseController {

	/* Google支付 */
	
	public function notify(){
		
        $rs = array('code' => 0, 'msg' => lang('支付成功'), 'info' => array());

        $data = $this->request->param();
        
        $this->loggoogle("data: ".json_encode($data));
			
		$signed_data=htmlspecialchars_decode(checkNull($data['signed_data']));
		$google_orderid=checkNull($data['google_orderid']);
		$signature=checkNull($data['signature']);
		$orderid=checkNull($data['orderid']);
        
		$configpri=getConfigPri();
		$public_key_base64 = $configpri['google_key'];
		
        //$signed_data = '{"orderId":"GPA.3379-2257-5428-12007","packageName":"com.shopten.kr.phonelive","productId":"test_id1","purchaseTime":1535623500926,"purchaseState":0,"purchaseToken":"mbpljcfdcaeejcphdlnknlol.AO-J1Ozg9sPjSSNANANZk0-7m32-Jd7ZwPgPBUzODzfAHkdjed9TwMxVQol04bcy8OG0Q30xxTyK3CVrBkqoyVJHA_6e-zVGU8hq0thoiU3hB2JRQNuppRESOEvOOpyoxeC_xRiTfYhm"}';
        //$signature = 'usbwQB9OypVMFbOarYrb4myc/vkKZi3anfFRzZfQUOvOdJADTKlA4VL0xQ3O1viygvM/pdRoYYWgmSesDjitaQonj4V86t9h4VvmIt1leP4qp8H8RDTywHsE4xwEAPS/tm2WTK7yA9bCeDOWC1xsx0s9s6DrUCFAqWjeAO9ePj3ACFQPqB/zfsduySepQ5a3ZyU62Vj+VSvcthLqZItMkA8Qu1+cqDi4jWh9KjpmCfNXsNhZXqIAjUXf5IEH7HYP1OEbSKjVHaRoBoxNc3cLiHqdHBet6GalgVbhFSd4clFXJkTm+kvg8bMq61oPyNAQZc7SW7YWsbo798TaTNdgsw==';
        // $public_key_base64 = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvf/xREA9vAlQPGrj5qlk5YtbN3cQWbZ7wAv4Y9ezr+Dtcsm52JWHjMaAOrzDOeoKEXP88Sm2jDrQBZs+iDb3kz19CT6FbaXmzkDCkv2Bk3kuXbpzMK5yfXTvDD2Kb9ZjuMz0IZ7JqUP3GToQaVtaKxIvi5SXrRUvinaEdTrysZmDqLQ3J0NvT8MDjDV3KtV0OKKtfciH+0+zNC9eNXAWhXTZsR/nThssOg4JlGhWItMd9tFQ2+xgnVUhcuJr8TajvfSHE8c3hrxJSCGK8HI+yl6YKxBccWAGS88NuNwZvXazw44f3l6gderNy5xwrnNxVx/Ny6ED3kcHxZCIOmUXCQIDAQAB';
        

        $key =	"-----BEGIN PUBLIC KEY-----\n".
            chunk_split($public_key_base64, 64,"\n").
            '-----END PUBLIC KEY-----';   
        //using PHP to create an RSA key
        $key = openssl_get_publickey($key);
        //$signature should be in binary format, but it comes as BASE64. 
        //So, I'll convert it.
        $signature = base64_decode($signature);   
        //using PHP's native support to verify the signature
        $result = openssl_verify(
                $signed_data,
                $signature,
                $key,
                OPENSSL_ALGO_SHA1);

        $this->loggoogle("result: ".json_encode($result));

		if (1 != $result)
		{
			$rs['code']=1002;
			$rs['msg']=lang('非法提交');
			echo json_encode($rs);
            exit;
		}
        
        /* 校验数据库信息 */
        
        $signed_data_a=json_decode($signed_data,true);
        
        if($signed_data_a['purchaseState']!=0){
            $rs['code']=1007;
			$rs['msg']=lang('非法提交');
			echo json_encode($rs);
            exit;
        }
        
        $iforderinfo=Db::name('charge_user')->where("trade_no='{$signed_data_a['orderId']}' and type='4'")->find();

		if($iforderinfo){
			$rs['code']=1005;
			$rs['msg']=lang('非法提交');
			echo json_encode($rs);
            exit;
		}
        
        $chargeinfo=Db::name("charge_rules")->where("google_pid='{$signed_data_a['productId']}'")->find();
        if(!$chargeinfo){
            $rs['code']=1006;
			$rs['msg']=lang('非法提交');
			echo json_encode($rs);
            exit;
        }

        //$out_trade_no = decrypt($orderid);
        $out_trade_no = $orderid;
        
		//判断订单是否存在
		$orderinfo=Db::name('charge_user')->where("orderno='{$out_trade_no}' and coin='{$chargeinfo['coin']}'  and type='4'")->find();
        
		if(!$orderinfo){
            $this->loggoogle("orderno:".$out_trade_no.' 订单信息不存在');
            $rs['code']=1003;
			$rs['msg']=lang('非法提交');
			echo json_encode($rs);
            exit;
        }
        
        if($orderinfo['status']!=0){
            $this->loggoogle("orderno:".$out_trade_no.' 已确认支付成功');
            $rs['code']=1004;
			$rs['msg']=lang('非法提交');
			echo json_encode($rs);
            exit;
            
        }
        
        
        /* 更新会员虚拟币 */
        $coin=$orderinfo['coin']+$orderinfo['coin_give'];
        Db::name('user')->where("id='{$orderinfo['touid']}'")->setInc("coin",$coin);
        /* 更新 订单状态 */
        Db::name('charge_user')->where("id='{$orderinfo['id']}'")->update(array("status"=>1,"trade_no"=>$signed_data_a['orderId'],"ambient"=>$info['ambient']));
        
        $this->loggoogle("orderno:".$out_trade_no.' 支付成功');


        echo json_encode($rs);
        exit;

        
	}
    	
	/* 打印log */
	protected function loggoogle($msg){
		file_put_contents(CMF_ROOT.'log/paylog/loggoogle_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msg:'.$msg."\r\n",FILE_APPEND);
	}						

}


