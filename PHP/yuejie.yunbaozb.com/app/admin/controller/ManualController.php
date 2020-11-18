<?php

/* 手动充值 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ManualController extends AdminBaseController
{

    public function index()
    {
        $data = $this->request->param();
        $map=[];

        $start_time=$data['start_time'];
        $end_time=$data['end_time'];
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        if($data['touid']!=''){
            $map[]=['touid','=',intval($data['touid'])];
        }
       
   
        $list = Db::name('charge_admin')
			->where($map)
            ->order("id desc")
            ->paginate(20);
            
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['touid']);
           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);

		$coin = Db::name("charge_admin")
            ->where($map)
			->sum('coin');
        if(!$coin){
            $coin=0;
        }

    	$this->assign('coin', $coin);
        return $this->fetch();
    }


    public function add()
    {
        return $this->fetch();
    }

    public function addPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            
            $touid=$data['touid'];
            
            if($touid == ''){
                $this->error('请填写用户ID');
            }else{
                $check = Db::name('user')->where("id='{$touid}'")->find();
                if(!$check){
                    $this->error('该用户不存在');
                }
                if($check['user_type']==1){
                    $this->error('该用户为管理员');
                }
            }

            $coin=(int)$data['coin'];
            if($coin==0){
                $this->error('请输入有效金额');
            }
            
            $ip=get_client_ip(0, true);
            
            $ip=ip2long($ip);
            
            $admin_id = session('ADMIN_ID');
            $admininfo = Db::name('user')->where("id='{$admin_id}'")->find();
            
            $admin=$admininfo['user_login'];
            if($admininfo['user_nickname']){
                $admin=$admininfo['user_nickname'];
            }
            
            if($coin>0){
                $rs=Db::name('user')->where("id='{$touid}'")->setInc('coin',$coin);
            }else{
                $coin2=abs($coin);
                $rs=Db::name('user')->where("id='{$touid}' and coin >={$coin2}")->setDec('coin',$coin2);
            }
            
            if(!$rs){
                $this->error("充值失败！");
            }
            
            $insert=[
                'touid'=>$touid,
                'coin'=>$coin,
                'addtime'=>time(),
                'admin'=>$admin,
                'ip'=>$ip,
            ];
            $id = DB::name('charge_admin')->insertGetId($insert);
            if(!$id){
                $this->error("充值失败！");
            }

            $this->success("充值成功！");
        }
    }
	
	//导出
	
	function export(){
        $data = $this->request->param();
        $map=[];

        $start_time=$data['start_time'];
        $end_time=$data['end_time'];
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        if($data['touid']!=''){
            $map[]=['touid','=',intval($data['touid'])];
        }
        
        $xlsName  = "手动充值记录";
        $xlsData = Db::name("charge_admin")
            ->where($map)
			->order("id desc")
			->select()
            ->toArray();

        foreach ($xlsData as $k => $v)
        {
            $userinfo=getUserInfo($v['touid']);
            
            $xlsData[$k]['user_nickname']= $userinfo['user_nickname'].'('.$v['touid'].')';
            $xlsData[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']); 
        }
        
        $cellName = array('A','B','C','D','E','F');
        $xlsCell  = array(
            array('id','序号'),
            array('admin','管理员'),
            array('user_nickname','会员 (账号)(ID)'),
            array('coin','充值点数'),
            array('ip','IP'),
            array('addtime','时间'),
        );
        exportExcel($xlsName,$xlsCell,$xlsData,$cellName);
    }

}