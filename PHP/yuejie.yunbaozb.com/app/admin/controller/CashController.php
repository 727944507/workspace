<?php

/* 提现记录 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class CashController extends AdminBaseController
{
    var $status=array("0"=>"未处理","1"=>"已完成","2"=>"已拒绝");
    var $type=array(
        '1'=>'支付宝',
        '2'=>'微信',
        '3'=>'银行卡',
    );
	var $votestype=array("0"=>"订单收益提现","1"=>"礼物收益提现");
        
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
        if($data['status']!=''){
            $map[]=['status','=',$data['status']];
        }

		if($data['votes_type']!=''){
            $map[]=['votes_type','=',$data['votes_type']];
        }
		
        $start_time=$data['start_time'];
        $end_time=$data['end_time'];
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        if($data['uid']!=''){
            $map[]=['uid','=',intval($data['uid'])];
        }
        
        if($data['keyword']!=''){
            $map[]=['orderno|trade_no','like',"%".$data['keyword']."%"];
        }

        
        $list = Db::name('cash_record')
            ->where($map)
            ->order("id desc")
            ->paginate(20);
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
		$cashrecord_total = DB::name("cash_record")->where($map)->sum("money");
        if($status=='')
        {
            $success=$map;
            $success[]=['status','=',1];
            $fail=$map;
            $fail[]=['status','=',2];
            $cashrecord_success = DB::name("cash_record")->where($success)->sum("money");
            $cashrecord_fail = DB::name("cash_record")->where($fail)->sum("money");
            $cash['success']=$cashrecord_success;
            $cash['fail']=$cashrecord_fail;
            $cash['type']=0;
        }
        $cash['total']=$cashrecord_total;
		$this->assign('cash', $cash);
            
        $this->assign('list', $list);
        $this->assign('status', $this->status);
		$this->assign('votestype', $this->votestype);
        $this->assign('type', $this->type);

        return $this->fetch();
    }


    public function setCash()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        if(!$id){
            $this->error("数据传入失败！");
        }
        
        $status = $this->request->param('status');
        
        $result=DB::name("cash_record")->where("id={$id}")->find();				
        if($result){
            if($result['status']!=0){
                $this->error("该订单已处理");
            }

            if($status==2){
                DB::name("user")->where("id='{$result['uid']}'")->setInc("votes",$result['votes']);                
            }
            DB::name("cash_record")->where("id='{$result['id']}'")->update(array("status"=>$status,"uptime"=>time()));

            $this->success('操作成功');
         }else{
            $this->error('数据传入失败！');
         }	
             
    }
	
	//编辑
	function edit(){
        
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('cash_record')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $data['userinfo']=getUserInfo($data['uid']);
        
        $this->assign('type', $this->type);
        $this->assign('status', $this->status);
            
        $this->assign('data', $data);
        return $this->fetch();
	}
    
    function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
			$status=$data['status'];
			$uid=$data['uid'];
			$votes=$data['votes'];
			$id=$data['id'];
			$votes_type=$data['votes_type'];//提现类型：0：订单收益提现；1：礼物收益提现

			if($status=='0'){
				$this->success("修改成功！");
			}

            
            $data['uptime']=time();
            
			$rs = DB::name('cash_record')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            if($status=='2'){
				if($votes_type=='1'){//礼物收益提现
					DB::name("user")->where(["id"=>$uid])->setInc("votes_gift",$votes); 
				}else{
					DB::name("user")->where(["id"=>$uid])->setInc("votes",$votes); 
				}
            }
          
            $this->success("修改成功！");
		}
	}
	
	
	//导出
	function export()
    {
        $data = $this->request->param();
        $map=[];
        if($data['status']!=''){
            $map[]=['status','=',$data['status']];
        }

		if($data['votes_type']!=''){
            $map[]=['votes_type','=',$data['votes_type']];
        }
		
        $start_time=$data['start_time'];
        $end_time=$data['end_time'];
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        if($data['uid']!=''){
            $map[]=['uid','=',intval($data['uid'])];
        }
        
        if($data['keyword']!=''){
            $map[]=['orderno|trade_no','like',"%".$data['keyword']."%"];
        }
		
        $xlsName  = "提现";
        
        $xlsData=DB::name("cash_record")
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($xlsData as $k => $v)
        {
            $userinfo=getUserInfo($v['uid']);
            $xlsData[$k]['user_nickname']= $userinfo['user_nickname']."(".$v['uid'].")";
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
            $xlsData[$k]['uptime']=date("Y-m-d H:i:s",$v['uptime']); 
            $xlsData[$k]['status']=$this->status[$v['status']];
        }
		
        $cellName = array('A','B','C','D','E','F','G','H');
        $xlsCell  = array(
            array('id','序号'),
            array('user_nickname','会员'),
            array('money','提现金额'),
            array('votes','兑换点数'),
            array('trade_no','第三方支付订单号'),
            array('status','状态'),
            array('addtime','提交时间'),
            array('uptime','处理时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }


}