<?php
namespace App\Domain;

use App\Model\Refund as Model_Refund;
use App\Model\Orders as Model_Orders;
use App\Domain\Orders as Domain_Orders;

class Refund {
	
   /* 申请退款 */
	public function setRefund($uid,$touid,$orderid,$content) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('已发出'), 'info' => array());
       
		$userinfo=\App\getUserInfo($uid);
        if(!$userinfo){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('用户不存在');
            return $rs;
        }
		$where=[
			"id"=>$orderid,
			"uid"=>$uid,
			"liveuid"=>$touid
		]; 
		
		$modelorder = new Model_Orders();
		$orderinfo = $modelorder->getOrderInfo($where);

        if(!$orderinfo){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('订单不存在');
			return $rs;
        }
        $status=$orderinfo['status'];
        
        if($status!=2){
            $rs['code']=1004;
			$rs['msg']=\PhalApi\T('该订单未接单或已完成，无法发起退款申请');
			return $rs;
        }
		
		
        $nowtime=time();
        $data=[
            'uid'=>$uid,
            'touid'=>$touid,
            'content'=>$content,
            'orderid'=>$orderid,
            'total'=>$orderinfo['total'],
            'addtime'=>$nowtime,
        ]; 
		
        $model = new Model_Refund();

        $res= $model->setRefund($data);
        if(!$res){
            $rs['code'] = 1005;
            $rs['msg'] = \PhalApi\T('申请退款失败，请重试');
            return $rs;
        }
		//更新订单状态
		$data1=[
            'status'=>3,//3：等待退款；4:拒绝退款；5：同意退款
            'refundtime'=>$nowtime,//退款时间
        ];
        
        $where1=[
            'id'=>$orderid
        ];
        
        $res = $modelorder->upOrder($where1,$data1);
		//发送IM：action：3：等待处理；4：拒绝退款；5：同意退款
		$ext=['action'=>'3','method'=>'refundorder','liveuid'=>$touid,'tip_title'=>'申请退款','tip_des'=>'你已申请退款,等待大神处理','tip_des2'=>'对方已申请退款,15分钟内未做操作,系统将自动进行处理,请及时处理'];
		
		\App\sendImCustom($uid,$touid,$ext,0,1);
		
		
        return $rs;
    }
	
	/* 退款信息 */
    public function getRefundinfo($uid,$orderid){
        $rs = array('code' => 0, 'msg' => "", 'info' => array());
        $where=[
            'orderid=?'=>$orderid,
            'touid=? '=>[$uid]
        ];
        
        $model = new Model_Refund();
		$info = $model->getRefundinfo($where);
         
        if($info){
			$addtime=$info['addtime'];
			$nowtime=time();
			$endtime=$addtime+15*60;
			if($nowtime>$endtime){
				$rs['code']=1002;
				$rs['msg']=\PhalApi\T('退款申诉已超时,已默认拒绝退款');
				return $rs;
			}
			$difftime=ceil(($endtime-$nowtime)/60);
			$info['difftime']=$difftime;//剩余时间
			$info['difftime_str']="还剩".$difftime."分钟,若超时未处理系统将自行处理";//剩余时间
			$domainorder = new Domain_Orders();
			$where1=[
				'id'=>$orderid,
				'status'=>'3'
			];
			$orderinfo = $domainorder->getOrderInfo($where1);
			
			if(!$orderinfo){
				$rs['code']=1003;
				$rs['msg']=\PhalApi\T('订单已处理');
				return $rs;
			}
			if($orderinfo){
				$orderinfo=$domainorder->handelInfo($uid,$orderinfo);
				
				$info['order_status']=$orderinfo['status'];
				$info['skill']=$orderinfo['skill'];
				
			}
			
			
			
        }else{
			$rs['code']=1004;
			$rs['msg']=\PhalApi\T('信息错误');
			return $rs;
		}
        $rs['info'][0]=$info;
        return $rs;
    }
    /* 退款理由列表 */
	public function getRefundcat() {
        
        $model = new Model_Refund();

        $list= $model->getRefundcat();
        
        return $list;
    }
	
	
	/* 更新退款状态 */
    public function setRefundStatus($uid,$orderid,$status){
        
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
        
        /* 更新订单 */
        $data=[
            'status'=>$status,
        ];
       
        $res = $model->upOrder($where,$data);
		// file_put_contents(API_ROOT.'/runtime/aarefund'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 status===:'.json_encode($status)."\r\n",FILE_APPEND);
        if($status!='6'){
			$total=$info['total'];
			/* 发送IM：action：3：等待处理；4：拒绝退款；5：同意退款；6：退款申诉：等待平台退款*/
			$ext=['action'=>$status,'method'=>'refundorder','liveuid'=>$uid,'tip_title'=>'大神拒绝退款','tip_des'=>'你可以向平台发起退款申诉','tip_des2'=>''];
			$type=2;
			if($status=='5'){
				//将金额退给下单用户：
				$res1=\App\addCoin($info['uid'],$total);
				$record=[
					'type'=>'1',
					'action'=>'4',
					'uid'=>$info['uid'],
					'touid'=>$info['liveuid'],
					'actionid'=>$info['id'],
					'nums'=>$info['nums'],
					'total'=>$total,
					'addtime'=>time(),
				];
				\App\addCoinRecord($record);
				
				 $recordv=[
                    'type'=>'0',
                    'action'=>'4',
                    'uid'=>$info['uid'],
                    'fromid'=>$info['liveuid'],
                    'actionid'=>$info['id'],
                    'nums'=>$info['nums'],
                    'total'=>$total,
                    'addtime'=>time(),
                ];
                \App\addVotesRecord($recordv);
				
				$type=1;
				$ext['tip_title']="退款成功";
				$ext['tip_des']="退款金额：".$total."币,已原路返回";
				$ext['tip_des2']="您已同意对方的退款申请,退款金额：".$total."币,已原路返回";
			}
			// file_put_contents(API_ROOT.'/runtime/aarefund'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ext===:'.json_encode($ext)."\r\n",FILE_APPEND);
			\App\sendImCustom($uid,$info['uid'],$ext,0,$type);
		}
        return $rs;
        
    }
	
	
	
}
