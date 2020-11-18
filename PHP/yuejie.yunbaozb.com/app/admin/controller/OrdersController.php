<?php

/* 订单列表 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class OrdersController extends AdminBaseController
{

    protected function getPayType($k=''){
        $status=array("0"=>"余额","1"=>"支付宝","2"=>"微信","3"=>"ApplePay",'4'=>'GooglePay');
        if($k==''){
            return $status;
        }
        return $status[$k];
    }
    
    protected function getStatus($k=''){
        // $status=array("-3"=>"已拒接","-2"=>"已完成","-1"=>"已取消","0"=>"待付款","1"=>"待接单",'2'=>'已接单');
        $status=array("-4"=>"已超时","-3"=>"已拒接","-2"=>"已完成","-1"=>"已取消","0"=>"待付款","1"=>"待接单",'2'=>'已接单','3'=>'等待退款','4'=>'拒绝退款','5'=>'同意退款','6'=>'退款申诉：等待平台退款');
        if($k==''){
            return $status;
        }
        return $status[$k];
    }
    
    protected function getSkill(){
        $list = Db::name('skill')
            ->order("list_order asc")
            ->column('*','id');
        return $list;
    }
    
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
        
        if($data['liveuid']!=''){
            $map[]=['liveuid','=',intval($data['liveuid'])];
        }
        
        if($data['skillid']!=''){
            $map[]=['skillid','=',intval($data['skillid'])];
        }
        
        $this->skill=$this->getSkill();
        
        $list = Db::name('orders')
            ->where($map)
            ->order("id desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           $v['liveinfo']= getUserInfo($v['liveuid']);
           $v['svctm']= handelsvctm($v['svctm']);
           $v['overtime']= handelsvctm($v['overtime']);
           $skillinfo= $this->skill[$v['skillid']];
           if(!$skillinfo){
               $skillinfo=[
                    'name'=>'已移除'
               ];
           }
           $v['skill']= $skillinfo;

           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);
        $this->assign('status', $this->getStatus());
        $this->assign('skill', $this->getSkill());
        $this->assign('paytype', $this->getPayType());

        return $this->fetch();
    }

}