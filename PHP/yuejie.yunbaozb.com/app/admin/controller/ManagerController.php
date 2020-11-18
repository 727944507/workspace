<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\admin\model\UserModel;
use app\admin\model\UserAuthModel;
use think\Db;
use think\db\Query;

/**
 * Class AdminIndexController
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class ManagerController extends AdminBaseController
{
	
	protected function getStatus($k=''){
	    $status=array("0"=>"审核中","1"=>"已通过",'2'=>'已拒绝');
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
	
	protected function getLevel(){
	    
	    $list1=$this->getSkill();
	    
	    $list2 = Db::name('skill_level')
	        ->order("levelid asc")
	        ->column('*','id');
	        
	    foreach($list1 as $k=>$v){
	        $list=[];
	        foreach($list2 as $k2=>$v2){
	            
	            if($v['id']==$v2['skillid']){
	                $list[$v2['levelid']]=$v2;
	            }
	        }
	        $list1[$k]=$list;
	    }
	    return $list1;
	}

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
	
	public function realeName()
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
	        $map[]=['name|mobile','like',"%".$data['keyword']."%"];
	    }
	
	    
	    $list = Db::name('user_auth')
	        ->where($map)
	        ->order("addtime desc")
	        ->paginate(20);
	    
	    $list->each(function($v,$k){
	        $v['userinfo']= getUserInfo($v['uid']);
	       
	        $v['front_view']=get_upload_path($v['front_view']);
	        $v['back_view']=get_upload_path($v['back_view']);
	        $v['handset_view']=get_upload_path($v['handset_view']);
	        $v['mobile']=m_s($v['mobile']);
	        $v['cer_no']=m_s($v['cer_no']);
	
	       return $v; 
	    });
	    
	    $list->appends($data);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	        
	    $this->assign('list', $list);
	    $this->assign('status', $this->getStatus());
	
	    return $this->fetch();
	}
	
	
	public function realeNamesetstatus()
	{
	    $uid = $this->request->param('uid', 0, 'intval');
	    if(!$uid){
	        $this->error("数据传入失败！");
	    }
	    $status = $this->request->param('status', 0, 'intval');
	    $reason = $this->request->param('reason');
	    
	    $result=DB::name("user_auth")->where("uid={$uid}")->find();
	    if(!$result){
	        $this->error("数据传入失败！");
	    }
	    
	    if($result['status']!=0 && $result['status'] == $status){
	        $this->error("操作失败");
	    }
	    
	    $nowtime=time();
	    
	    $rs=DB::name("user_auth")->where("uid={$uid}")->update(['status'=>$status,'reason'=>$reason,'uptime'=>$nowtime]);
	    if(!$rs){
	        $this->error("操作失败");
	    }
		if($status==1){
			DB::name("user")->where("id={$uid}")->update(['isauth'=>"1"]);
		}else{
			DB::name("user")->where("id={$uid}")->update(['isauth'=>"0"]);
		}
		
	    
	    $this->success("操作成功");        
	}
	
	
	
	public function reale_pass(){
		$data = $this->request->param();
		$id   = $this->request->param('id', 0, 'intval');
		
		if($id<1){
			$this->error('数据有误!');
		}
		$userInfo = Db::name('user')->where('id',intval($id))->find();
		if(!$userInfo){
			$this->error('数据错误!');
		}
		if($userInfo['isauth']==0){
			$re = Db::name('user')->where('id',intval($id))->update(['isauth'=>1]);
		}else{
			$re = Db::name('user')->where('id',intval($id))->update(['isauth'=>0]);
		}
		
		$this->success('修改成功',url('manager/realeName'));
	}
	
	public function skill(){
		
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
		
		if($data['skillid']!=''){
		    $map[]=['skillid','=',intval($data['skillid'])];
		}
		
		
		$list = Db::name('skill_auth')
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
		$this->assign('skill', $this->getSkill());
		$this->assign('level', $this->getLevel());
		
		return $this->fetch();
	}
	
	public function avatar()
	{
		$data = $this->request->param();
		$map=[];
		$start_time=isset($data['start_time']) ? $data['start_time']: '';
		$end_time=isset($data['end_time']) ? $data['end_time']: '';
		
		if($start_time!=""){
		   $map[]=['a.create_time','>=',strtotime($start_time)];
		}
		
		if($end_time!=""){
		   $map[]=['a.create_time','<=',strtotime($end_time) + 60*60*24];
		}
		
		$content = hook_one('user_admin_index_view');
		
		if (!empty($content)) {
		    return $content;
		}
		
		$list = Db::name('user')
			->alias("a")
			->join('user_auth i', 'a.id = i.uid')
			->where($map)
		    ->where('a.user_type=2')
		    ->where(function (Query $query) {
		        $data = $this->request->param();
		        if (!empty($data['uid'])) {
		            $query->where('a.id', intval($data['uid']));
		        }
		
		        if (!empty($data['keyword'])) {
		            $keyword = $data['keyword'];
		            $query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
		        }
				if (!empty($data['source'])) {
		            $query->where('a.source', $data['source']);
		        }
		
		    })
		    ->order("a.create_time DESC")
		    ->paginate(10);
		$nums=Db::name("user")
				->alias("a")
				->join('user_auth i', 'a.id = i.uid')
				->where($map)
				->where('a.user_type=2')
				->where(function (Query $query) {
					$data = $this->request->param();
					if (!empty($data['uid'])) {
						$query->where('a.id', intval($data['uid']));
					}
		
					if (!empty($data['keyword'])) {
						$keyword = $data['keyword'];
						$query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
					}
					if (!empty($data['source'])) {
						$query->where('a.source', $data['source']);
					}
		
				})
				->count();
		//在线总人数
		$online=Db::name("user")
				->alias("a")
				->join('user_auth i', 'a.id = i.uid')
				->where($map)
				->where('a.user_type=2')
				->where('a.online!=0')
				->where(function (Query $query) {
					$data = $this->request->param();
					if (!empty($data['uid'])) {
						$query->where('a.id', intval($data['uid']));
					}
		
					if (!empty($data['keyword'])) {
						$keyword = $data['keyword'];
						$query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
					}
					if (!empty($data['source'])) {
						$query->where('a.source', $data['source']);
					}
		
				})
				->count();		
		// 获取分页显示
		$list->each(function($v,$k){
		    $v['user_login']=m_s($v['user_login']);
		    $v['user_email']=m_s($v['user_email']);
		    $v['mobile']=m_s($v['mobile']);
		    return $v;
		});
		$page = $list->render();
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('nums', $nums);
		$this->assign('online', $online);
		// 渲染模板输出
		return $this->fetch();
	}
	
	public function avatarPass(){
		$data = $this->request->param();
		$id   = $this->request->param('id', 0, 'intval');
		
		if($id<1){
			$this->error('数据有误!');
		}
		$userInfo = Db::name('user')->where('id',intval($id))->find();
		if(!$userInfo){
			$this->error('数据错误!');
		}
		if($userInfo['avatar_isauth']==0){
			$re = Db::name('user')->where('id',intval($id))->update(['avatar_isauth'=>1]);
		}else{
			$re = Db::name('user')->where('id',intval($id))->update(['avatar_isauth'=>0]);
		}
		
		$this->success('修改成功',url('manager/avatar'));
	}
	
	public function album()
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
	
	public function live()
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
	
	    
	    $list = Db::name('live_apply')
	        ->where($map)
	        ->order("addtime desc")
	        ->paginate(20);
	    
	    $list->each(function($v,$k){
	        $v['userinfo']= getUserInfo($v['uid']);
	       
	        $v['voice']=get_upload_path($v['voice']);
	
	       return $v; 
	    });
	    
	    $list->appends($data);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	        
	    $this->assign('list', $list);
	    $this->assign('status', $this->getStatus());
	
	    return $this->fetch();
	}
	
	
	public function setLiveStatus()
	{
	    $uid = $this->request->param('uid', 0, 'intval');
	    if(!$uid){
	        $this->error("数据传入失败！");
	    }
	    $status = $this->request->param('status', 0, 'intval');
	    $reason = $this->request->param('reason');
	    
	    $result=DB::name("live_apply")->where("uid={$uid}")->find();
	    if(!$result){
	        $this->error("数据传入失败！");
	    }
	    
	    if($result['status']!=0 && $result['status'] == $status){
	        $this->error("操作失败");
	    }
	    
	    $nowtime=time();
	    
	    $rs=DB::name("live_apply")->where("uid={$uid}")->update(['status'=>$status,'reason'=>$reason,'uptime'=>$nowtime]);
	    if(!$rs){
	        $this->error("操作失败");
	    }
	    
	    $this->success("操作成功");        
	}
	
	public function video()
	{
		$data = $this->request->param();
		$map=[];
		$start_time=isset($data['start_time']) ? $data['start_time']: '';
		$end_time=isset($data['end_time']) ? $data['end_time']: '';
		
		if($start_time!=""){
		   $map[]=['a.create_time','>=',strtotime($start_time)];
		}
		
		if($end_time!=""){
		   $map[]=['a.create_time','<=',strtotime($end_time) + 60*60*24];
		}
		
		$content = hook_one('user_admin_index_view');
		
		if (!empty($content)) {
		    return $content;
		}
		
		$list = Db::name('user')
			->alias("a")
			->join('user_auth i', 'a.id = i.uid')
			->where($map)
		    ->where('a.user_type=2')
		    ->where(function (Query $query) {
		        $data = $this->request->param();
		        if (!empty($data['uid'])) {
		            $query->where('a.id', intval($data['uid']));
		        }
		
		        if (!empty($data['keyword'])) {
		            $keyword = $data['keyword'];
		            $query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
		        }
				if (!empty($data['source'])) {
		            $query->where('a.source', $data['source']);
		        }
		
		    })
		    ->order("a.create_time DESC")
		    ->paginate(10);
		$nums=Db::name("user")
				->alias("a")
				->join('user_auth i', 'a.id = i.uid')
				->where($map)
				->where('a.user_type=2')
				->where(function (Query $query) {
					$data = $this->request->param();
					if (!empty($data['uid'])) {
						$query->where('a.id', intval($data['uid']));
					}
		
					if (!empty($data['keyword'])) {
						$keyword = $data['keyword'];
						$query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
					}
					if (!empty($data['source'])) {
						$query->where('a.source', $data['source']);
					}
		
				})
				->count();
		//在线总人数
		$online=Db::name("user")
				->alias("a")
				->join('user_auth i', 'a.id = i.uid')
				->where($map)
				->where('a.user_type=2')
				->where('a.online!=0')
				->where(function (Query $query) {
					$data = $this->request->param();
					if (!empty($data['uid'])) {
						$query->where('a.id', intval($data['uid']));
					}
		
					if (!empty($data['keyword'])) {
						$keyword = $data['keyword'];
						$query->where('a.user_login|a.user_nickname|a.user_email|a.mobile', 'like', "%$keyword%");
					}
					if (!empty($data['source'])) {
						$query->where('a.source', $data['source']);
					}
		
				})
				->count();		
		// 获取分页显示
		$list->each(function($v,$k){
		    $v['user_login']=m_s($v['user_login']);
		    $v['user_email']=m_s($v['user_email']);
		    $v['mobile']=m_s($v['mobile']);
		    return $v;
		});
		$page = $list->render();
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('nums', $nums);
		$this->assign('online', $online);
		// 渲染模板输出
		return $this->fetch();
	}
	
	public function videoPass(){
		$data = $this->request->param();
		$id   = $this->request->param('id', 0, 'intval');
		
		if($id<1){
			$this->error('数据有误!');
		}
		$userInfo = Db::name('user')->where('id',intval($id))->find();
		if(!$userInfo){
			$this->error('数据错误!');
		}
		if($userInfo['video_isauth']==0){
			$re = Db::name('user')->where('id',intval($id))->update(['video_isauth'=>1]);
		}else{
			$re = Db::name('user')->where('id',intval($id))->update(['video_isauth'=>0]);
		}
		
		$this->success('修改成功',url('manager/video'));
	}
	
	public function complainttype()
	{
	    
	    $list = Db::name('user_reportcat')
	        ->order("list_order asc")
	        ->paginate(20);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	        
	    $this->assign('list', $list);
	
	    return $this->fetch();
	}
	
	
	public function complainttypeadd()
	{
	    return $this->fetch();
	}
	
	public function complainttypeaddPost()
	{
	    if ($this->request->isPost()) {
	        $data      = $this->request->param();
	        
	        $name=$data['name'];
	        
	        if($name == ''){
	            $this->error('请填写名称');
	        }
	        
	        $map[]=['name','=',$name];
	        $isexist = DB::name('user_reportcat')->where($map)->find();
	        if($isexist){
	            $this->error('同名已存在');
	        }
	
	        $id = DB::name('user_reportcat')->insertGetId($data);
	        if(!$id){
	            $this->error("添加失败！");
	        }
	        $this->complainttyperesetcache();
	        $this->success("添加成功！");
	    }
	}
	
	public function complainttypeedit()
	{
	    $id   = $this->request->param('id', 0, 'intval');
	    
	    $data=Db::name('user_reportcat')
	        ->where("id={$id}")
	        ->find();
	    if(!$data){
	        $this->error("信息错误");
	    }
	    
	    $this->assign('data', $data);
	    return $this->fetch();
	}
	
	public function complainttypeeditPost()
	{
	    if ($this->request->isPost()) {
	        $data      = $this->request->param();
	
	        $id=$data['id'];
	        $name=$data['name'];
	        
	        if($name == ''){
	            $this->error('请填写名称');
	        }
	        
	        $map[]=['name','=',$name];
	        $map[]=['id','<>',$id];
	        $isexist = DB::name('user_reportcat')->where($map)->find();
	        if($isexist){
	            $this->error('同名已存在');
	        }
	
	        $rs = DB::name('user_reportcat')->update($data);
	
	        if($rs === false){
	            $this->error("保存失败！");
	        }
	        $this->complainttyperesetcache();
	        $this->success("保存成功！");
	    }
	}
	
	public function complainttypelistOrder()
	{
	    $model = DB::name('user_reportcat');
	    parent::listOrders($model);
	    $this->complainttyperesetcache();
	    $this->success("排序更新成功！");
	}
	
	public function complainttypedel()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    
	    $rs = DB::name('user_reportcat')->where('id',$id)->delete();
	    if(!$rs){
	        $this->error("删除失败！");
	    }
	    $this->complainttyperesetcache();
	    $this->success("删除成功！");
	}
	
	
	protected function complainttyperesetcache(){
	    /* $key='getdynamicreportcat';
	
	    $level=DB::name('user_reportcat')
	            ->order("list_order asc")
	            ->select();
	    if($level){
	        setcaches($key,$level);
	    } */
	}
	
	
	
	
	protected function getBanlong($k=''){
	    $banlong=array("0"=>"0.5","1"=>"3",'2'=>'6','3'=>'12');
	    if($k==''){
	        return $banlong;
	    }
	    return $banlong[$k];
	}
	
	public function complaintlist()
	{
	    
	    $data = $this->request->param();
	    $map=[];
	    
	    $status=isset($data['status']) ? $data['status']: '';
	    
	    if($status!=''){
	        $map[]=['status','=',$status];
	    }
	
	    $start_time=isset($data['start_time']) ? $data['start_time']: '';
	    $end_time=isset($data['end_time']) ? $data['end_time']: '';
	    
	    if($start_time!=""){
	       $map[]=['addtime','>=',strtotime($start_time)];
	    }
	
	    if($end_time!=""){
	       $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
	    }
	
	    if($data['uid']!=''){
	        $map[]=['uid','=',intval($data['uid'])];
	    }
	    
	    if($data['touid']!=''){
	        $map[]=['touid','=',intval($data['touid'])];
	    }
	    
	    if($data['did']!=''){
	        $map[]=['did','=',intval($data['did'])];
	    }
	    
	    
	    $list = Db::name('user_report')
	        ->where($map)
	        ->order("id desc")
	        ->paginate(20);
	    
	    $list->each(function($v,$k){
	       $v['userinfo']= getUserInfo($v['uid']);
	       $v['touserinfo']= getUserInfo($v['touid']);
	       
	       return $v; 
	    });
	    
	    $list->appends($data);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	        
	    $this->assign('list', $list);
	    $this->assign('status', $this->getStatus());
		$this->assign('banlong', $this->getBanlong());
	
	    return $this->fetch();
	}
	
	
	public function complaintlistsetstatus()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    if(!$id){
	        $this->error("数据传入失败！");
	    }
	    $status = $this->request->param('status', 0, 'intval');
	    
	    $rs=DB::name("user_report")->where("id={$id}")->update(['status'=>$status]);
	    if($rs!==false){
	        $this->error("操作失败");
	    }
	    
	    $this->success("操作成功");        
	}
	
	public function complaintlistdel()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    if(!$id){
	        $this->error("数据传入失败！");
	    }
	    
	    $result=DB::name("user_report")->where("id={$id}")->delete();
	    if(!$result){
	        $this->error("删除失败！");
	    }
	
	    $this->success("删除成功！");
	}
	
	//禁止接单列表
	public function complaintlistsetbanorder(){
		
		$data = $this->request->param();
		$uid=$data['touid'];
		$_selectBanval=$data['_selectBanval'];
		
		$map['uid']=$uid;
		
		
		$baninfo = Db::name('user_banorder')
	        ->where($map)
	        ->find();
		
		$this->assign("baninfo",$baninfo);
		$this->assign('banlong', $this->getBanlong());
		$this->assign('_selectBanval', $_selectBanval);
		return $this->fetch();
	
	}
	//禁止时长设置
	public function complaintlistsetBan(){
		$res=array("code"=>0,"msg"=>"设置成功","info"=>array());
		$data = $this->request->param();
		$uid=$data['touid'];
		$reportid=$data['reportid'];
		$selectBanval=$data['selectBanval'];
		
		$nowtime=time();
		
		$endtime=$nowtime+$selectBanval*60*60;
		$map['uid']=$uid;
		$map['type']="0";
		
		$isexist = Db::name('user_banorder')
	        ->where($map)
	        ->find();
		if($isexist){
			$result = Db::name('user_banorder')
				->where($map)
				->update(["starttime"=>$nowtime,'endtime'=>$endtime,"banlong"=>$selectBanval]);
		}else{
			$result=Db::name("user_banorder")->insert(["uid"=>$uid,"banlong"=>$selectBanval,"starttime"=>$nowtime,'endtime'=>$endtime]);
		}
		//更新举报信息状态
		DB::name("user_report")->where("id={$reportid}")->update(['status'=>"1"]);
		
		if($result===false){
			$res['code']=1001;
			$res['msg']="设置失败";
			echo json_encode($res);
		}
	
		echo json_encode($res);
		exit;
	
	}
	
	
	
	
	public function dynamictype()
	{
	    
	    $list = Db::name('dynamic_reportcat')
	        ->order("list_order asc")
	        ->paginate(20);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	        
	    $this->assign('list', $list);
	
	    return $this->fetch();
	}
	
	
	public function dynamictypeadd()
	{
	    return $this->fetch();
	}
	
	public function resetcacheaddPost()
	{
	    if ($this->request->isPost()) {
	        $data      = $this->request->param();
	        
	        $name=$data['name'];
	        
	        if($name == ''){
	            $this->error('请填写名称');
	        }
	        
	        $map[]=['name','=',$name];
	        $isexist = DB::name('dynamic_reportcat')->where($map)->find();
	        if($isexist){
	            $this->error('同名已存在');
	        }
	
	        $id = DB::name('dynamic_reportcat')->insertGetId($data);
	        if(!$id){
	            $this->error("添加失败！");
	        }
	        $this->resetcacheresetcache();
	        $this->success("添加成功！");
	    }
	}
	
	public function dynamictypeedit()
	{
	    $id   = $this->request->param('id', 0, 'intval');
	    
	    $data=Db::name('dynamic_reportcat')
	        ->where("id={$id}")
	        ->find();
	    if(!$data){
	        $this->error("信息错误");
	    }
	    
	    $this->assign('data', $data);
	    return $this->fetch();
	}
	
	public function dynamictypeeditPost()
	{
	    if ($this->request->isPost()) {
	        $data      = $this->request->param();
	
	        $id=$data['id'];
	        $name=$data['name'];
	        
	        if($name == ''){
	            $this->error('请填写名称');
	        }
	        
	        $map[]=['name','=',$name];
	        $map[]=['id','<>',$id];
	        $isexist = DB::name('dynamic_reportcat')->where($map)->find();
	        if($isexist){
	            $this->error('同名已存在');
	        }
	
	        $rs = DB::name('dynamic_reportcat')->update($data);
	
	        if($rs === false){
	            $this->error("保存失败！");
	        }
	        $this->dynamictyperesetcache();
	        $this->success("保存成功！");
	    }
	}
	
	public function dynamictypelistOrder()
	{
	    $model = DB::name('dynamic_reportcat');
	    parent::listOrders($model);
	    $this->dynamictyperesetcache();
	    $this->success("排序更新成功！");
	}
	
	public function dynamictypedel()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    
	    $rs = DB::name('dynamic_reportcat')->where('id',$id)->delete();
	    if(!$rs){
	        $this->error("删除失败！");
	    }
	    $this->dynamictyperesetcache();
	    $this->success("删除成功！");
	}
	
	
	protected function dynamictyperesetcache(){
	    /* $key='getdynamicreportcat';
	
	    $level=DB::name('dynamic_reportcat')
	            ->order("list_order asc")
	            ->select();
	    if($level){
	        setcaches($key,$level);
	    } */
	}
	
	
	
	
	public function dynamiclist()
	{
	    
	    $data = $this->request->param();
	    $map=[];
	    
	    $status=isset($data['status']) ? $data['status']: '';
	    
	    if($status!=''){
	        $map[]=['status','=',$status];
	    }
	
	    $start_time=isset($data['start_time']) ? $data['start_time']: '';
	    $end_time=isset($data['end_time']) ? $data['end_time']: '';
	    
	    if($start_time!=""){
	       $map[]=['addtime','>=',strtotime($start_time)];
	    }
	
	    if($end_time!=""){
	       $map[]=['addtime','<=',strtotime($end_time) + 60*60*24];
	    }
	
	    if($data['uid']!=''){
	        $map[]=['uid','=',intval($data['uid'])];
	    }
	    
	    if($data['touid']!=''){
	        $map[]=['touid','=',intval($data['touid'])];
	    }
	    
	    if($data['did']!=''){
	        $map[]=['did','=',intval($data['did'])];
	    }
	    
	    
	    $list = Db::name('dynamic_report')
	        ->where($map)
	        ->order("id desc")
	        ->paginate(20);
	    
	    $list->each(function($v,$k){
	       $v['userinfo']= getUserInfo($v['uid']);
	       $v['touserinfo']= getUserInfo($v['touid']);
	       
	       return $v; 
	    });
	    
	    $list->appends($data);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	        
	    $this->assign('list', $list);
	    $this->assign('status', $this->getStatus());
	
	    return $this->fetch();
	}
	
	
	public function dynamiclistsetstatus()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    if(!$id){
	        $this->error("数据传入失败！");
	    }
	    $status = $this->request->param('status', 0, 'intval');
	    
	    $rs=DB::name("dynamic_report")->where("id={$id}")->update(['status'=>$status]);
	    if($rs===false){
	        $this->error("操作失败");
	    }
	    
	    $this->success("操作成功");        
	}
	
	public function dynamiclistdel()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    if(!$id){
	        $this->error("数据传入失败！");
	    }
	    
	    $result=DB::name("dynamic_report")->where("id={$id}")->delete();
	    if(!$result){
	        $this->error("删除失败！");
	    }
	
	    $this->success("删除成功！");
	}
	
	
	protected function getTypes($k=''){
	    $type=[
	        "0"=>"纯文字",
	        "1"=>"图片",
	        '2'=>'视频',
	        '3'=>'语音',
	    ];
	    if($k==''){
	        return $type;
	    }
	    return isset($type[$k])? $type[$k] : '' ;
	}
	
	public function dynmanagerylist()
	{
	    
	    $data = $this->request->param();
	    $map=[];
	    $status=isset($data['status'])? $data['status']:'';
	    if($status!=''){
	        $map[]=['status','=',$status];
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
	    
	    
	    $list = Db::name('dynamic')
	        ->where($map)
	        ->order("addtime desc")
	        ->paginate(20);
	    
	    $list->each(function($v,$k){
	       $v['userinfo']= getUserInfo($v['uid']);
	       $thumbs=preg_split('/,|，/',$v['thumbs']);
	       $thumb=[];
	       foreach($thumbs as $k1=>$v1){
	           $thumb[]=get_upload_path($v1);
	       }
	       $v['thumb']=$thumb;
	       $v['video_t']=get_upload_path($v['video_t']);
	       $v['video']=get_upload_path($v['video']);
	       $v['voice']=get_upload_path($v['voice']);
	
	       return $v; 
	    });
	    
	    $list->appends($data);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	        
	    $this->assign('list', $list);
	    $this->assign('status2', $status);
	    $this->assign('status', $this->getStatus());
	    $this->assign('type', $this->getTypes());
	
	    return $this->fetch('dynmanagerylist');
	}
	
	public function dynmanagernlist(){
	    return $this->dynmanagerylist();
	}
	public function dynmanagerflist(){
	    return $this->dynmanagerylist();
	}
	
	public function dynmanagerylistedit(){
		$id = intval($this->request->param('id'));
		if($id<=0){
			$this->error("数据有误");
		}
		
		$dynamic = Db::name('dynamic')->where('id',$id)->find();
		$this->assign('data', $dynamic);
		return $this->fetch('dynmanagerylistedit');
	}
	
	public function dynmanagerylisteditPost()
	{
		$data = $this->request->param('post');
		$id = intval($this->request->param('id'));
		
		if($id<=0){
			$this->error("数据有误");
		}
		
		$dynamicResult = Db::name('dynamic')->where('id',$id)->strict(false)->update($data);
		
		if(!$dynamicResult){
			$this->error("数据有误");
		}
		$this->success("保存成功！");
	}
	
	
	public function dynmanagerylistsee()
	{
	    $id   = $this->request->param('id', 0, 'intval');
	    
	    $data=Db::name('dynamic')
	        ->where("id={$id}")
	        ->find();
	    if(!$data){
	        $this->error("信息错误");
	    }
	    
	    $data['video']=get_upload_path($data['video']);
	    $data['voice']=get_upload_path($data['voice']);
	       
	    $this->assign('data', $data);
	    return $this->fetch();
	}
	
	public function dynmanagerylistsetstatus()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    if(!$id){
	        $this->error("数据传入失败！");
	    }
	    $status = $this->request->param('status', 0, 'intval');
	    
	    $nowtime=time();
	    
	    $rs=DB::name("dynamic")->where("id={$id}")->update(['status'=>$status]);
	    if(!$rs){
	        $this->error("操作失败");
	    }
	    
	    $this->success("操作成功");        
	}
	
	public function dynmanagerylistsetrecom()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    if(!$id){
	        $this->error("数据传入失败！");
	    }
	    $recoms = $this->request->param('recoms', 0, 'intval');
	    
	    $nowtime=time();
	    
	    $rs=DB::name("dynamic")->where("id={$id}")->update(['recoms'=>$recoms]);
	    if($rs===false){
	        $this->error("操作失败");
	    }
	    
	    $this->success("操作成功");        
	}
	
	public function dynmanagerylistdel()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    
	    $rs = DB::name('dynamic')->where("id={$id}")->delete();
	    if(!$rs){
	        $this->error("删除失败！");
	    }
	    
	    $this->success("删除成功！");
	}
	
	
	
}
