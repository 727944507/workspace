<?php

/* 分销管理、邀请赚钱 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AgentController extends AdminBaseController
{

        
    public function index()
    {
        
        $data = $this->request->param();
        $map=[];


        if($data['uid']!=''){
            $map[]=['uid','=',intval($data['uid'])];
        }
        
        if($data['one']!=''){
            $map[]=['one','=',intval($data['one'])];
        }
        
		$start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['addtime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
        }

        
        $list = Db::name('agent')
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           
           if($v['one']){
               $oneinfo=getUserInfo($v['one']);
               $v['oneinfo']=$oneinfo;
           }

           return $v; 
        });
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function profit()
    {
        
        $data = $this->request->param();
        $map=[];

        $map[]=['one_p','<>',0];
        
        if($data['uid']!=''){
            $map[]=['uid','=',intval($data['uid'])];
        }
        
        
        $list = Db::name('agent_profit')
            ->where($map)
            ->order("uid desc")
            ->paginate(20);
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           return $v; 
        });
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);


        return $this->fetch();
    }


}