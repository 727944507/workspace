<?php

/**
 * 直播列表
 */
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiveingController extends AdminbaseController {
    
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
        $data = $this->request->param();
        $map=[];
        $map[]=['islive','=',1];
        $start_time=isset($data['start_time']) ? $data['start_time']: '';
        $end_time=isset($data['end_time']) ? $data['end_time']: '';
        
        if($start_time!=""){
           $map[]=['starttime','>=',strtotime($start_time)];
        }

        if($end_time!=""){
           $map[]=['starttime','<=',strtotime($end_time) + 60*60*24];
        }
        
        $uid=isset($data['uid']) ? $data['uid']: '';
        if($uid!=''){
            $map[]=['uid','=',$uid];
        }
        
        $this->configpri=getConfigPri();
			

    	$lists = Db::name("live")
                ->where($map)
                ->order("starttime DESC")
                ->paginate(20);
        
        $lists->each(function($v,$k){

            $v['userinfo']=getUserInfo($v['uid']);
            $v['thumb']=get_upload_path($v['thumb']);
            if($v['isvideo']!='1'){
				$v['pull']=PrivateKeyA('http',$v['uid']);
			}
            $v['type_val']=$this->getTypes($v['type']);
                
            return $v;           
        });
        
        $lists->appends($data);
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
		
		$type = Db::name('live_class')->select();
		$arr=[];
		foreach($type as $v){
			$arr[$v['id']]=$v['name'];
		}
        $this->assign("type", $arr);
        return $this->fetch();
    }
    
    function addPost(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $nowtime=time();
            $uid=$data['uid'];
			$type=$data['type'];//类型，1派单2交友3闲谈4点歌
            $thumb=$data['thumb'];//封面
            $bgid=$data['bgid'];//背景图片
            $title=$data['title'];//聊天室标题
            $des=$data['des'];//聊天室公告
            // $pull=$data['pull'];//聊天室播流地址
			$stream=$uid.'_'.$nowtime;
			
			
            $userinfo=DB::name('user')->field("ishost,isrecommend")->where(["id"=>$uid])->find();
            if(!$userinfo){
                $this->error('用户不存在');
            }
			if($type=='1' && $userinfo['ishost']!='1'){//不是派单用户
				$this->error('该用户未设为派单主持人');
			}
			//是否申请主持人
			$liveapplyinfo=DB::name('live_apply')->where(["uid"=>$uid])->find();
			if(!$liveapplyinfo || $liveapplyinfo['status']!='1'){
				$this->error('该用户还不是主持人');
			}
			/* if($pull==''){
				$this->error('请输入播流地址');
			} */
			if($thumb==''){
				$this->error('请上传封面');
			}
			if($title==''){
				$this->error('请输入标题');
			}else{
				if(mb_strlen($title) > 20){
					$this->error('标题最多20字');
				}
			}
			if($des==''){
				$this->error('请输入公告');
			}else{
				if(mb_strlen($des)>200){
					$this->error('公告最多200字');
				}
			}
			if($bgid==''){
				$bgid=isset($data['bgid'])?$data['bgid']:0;
				if($bgid==0){
					$this->error('请选择背景');
				}
			}
			$data2=array(
                "isvideo"=>1,
                "islive"=>1,
                "starttime"=>$nowtime,
                "showid"=>$nowtime,
                "stream"=>$stream,
                "nums"=>0,
                "totalnums"=>0,
                "deviceinfo"=>'',
                "uid"=>$uid,
                "type"=>$type,
                "thumb"=>$thumb,
                "bgid"=>$bgid,
                "title"=>$title,
                "des"=>$des,
                // "pull"=>$pull,
            );
			
			$liveinfo=DB::name('live')->field('uid,islive')->where(["uid"=>$uid])->find();
            if($liveinfo['islive']==1){
                $this->error('该用户聊天室开启中');
            }
			if($liveinfo){
				$rs = DB::name('live')->where(["uid"=>$uid])->update($data2);
			}else{
				$rs = DB::name('live')->insertGetId($data2);
			}
            if($rs===false){
                $this->error("添加失败！");
            }
            
			$userinfo1=getUserInfo($uid);

            $userinfo1['usertype']='50';
            $userinfo1['sign']='0'.'.0'.'1';
            $userinfo1['livetype']=$type;
        
            setcaches($token,$userinfo1);
            
			/* 重置坐位信息 */
            $sitting=[
                '1'=>0,
                '2'=>0,
                '3'=>0,
                '4'=>0,
                '5'=>0,
                '6'=>0,
                '7'=>0,
                '8'=>0,
            ];
            $key='sitting_'.$data['uid'];
            delcache($key);
            hMSet($key,$sitting);
			
            
            /* 清空心动选择 */
            $key6='heart_'.$uid;
            delcache($key6);
			
			/* 清除交友环节 */
			$key_step='jy_step';
			hDel($key_step,$uid);
			
			/* 清除交友环节 */
			$jy_step_time='jy_step_time';
			hDel($jy_step_time,$uid);
			
			/* 清空老板位申请列表 */
            $key2='boss_'.$uid;
            delcache($key2);
            
            /* 清空交友-男申请列表 */
            $key4='jy_'.$uid.'_1';
            delcache($key4);
            
            /* 清空交友-女申请列表 */
            $key5='jy_'.$uid.'_2';
            delcache($key5);
            
            /* 清除累计人数列表 */
            $key3='totalnums_'.$uid;
            delcache($key3);
			$this->success("添加成功！");
        }           
    }
    
    function edit(){
        $uid   = $this->request->param('uid', 0, 'intval');
        
        $data=Db::name('live')
            ->where("uid={$uid}")
            ->find();
        if(!$data){
            $this->error("信息错误");
        }
        
        $this->assign('data', $data);
        
        $this->assign("bglist", $this->getBglist());
        
		$type = Db::name('live_class')->select();
		$arr=[];
		foreach($type as $v){
			$arr[$v['id']]=$v['name'];
		}
		$this->assign("type", $arr);
        //$this->assign("type", $this->getTypes());
        
        return $this->fetch();


    }
    
    function editPost(){
        if ($this->request->isPost()) {
            
            $data      = $this->request->param();
            
            // $data['pull']=urldecode($data['pull']);
        
            $rs = DB::name('live')->update($data);
            if($rs===false){
                $this->error("修改失败！");
            }
            
			/* 重置坐位信息 */
            $sitting=[
                '1'=>0,
                '2'=>0,
                '3'=>0,
                '4'=>0,
                '5'=>0,
                '6'=>0,
                '7'=>0,
                '8'=>0,
            ];
            $key='sitting_'.$data['uid'];
            delcache($key);
            hMSet($key,$sitting);
			
			
            $this->success("修改成功！");
        }
    }
	//关闭后台聊天室
	function del(){
        $uid = $this->request->param('uid', 0, 'intval');
       
		$liveinfo = DB::name('live')->where(["uid"=>$uid])->find();
		if($liveinfo){
			$data2=['islive'=>0];
			DB::name('live')->where(["uid"=>$uid])->update($data2);
			
			$nowtime=time();
			
			$nums=$liveinfo['totalnums'];
			
			$data=[
				'uid'=>$liveinfo['uid'],
				'type'=>$liveinfo['type'],
				'title'=>$liveinfo['title'],
				'des'=>$liveinfo['des'],
				'thumb'=>$liveinfo['thumb'],
				'bgid'=>$liveinfo['bgid'],
				'showid'=>$liveinfo['showid'],
				'stream'=>$liveinfo['stream'],
				'starttime'=>$liveinfo['starttime'],
				'nums'=>$nums,
			];
			$data['endtime']=$nowtime;
			$length=$nowtime-$data['starttime'];
			$data['length']=$length;
			
			$rs=DB::name('live_record')->insertGetId($data);
			

			/* $info=[];
			$info['length']=handellength($length);
			$info['nums']=(string)$nums;
			$info['title']=$liveinfo['title']; */
		}
		
        if(!$rs){
            $this->error("删除失败！");
        }
        
        $this->success("删除成功！",url("liveing/index"));
            
    }
      
	  
		
}
