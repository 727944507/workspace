<?php

/**
 * 礼物
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class GiftController extends AdminbaseController {
    protected function getTypes($k=''){
        $type=[
            '0'=>'普通礼物',
            '1'=>'豪华礼物',
        ];
        if($k==''){
            return $type;
        }
        return isset($type[$k]) ? $type[$k]: '';
    }
    
    protected function getSwftype($k=''){
        $swftype=[
            '0'=>'GIF',
            '1'=>'SVGA',
        ];
        if($k==''){
            return $swftype;
        }
        return isset($swftype[$k]) ? $swftype[$k]: '';
    }
    
    function index(){

    	$lists = Db::name("gift")
			->order("list_order asc,id desc")
			->paginate(20);
        
        $lists->each(function($v,$k){
			$v['gifticon']=get_upload_path($v['gifticon']);
			$v['swf']=get_upload_path($v['swf']);
            return $v;           
        });
        
        $page = $lists->render();

    	$this->assign('lists', $lists);

    	$this->assign("page", $page);
        
    	$this->assign("type", $this->getTypes());
    	$this->assign("swftype", $this->getSwftype());
    	
    	return $this->fetch();
    }
    
	function del(){
        
        $id = $this->request->param('id', 0, 'intval');
        
        $rs = DB::name('gift')->where("id={$id}")->delete();
        if(!$rs){
            $this->error("删除失败！");
        }
                    
        $this->resetcache();
        $this->success("删除成功！");
        
	}
    
    /* 全站飘屏 */
    function plat(){
        
        $id = $this->request->param('id', 0, 'intval');
        $isplatgift = $this->request->param('isplatgift', 0, 'intval');
        
        $rs = DB::name('gift')->where("id={$id}")->update(['isplatgift'=>$isplatgift]);
        if(!$rs){
            $this->error("操作失败！");
        }
                    
        $this->resetcache();
        $this->success("操作成功！");
        
	}
    
    //排序
    public function listOrder() { 
		
        $model = DB::name('gift');
        parent::listOrders($model);
        
        
        $this->resetcache();
        $this->success("排序更新成功！");
        
    }

    function add(){
        
        $this->assign("type", $this->getTypes());
    	$this->assign("swftype", $this->getSwftype());
        
        return $this->fetch();				
    }

	function addPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
            $giftname=$data['giftname'];
            if($giftname == ''){
                $this->error('请输入名称');
            }else{
                $check = Db::name('gift')->where("giftname='{$giftname}'")->find();
                if($check){
                    $this->error('名称已存在');
                }
            }
            
            
            $needcoin=$data['needcoin'];
            $gifticon=$data['gifticon'];
            
            if($needcoin==''){
                $this->error('请输入价格');
            }

            if($gifticon==''){
                $this->error('请上传图片');
            }
            
            $swftype=$data['swftype'];
            $data['swf']=$data['gif'];
            if($swftype==1){
                $data['swf']=$data['svga'];
            }
            $data['addtime']=time();
            unset($data['gif']);
            unset($data['svga']);
            
			$id = DB::name('gift')->insertGetId($data);
            if(!$id){
                $this->error("添加失败！");
            }
            
            
            $this->resetcache();
            $this->success("添加成功！");
            
		}			
	}
    
    function edit(){

        $id   = $this->request->param('id', 0, 'intval');
        
        $data=Db::name('gift')
            ->where("id={$id}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign("type", $this->getTypes());
    	$this->assign("swftype", $this->getSwftype());
        
        $this->assign('data', $data);
        return $this->fetch();            
    }
    
	function editPost(){
		if ($this->request->isPost()) {
            
            $data      = $this->request->param();

            $id=$data['id'];
            $giftname=$data['giftname'];
            if($giftname == ''){
                $this->error('请输入名称');
            }else{
                $check = Db::name('gift')->where("giftname='{$giftname}' and id!={$id}")->find();
                if($check){
                    $this->error('名称已存在');
                }
            }
            
            
            $needcoin=$data['needcoin'];
            $gifticon=$data['gifticon'];
            
            if($needcoin==''){
                $this->error('请输入价格');
            }

            if($gifticon==''){
                $this->error('请上传图片');
            }
            
            $swftype=$data['swftype'];
            $data['swf']=$data['gif'];
            if($swftype==1){
                $data['swf']=$data['svga'];
            }
            unset($data['gif']);
            unset($data['svga']);
            
			$rs = DB::name('gift')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
            
            $this->resetcache();
            $this->success("修改成功！");
		}	
	}
        
    function resetcache(){
        $key='getGiftList';
        
		$rs=DB::name('gift')
			->field("id,type,giftname,needcoin,gifticon,swftype,swf,swftime")
			->order("list_order asc,id desc")
			->select();
        if($rs){
            setcaches($key,$rs);
        }   
        return 1;
    }
}
