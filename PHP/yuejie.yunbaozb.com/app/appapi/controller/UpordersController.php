<?php

/* 更新订单状态 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class UpordersController extends HomebaseController{
    
    public function Uporder() {
        
        $data = $this->request->param();
        $lastpetid=isset($data['lastpetid']) ? $data['lastpetid']: '0';
        $lastpetid = (int)checkNull($lastpetid);
        if(!$lastpetid){
            $lastpetid=0;
        }
        
        
        $now=time();
        $addtime=$now - 60*15;
        //file_put_contents('./Uporders.txt',date('Y-m-d H:i:s').' 提交参数信息 lastpetid:'.$lastpetid."\r\n",FILE_APPEND);
        //file_put_contents('./Uporders.txt',date('Y-m-d H:i:s').' 提交参数信息 开始:'.$lastpetid.'--'.time()."\r\n",FILE_APPEND);
        
        $limit=1000;
        $where=[];
        $where[]=['id','>',$lastpetid];
        $where[]=['addtime','<',$addtime];

        $list=Db::name("orders")->where("(status=1 or status=0)")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();
		if($list){
			file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  list==订单超时====:'.json_encode($list)."\r\n",FILE_APPEND);
		}
        
        $nums=count($list);
        
        foreach($list as $k=>$v){
            if($v['status']==1){
                /* 已付款 退还 */
                $total=$v['total'];
            
                addCoin($v['uid'],$v['total'],1);
                $record=[
                    'type'=>'1',
                    'action'=>'2',
                    'uid'=>$v['uid'],
                    'touid'=>$v['uid'],
                    'actionid'=>$v['id'],
                    'nums'=>$v['nums'],
                    'total'=>$v['total'],
                    'addtime'=>$now,
                ];
				
                addCoinRecord($record);
				
				$recordv=[
                    'type'=>'0',
                    'action'=>'2',
                    'uid'=>$v['uid'],
                    'fromid'=>$v['uid'],
                    'actionid'=>$v['id'],
                    'nums'=>$v['nums'],
                    'total'=>$v['total'],
                    'addtime'=>$now,
                ];
				// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  list=收益记录====:'.json_encode($recordv)."\r\n",FILE_APPEND);
                addVotesRecord($recordv);
            }
          
			Db::name("orders")->where(['id'=>$v['id']])->update(['status'=>-4]);
           
            $lastpetid=$v['id'];
        }
		// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  订单超时======:'.json_encode($ret)."\r\n",FILE_APPEND);
        //file_put_contents('./Uporders.txt',date('Y-m-d H:i:s').' 提交参数信息 结束:'.$lastpetid.'--'.time()."\r\n",FILE_APPEND);
        
        if($nums<$limit){
            //file_put_contents('./Uporders.txt',date('Y-m-d H:i:s').' 提交参数信息 echo NO:'.$lastpetid.'--'."\r\n",FILE_APPEND);
            echo "NO";
            exit;   
        }
        //file_put_contents('./Uporders.txt',date('Y-m-d H:i:s').' 提交参数信息 echo lastpetid:'.$lastpetid.'--'.$this->lastpetid."\r\n",FILE_APPEND);
        echo 'OK-'.$lastpetid;
        exit;

	}
	
	/* 发送IM：距离陪玩前十分钟向对方（即下单用户）：订单即将开始通知 */
    public function orderStart() {
        
        $data = $this->request->param();
        $lastpetid=isset($data['lastpetid']) ? $data['lastpetid']: '0';
        $lastpetid = (int)checkNull($lastpetid);
        if(!$lastpetid){
            $lastpetid=0;
        }
		
		$now=$data['nowtime'];
        $addtime=$now + 10*60;

        $limit=1000;
        $where=[];
        $where[]=['id','>',$lastpetid];
		$where[]=['status','=',2];
        $where[]=['recept_status','neq',2];
		$where[]=['svctm','=',$addtime];
		
        $list=Db::name("orders")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();

        $nums=count($list);
        foreach($list as $k=>$v){
			$svtime=handelsvctm($v['svctm']);
			$ext=['action'=>'0','method'=>'orderstart','tip_title'=>'订单即将开始','tip_des'=>$svtime.'订单,还有10分钟即将开始'];
			// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  orderStart======:'.json_encode($ret)."\r\n",FILE_APPEND);
			sendImCustom($ext,$v['liveuid'],$v['uid'],0,1);
			
            $lastpetid=$v['id'];
        }

        if($nums<$limit){
            echo "NO";
            exit;   
        }
        echo 'OK-'.$lastpetid;
        exit;

	}
	
	/* 发送IM：订单开始通知 */
    public function orderStarting() {        
        $data = $this->request->param();
        $lastpetid=isset($data['lastpetid']) ? $data['lastpetid']: '0';
        $lastpetid = (int)checkNull($lastpetid);
        if(!$lastpetid){
            $lastpetid=0;
        }
		$now=time();
        $limit=1000;
        $where=[];
        $where[]=['id','>',$lastpetid];
		$where[]=['status','=',2];
		$where[]=['svctm','=',$data['nowtime']];
        $list=Db::name("orders")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();
		// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  list======:'.json_encode($list)."\r\n",FILE_APPEND);
        $nums=count($list);
        foreach($list as $k=>$v){
			$ext=['action'=>'1','method'=>'orderstart','tip_title'=>'订单开始','tip_des'=>'订单已开始,愿本次体验愉快'];
			// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  orderStarting======:'.json_encode($ret)."\r\n",FILE_APPEND);
			sendImCustom($ext,$v['liveuid'],$v['uid'],0,1);
            $lastpetid=$v['id'];
        }
        if($nums<$limit){
            echo "NO";
            exit;   
        }
        echo 'OK-'.$lastpetid;
        exit;

	}
	
	/* 发送IM：订单结束通知 */
    public function orderEnd() {        
        $data = $this->request->param();
        $lastpetid=isset($data['lastpetid']) ? $data['lastpetid']: '0';
        $lastpetid = (int)checkNull($lastpetid);
        if(!$lastpetid){
            $lastpetid=0;
        }
		$now=$data['nowtime'];
		$endtime=$now + 5*60;
		$endtimediffb=$now + 5*60+5;
		// $endtimediffl=$now + 5*60-5;
        $limit=1000;
        $where=[];
        $where[]=['id','>',$lastpetid];
		$where[]=['status','=',2];
		// $where[]=['overtime','=',$endtime];
		
		$where[]=['overtime','>=',$endtime];
		$where[]=['overtime','<=',$endtimediffb];
		
        $list=Db::name("orders")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();
		// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  list==再来一单====:'.json_encode($list)."\r\n",FILE_APPEND);
        $nums=count($list);
        foreach($list as $k=>$v){
			$ext=['action'=>'3','method'=>'orderstart','uid'=>$v['uid'],'liveuid'=>$v['liveuid'],'skillid'=>$v['skillid'],'tip_title'=>'订单即将结束','tip_des'=>'体验不错,可以再来一单'];
			// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  orderEnd======:'.json_encode($ret)."\r\n",FILE_APPEND);
			sendImCustom($ext,$v['liveuid'],$v['uid'],0,2);
            $lastpetid=$v['id'];
        }
        if($nums<$limit){
            echo "NO";
            exit;   
        }
        echo 'OK-'.$lastpetid;
        exit;

	}
	
	/* 发送IM：订单退款自动处理:默认拒绝退款 */
    public function orderRefund() {        
        $data = $this->request->param();
        $lastpetid=isset($data['lastpetid']) ? $data['lastpetid']: '0';
        $lastpetid = (int)checkNull($lastpetid);
        if(!$lastpetid){
            $lastpetid=0;
        }
		$now=$data['nowtime'];
		$endtime=$now - 15*60;
		$endtimediffb=$now - 15*60+5;
		// $endtimediffl=$now - 15*60-5;
        $limit=1000;
        $where=[];
        $where[]=['id','>',$lastpetid];
	
		// $where[]=['addtime','<=',$endtime];
		$where[]=['addtime','>=',$endtime];
		$where[]=['addtime','<=',$endtimediffb];
		
        $list=Db::name("refund")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();
		
        $nums=count($list);
        foreach($list as $k=>$v){
			//更新退款状态：拒绝退款
			$rs=Db::name("orders")->where(['id'=>$v['orderid'],"status"=>3])->update(['status'=>4]);
		    if($rs){
				$ext=['action'=>'4','method'=>'refundorder','tip_title'=>'大神拒绝退款','tip_des'=>'你可以向平台发起退款申诉'];
				// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  orderRefund======:'.json_encode($ret)."\r\n",FILE_APPEND);
				sendImCustom($ext,$v['touid'],$v['uid'],0,2);
		    }
	
            $lastpetid=$v['id'];
        }
        if($nums<$limit){
            echo "NO";
            exit;   
        }
        echo 'OK-'.$lastpetid;
        exit;

	}
	/* 发送IM：自动完成订单*/
    public function orderComplete() {        
        $data = $this->request->param();
        $lastpetid=isset($data['lastpetid']) ? $data['lastpetid']: '0';
        $lastpetid = (int)checkNull($lastpetid);
        if(!$lastpetid){
            $lastpetid=0;
        }
		$now=$data['nowtime'];
		$configpri=getConfigPri();
		$sys_oklong=$configpri['sys_oklong'];//0.1;//
		  
		if($sys_oklong>0){
			$endtime=$now - $sys_oklong*60*60;
			$endtimediffb=$now - $sys_oklong*60*60+5;
			$limit=1000;
			$where=[];
			$where[]=['id','>',$lastpetid];
			// $where[]=['status','in',[2,4,5]];//已接单
			$where[]=['status','in',[2,4]];//已接单
			$where[]=['overtime','>=',$endtime];
			$where[]=['overtime','<=',$endtimediffb];
			 
			$list=Db::name("orders")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();
			/* file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  where====自动完成订单==:'.json_encode($where)."\r\n",FILE_APPEND);
			file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  list====自动完成订单=1111=:'.json_encode($list)."\r\n",FILE_APPEND); */
			$nums=count($list);
			
			foreach($list as $k=>$v){
				// file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  v======:'.json_encode($v)."\r\n",FILE_APPEND);
				//自动完成订单
				Db::name("orders")->where(['id'=>$v['id']])->update(['status'=>-2,'overtime'=>$now]);
				//接单者增加收益
				/* 主播收益 */
				$profit=$v['profit'];
				
				$res=addVotes($v['liveuid'],$profit);
				$record=[
					'type'=>'0',
					'action'=>'1',
					'uid'=>$v['liveuid'],
					'fromid'=>$v['uid'],
					'actionid'=>$v['id'],
					'nums'=>$v['nums'],
					'total'=>$profit,
					'addtime'=>$now,
				];
				addVotesRecord($record);
				file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  ext====11111111==:'.json_encode($list)."\r\n",FILE_APPEND);
				$tips='接单：订单已经结束了，收入'.$profit.'，您可以给用户评价哦';
				$tips_en='Receipt: the order has been completed and the income is '.$profit.'. You can give make a comment on the user';
				$ext=['tips'=>$tips,'tips_en'=>$tips_en];
				file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  ext====22222====:'.json_encode($ext)."\r\n",FILE_APPEND);
				$ext['method']='orders';
				$ext['addtime']=$v['addtime'];
				$ext['auth']['coin']=0;
				$ext['auth']['switch']=0;
				$ext['comment']=(object)[];
				$ext['des']='';
				$ext['evaluate']=(object)[];
				$ext['fee']=$v['fee'];
				$ext['id']=$v['id'];
				$ext['iscommnet']=0;
				$ext['isevaluate']=0;
				$ext['ishideok']=0;
				$ext['liveuid']=$v['liveuid'];
				$ext['msgTime']=handelsvctm($v['svctm']);
				$ext['nums']=$v['nums'];
				$ext['order_type']=$v['order_type'];
				$ext['overtime']=$v['overtime'];
				$ext['profit']=$v['profit'];
				$ext['reason']=$v['reason'];
				$ext['recept_status']=$v['recept_status'];
				$ext['refundtime']=$v['refundtime'];
				$ext['skill']['coin']=0;
				$ext['skill']['id']=$v['skillid'];
				$ext['skill']['method']="";
				$ext['skill']['name']="";
				$ext['skill']['thumb']="";
				$ext['skillid']=$v['skillid'];
				$ext['svctm']=handelsvctm($v['svctm']);
				$ext['total']=$v['total'];
				$ext['uid']=$v['uid'];
				$ext['userinfo']=getUserInfo($v['uid']);
				
				file_put_contents(CMF_ROOT.'log/aaa'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  ext====33333=====:'.json_encode($ext)."\r\n",FILE_APPEND);
				sendImSysCustom($ext,$v['liveuid'],0,2);
				$lastpetid=$v['id'];
			}
        }
        if($nums<$limit){
            echo "NO";
            exit;   
        }
        echo 'OK-'.$lastpetid;
        exit;

	}

}