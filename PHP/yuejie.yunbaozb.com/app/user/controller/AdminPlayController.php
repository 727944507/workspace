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

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use app\admin\model\UserModel;
use app\admin\model\UserAuthModel;
use think\Db;
use think\db\Query;

/**
  *陪练师列表
 */
class AdminPlayController extends AdminBaseController
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
	 * 陪练师列表
	 */
    public function index()
    {
        $data = $this->request->param();
        $map=[];
        if($data['status']!=''){
            $map[]=['a.status','=',$data['status']];
        }
    
        $start_time=$data['start_time'];
        $end_time=$data['end_time'];
        
        if($start_time!=""){
           $map[]=['a.addtime','>=',strtotime($start_time)];
        }
    
        if($end_time!=""){
           $map[]=['a.addtime','<=',strtotime($end_time) + 60*60*24];
        }
    
        if($data['uid']!=''){
            $map[]=['a.uid','=',intval($data['uid'])];
        }
        
        if($data['skillid']!=''){
            $map[]=['a.skillid','=',intval($data['skillid'])];
        }
		
		$nums=Db::name("user")
				->alias("a")
				->join('skill_auth i', 'a.id = i.uid')
				->where($map)
				->where('a.user_type=2')
				->where('i.status=1')
				->group('i.uid')
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
				->join('skill_auth i', 'a.id = i.uid')
				->where($map)
				->where('i.status=1')
				->where('a.user_type=2')
				->where('a.online!=0')
				->group('i.uid')
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
        
        
        $list = Db::name('skill_auth')
			->field('*,a.stars as starts,a.id as auid,count(0) as num')
			->alias("a")
			->join('user i', 'a.uid = i.id')
            ->where($map)
			->where('a.status=1')
            ->order("addtime desc")
			->group('uid')
            ->paginate(10);
        
        $list->each(function($v,$k){
           $v['userinfo']= getUserInfo($v['uid']);
           
           $v['thumb']=get_upload_path($v['thumb']);
    
           return $v; 
        });
        
        $list->appends($data);
        
        $page = $list->render();
        $this->assign("page", $page);
        
		$this->assign('nums', $nums);
		$this->assign('online', $online);
        $this->assign('list', $list);
        $this->assign('status', $this->getStatus());
        $this->assign('skill', $this->getSkill());
        $this->assign('level', $this->getLevel());
    
        return $this->fetch();
    }
	
	/**
	 * 编辑修改陪练师信息
	 */
	public function update()
	{
		$content = hook_one('user_admin_update_view');
		
		if (!empty($content)) {
		    return $content;
		}
		
		$id      = $this->request->param('auth_id', 0, 'intval');
		if(!empty($id)){
			$auth_id = $id;
		}else{
			$this->error('数据出错');
		}
		
		$skill = Db::name('skill')
		    ->where($map)
		    ->order("list_order asc")->select();
			
		$skill->each(function($v,$k){
		    $v['thumb']=get_upload_path($v['thumb']);
		    return $v;
		});

		$result = Db::name('user')
			->alias("a")
			->join('skill_auth i','i.uid = a.id')
			->where('i.status=1')
			->where('i.id', intval($auth_id))
		    ->find();

		//$result  = Db::name('slideItem')->where('slide_id', $slideId)->select();
		$this->assign('slide_id', $id);
		$this->assign('result', $result);
		$this->assign('skill', $skill);
		return $this->fetch();
	}
	
	/**
	 * 保存陪练师编辑信息
	 */
	
	public function updatePost()
	{
		$data = $this->request->param('post');
		$id = intval($this->request->param('id'));
		
		$data['sex'] = intval($data['sex']);
		if(trim($data['avatar']) == ''){
			unset($data['avatar']);
		}
		
		if($id<1){
			$this->error("数据有误");
		}
		
		$auth = Db::name('skill_auth')->where('id',$id)->find();
		$authResult = Db::name('skill_auth')->where('id',$id)->strict(false)->update($data);
		$userResult = Db::name('user')->where('id',$auth['uid'])->strict(false)->update(['orders'=>$data['orders'],'funs'=>$data['funs']]);
		
		if(!$authResult){
			$this->error("数据有误");
		}

		if(!$userResult){
			$this->error("数据有误");
		}
		
		$this->success("保存成功！");
	}
	
	/**
	 * 查看评论
	 */
	public function comments()
	{
	    
	    $data = $this->request->param();
	    $map=[];
	    
	    $liveuid=isset($data['liveuid'])? $data['liveuid'] : '0' ;
	    if($liveuid!=0){
	        $map[]=['liveuid','=',$liveuid];
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
	    
		$skill = Db::name('skill')
		    //->where($map)
		    ->order("list_order asc")->select();
	    
	    $list = Db::name('skill_comment')
	        ->where($map)
	        ->order("id desc")
	        ->paginate(20);
	    
	    /*$list->each(function($v,$k){
	       $v['userinfo']= getUserInfo($v['uid']);
	       return $v; 
	    });*/
	    
	    $list->appends($data);
	    
	    $page = $list->render();
	    $this->assign("page", $page);
	    $this->assign("liveuid", $liveuid);
	    $this->assign('skill', $skill);   
	    $this->assign('list', $list);
	
	    return $this->fetch();
	}
	
	/**
	 * 编辑评论
	 */
	
	public function comments_edit()
	{
		$content = hook_one('user_admin_update_view');
		
		if (!empty($content)) {
		    return $content;
		}
		
		$id      = $this->request->param('edit_id', 0, 'intval');
		if(!empty($id)){
			$edit_id = $id;
		}else{
			$this->error('数据出错');
		}
		
		$comments = Db::name('skill_comment')
		    ->where('id',$edit_id)
			->find();
			
		$this->assign('edit_id', $id);
		$this->assign('comments', $comments);
		return $this->fetch();
	}
	
	/**
	 * 保存编辑评论
	 */
	
	public function editPost()
	{
		$data = $this->request->param('post');
		$id = intval($this->request->param('id'));
		
		if($id<=0){
			$this->error("数据有误");
		}
		
		$commentResult = Db::name('skill_comment')->where('id',$id)->strict(false)->update($data);
		
		if(!$commentResult){
			$this->error("数据有误");
		}
		
		$this->success("保存成功！");
	}
	
	/**
	 * 删除评论
	 */
	public function del()
	{
	    $id = $this->request->param('id', 0, 'intval');
	    
	    $info=DB::name('skill_comment')->where('id',intval($id))->find();
	    $rs = DB::name('skill_comment')->where('id',intval($id))->delete();
		
	    if(!$rs){
	        $this->error("删除失败！");
	    }
	    DB::name('skill_auth')
	            ->where("uid={$info['liveuid']} and skillid={$info['skillid']} and comments>=1")
	            ->setDec('comments','1');
	    
	    $this->success("删除成功！");
	}
	
	
}
