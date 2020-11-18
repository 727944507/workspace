<?php

/* 系统通知 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class SysnoticeController extends AdminBaseController
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

        if($data['keyword']!=''){
            $map[]=['content','like',"%".$data['keyword']."%"];
        }
   
        $list = Db::name('sys_notice')
            ->where($map)
            ->order("id desc")
            ->paginate(20);
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
            
        $this->assign('list', $list);

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
            

            $content=$data['content'];
            if($content==''){
                $this->error('请输入消息内容');
            }
            
            $ip=get_client_ip(0, true);
            
            $ip=ip2long($ip);
            
            $admin_id = session('ADMIN_ID');
            $admininfo = Db::name('user')->where("id='{$admin_id}'")->find();
            
            $admin=$admininfo['user_login'];
            if($admininfo['user_nickname']){
                $admin=$admininfo['user_nickname'];
            }

            $insert=[
                'content'=>$content,
                'addtime'=>time(),
                'admin'=>$admin,
                'ip'=>$ip,
            ];
            
            $id = DB::name('sys_notice')->insertGetId($insert);
            if(!$id){
                $this->error("操作失败！");
            }
            

            $this->success("操作成功！");
        }
    }
    
    public function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('sys_notice')->where("id={$id}")->delete();
        
        $this->success("删除成功！",url("Sysnotice/index"));
    }

}