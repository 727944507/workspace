<?php

/* 充值记录 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ChargeController extends AdminBaseController
{
    var $status=array("0"=>"未支付","1"=>"已完成");
    var $type=array("1"=>"支付宝","2"=>"微信","3"=>"苹果支付","4"=>"Google Pay");
    var $ambient=array(
            "1"=>array(
                '0'=>'App',
                '1'=>'PC',
            ),
            "2"=>array(
                '0'=>'App',
                '1'=>'公众号',
                '2'=>'PC',
            ),
            "3"=>array(
                '0'=>'沙盒',
                '1'=>'生产',
            ),
            "4"=>array(
                '0'=>'开发',
                '1'=>'生产',
            ),
        );
        
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];
        if($data['status']!=''){
            $map[]=['status','=',$data['status']];
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

        
        $list = Db::name('charge_user')
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
            
        $this->assign('list', $list);
        $this->assign('status', $this->status);
        $this->assign('type', $this->type);
        $this->assign('ambient', $this->ambient);

		$moneysum = Db::name("charge_user")
            ->where($map)
			->sum('money');
        if(!$moneysum){
            $moneysum=0;
        }

		$this->assign('moneysum', $moneysum);
		
        return $this->fetch();
    }


    public function setPay()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        if(!$id){
            $this->error("数据传入失败！");
        }
        
        $result=DB::name("charge_user")->where("id={$id}")->find();				
        if($result){
            if($result['status']==1){
                $this->error("该订单已支付成功");
            }
            /* 更新会员虚拟币 */
            $coin=$result['coin']+$result['coin_give'];
            
            DB::name("user")->where("id='{$result['touid']}'")->setInc("coin",$coin);
            /* 更新 订单状态 */
            DB::name("charge_user")->where("id='{$result['id']}'")->update(array("status"=>1));
                

            $this->success('操作成功');
         }else{
            $this->error('数据传入失败！');
         }	
             
    }
	
	function export()
    {
    
        $data = $this->request->param();
        $map=[];
        
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $status=isset($data['status']) ? $data['status']: '';
        if($status!=''){
            $map[]=['status','=',$status];
        }
        
        $keyword=isset($data['keyword']) ? $data['keyword']: '';
        if($keyword!=''){
            $map[]=['uid|orderno|trade_no','like','%'.$keyword.'%'];
        }
        
        
        $xlsName  = "充值记录";

        $xlsData=Db::name("charge_user")
            ->field('id,uid,money,coin,coin_give,orderno,type,trade_no,status,addtime')
            ->where($map)
            ->order('id desc')
			->select()
            ->toArray();
        foreach ($xlsData as $k => $v)
        {
            $userinfo=getUserInfo($v['uid']);
            $xlsData[$k]['user_nickname']= $userinfo['user_nickname']."(".$v['uid'].")";
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
            $xlsData[$k]['type']=$this->type[$v['type']];
            $xlsData[$k]['status']=$this->status[$v['status']];
        }

       
        $configpub=getConfigPub();
        $cellName = array('A','B','C','D','E','F','G','H','I','J');
        $xlsCell  = array(
            array('id','序号'),
            array('user_nickname','会员'),
            array('money','人民币金额'),
            array('coin','兑换'.$configpub['name_coin']),
            array('coin_give','赠送'.$configpub['name_coin']),
            array('orderno','商户订单号'),
            array('type','支付类型'),
            array('trade_no','第三方支付订单号'),
            array('status','订单状态'),
            array('addtime','提交时间')
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }


}