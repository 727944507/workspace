<?php

/**
 * 直播列表
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiveingClassController extends AdminbaseController {
    
	protected function getBglist(){

        $bglist=Db::name("live_bg")->order('list_order asc, id asc')->column('id,name,thumb');

        return $bglist;
    }
    protected function getTypes($k=''){
        $type=array(
            '1'=>'派单',
            '2'=>'交友',
            '3'=>'闲谈',
            '4'=>'点歌'
        );
        if($k===''){
            return $type;
        }
        
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    function index(){

    	$lists = Db::name("live_class")
                ->order("sort asc")
                ->paginate(10);
        
        $lists->each(function($v,$k){
            $v['img']=get_upload_path($v['img']);
            return $v;           
        });
        
        $page = $lists->render();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page);
		$config=getConfigPri();
		$this->assign('config', $config);
		
    	return $this->fetch();
	}

				  
				
	//添加聊天室
	function add(){
        
        $this->assign("bglist", $this->getBglist());
	
        $this->assign("type", $this->getTypes());
        
        return $this->fetch();
    }
    
    function addPost(){
        if ($this->request->isPost()) {
            $data = $this->request->param('post');
            $data['create_time']=time();
			
			Db::name('live_class')->insert($data);
			$this->success("添加成功！");
        }           
    }
    
    function edit(){
        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('live_class')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        /*$this->assign("bglist", $this->getBglist());
        $this->assign("type", $this->getTypes());*/
        
        return $this->fetch();


    }
    
    function editPost(){
        if ($this->request->isPost()) {
			$id   = $this->request->param('id', 0, 'intval');
            $data = $this->request->param('post');
        
            $rs = DB::name('live_class')->where('id',$id)->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            $this->success("修改成功！");
        }
    }
	//关闭后台聊天室
	function del(){
        $id = $this->request->param('id', 0, 'intval');
       
		$liveinfo = DB::name('live_class')->where(["id"=>$id])->find();
		if($liveinfo){
			$rs = DB::name('live_class')->where(["id"=>$id])->delete();

			/* $info=[];
			$info['length']=handellength($length);
			$info['nums']=(string)$nums;
			$info['title']=$liveinfo['title']; */
		}
		
        /*if(!$rs){
            $this->error("删除失败！");
        }*/
        
        $this->success("删除成功！",url("liveingClass/index"));
            
    }
      
	  
		
}
