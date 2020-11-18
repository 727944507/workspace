<?php

/* 相册管理 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class PhotoController extends AdminBaseController
{

    protected function getStatus($k=''){
        $status=array("0"=>"审核中","1"=>"已通过",'2'=>'已拒绝');
        if($k==''){
            return $status;
        }
        return isset($status[$k])? $status[$k] : '' ;
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
        
        $list = Db::name('photo')
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           
           $v['thumb']=get_upload_path($v['thumb']);

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

        
        $result=DB::name("photo")->where("id={$id}")->update(['status'=>$status]);
        if($result===false){
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
        
        $result=DB::name("photo")->where("id={$id}")->delete();
        if(!$result){
            $this->error("删除失败！");
        }

        $this->success("删除成功！");
    }

}