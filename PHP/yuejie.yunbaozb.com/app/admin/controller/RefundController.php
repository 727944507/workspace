<?php

/* 退款列表 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class RefundController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=array("-2"=>"订单完成","3"=>"等待退款","4"=>"拒绝退款","5"=>"同意退款","6"=>"退款申诉");
        if($k==''){
            return $status;
        }
        return isset($status[$k])? $status[$k] : '' ;
    }
	
	
    
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
       
		$map1=" status>=3 or refundtime > 0 ";
		
        $status=isset($data['status']) ? $data['status']: '';
        
        if($status!=''){
            $map[]=['status','=',$status];
        }

        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['refundtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['refundtime','<=',strtotime($end_time) + 60*60*24];
        }

        if($data['uid']!=''){
            $map[]=['uid','=',intval($data['uid'])];
        }
        
        if($data['liveuid']!=''){
            $map[]=['liveuid','=',intval($data['liveuid'])];
        }
        
        if($data['orderid']!=''){
            $map[]=['orderid','=',intval($data['orderid'])];
        }
        
      
        $list = Db::name('orders')
            ->where($map1)
			->where($map)
            ->order("refundtime desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           $v['touserinfo']= getUserInfo($v['liveuid']);
		   $refundinfo=Db::name('refund')
					->where("orderid={$v['id']}")
					->find();
		   if(!$refundinfo){
			   $refundinfo['content']="暂无退款原因";
		   }
		   $v['refundinfo']=$refundinfo;
           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        $this->assign('status', $this->getStatus());
	

        return $this->fetch();
    }


    public function setstatus()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        $status = $this->request->param('status', 0, 'intval');
        
        $rs=DB::name("refund")->where("id={$id}")->update(['status'=>$status]);
        if($rs!==false){
            $this->error("操作失败");
        }
        
        $this->success("操作成功");        
    }
    
    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        if(!$id){
            $this->error("数据传入失败！");
        }
        
        $result=DB::name("refund")->where("id={$id}")->delete();
        if(!$result){
            $this->error("删除失败！");
        }

        $this->success("删除成功！");
    }
	//同意、拒绝退款
	public function setRefund()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        if(!$id){
            $this->error("数据传入失败！");
        }
        
        $status = $this->request->param('status');
        
        $result=DB::name("orders")->where("id={$id}")->find();	
	    
        if($result){
			$nowtime=time();
            if($result['status']==4 || $result['status']==5 ){
                $this->error("该退款信息已处理");
            }

            if($status==5){
                DB::name("user")->where("id='{$result['uid']}'")->setInc("coin",$result['total']);//加钻石                
                DB::name("user")->where("id='{$result['uid']}'")->setDec("consumption",$result['total']);//减经验  
				$record=[
					'type'=>'0',
					'action'=>'4',
					'uid'=>$result['liveuid'],
					'fromid'=>$result['uid'],
					'actionid'=>$result['id'],
					'nums'=>$result['nums'],
					'total'=>$result['total'],
					'addtime'=>$nowtime,
				];
				addVotesRecord($record);				
            }
            DB::name("orders")->where("id='{$result['id']}'")->update(array("status"=>$status));
            $this->success('操作成功');
         }else{
            $this->error('数据传入失败！');
         }	
             
    }

}