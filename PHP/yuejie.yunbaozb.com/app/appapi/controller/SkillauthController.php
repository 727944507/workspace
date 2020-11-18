<?php

/* 技能认证 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class SkillauthController extends HomeBaseController
{

    public function apply()
    {
        $data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
        $skillid=checkNull($data['skillid']);
        $reset=checkNull($data['reset']);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason=lang('您的登陆状态失效，请重新登陆！');
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
        if($skillid<1){
			$reason=lang('信息错误');
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
        $isexist=Db::name("user_auth")
				->where(['uid'=>$uid,'status'=>1])
				->find();
        if(!$isexist){
            $reason=lang('请先进行实名认证或等待审核通过');
			$this->assign('reason', $reason);
			return $this->fetch(':error');
        }
        
        session('user.id',$uid);
        
        $this->assign("uid",$uid);
        $this->assign("token",$token);
        $this->assign("skillid",$skillid);
        
        $skillinfo=Db::name("skill")->where(['id'=>$skillid])->find();
        if(!$skillinfo){
           $reason=lang('信息错误');
			$this->assign('reason', $reason);
			return $this->fetch(':error'); 
        }
        
        $lang=session('lang');
        if($lang=='en'){
            $skillinfo['name']=$skillinfo['name_en'];
            $skillinfo['auth_tip']=$skillinfo['auth_tip_en'];
        }
        
        $this->assign("skillinfo",$skillinfo);
        
        
        $level=Db::name("skill_level")->field('name,name_en,levelid as id')->where(['skillid'=>$skillid])->order('levelid asc')->select()->toArray();
        
        foreach($level as $k=>$v){
            $value=$v['name'];
            if($lang=='en'){
                $value=$v['name_en'];
            }
            $v['value']=$value;
            $level[$k]=$v;
            
        }
        
        $this->assign("level",json_encode($level));
        
        if($reset!=1){
            $info=Db::name("skill_auth")->where(['uid'=>$uid,'skillid'=>$skillid])->find();

            if($info){
                $info['thumb1']=get_upload_path($info['thumb']);
                $levelname='';
                foreach($level as $k=>$v){
                    
                    if($v['id']==$info['levelid']){
                        $levelname=$v['name'];
                        if($lang=='en'){
                            $levelname=$v['name_en'];
                        }
                        break;
                    }
                    
                }
                $info['level']=$levelname;
                $this->assign("info",$info);
                if($info['status']!=1){
                    return $this->fetch('status');
                }
            }
        }
        
        return $this->fetch();

    }
    
	/*认证保存*/
	public function auth_save(){

		$rs=array("code"=>0,"msg"=>lang("申请成功"),"info"=>array());
        $data = $this->request->param();
        
		$uid=checkNull($data["uid"]);
		$token=checkNull($data["token"]);
		$skillid=checkNull($data["skillid"]);
		$thumb=checkNull($data["thumb"]);
		$levelid=(int)checkNull($data["levelid"]);
        
        if(checkToken($uid,$token)==700){
            $rs['code']=700;
            $rs['msg']=lang('您的登陆状态失效，请重新登陆！');
            echo json_encode($rs);
			exit;
		}
        
        if($skillid<1){
            $rs['code']=700;
            $rs['msg']=lang('信息错误');
            echo json_encode($rs);
		}
        
        $auth=Db::name("skill_auth")->where(['uid'=>$uid,'skillid'=>$skillid])->find();
        if($auth && $auth['status']==0){
            $rs['code']=1001;
			$rs['msg']=lang("认证审核中，不能申请");
			echo json_encode($rs);
            exit;
        }

		if($thumb==""){
			$rs['code']=1001;
			$rs['msg']=lang("请上传截图");
			echo json_encode($rs);
            exit;
		}

        if($levelid<0){
			$rs['code']=1002;
			$rs['msg']=lang("请选择段位");
			echo json_encode($rs);
            exit;
		}
        
        
        $skill=Db::name("skill")->where(['id'=>$skillid])->find();
        if(!$skill){
            $rs['code']=1002;
			$rs['msg']=lang("技能信息错误");
			echo json_encode($rs);
            exit;
        }
        
        $levelinfo=Db::name("skill_level")->where(['skillid'=>$skillid,'levelid'=>$levelid])->find();
        if(!$levelinfo){
            $rs['code']=1002;
			$rs['msg']=lang("段位信息错误");
			echo json_encode($rs);
            exit;
        }
        
        $nowtime=time();
        
        /* 用户性别 */
        $userinfo=Db::name("user")->field('sex')->where(['id'=>$uid])->find();
        
        $data=[
            'uid'=>$uid,
            'sex'=>$userinfo['sex'],
            'skillid'=>$skillid,
            'thumb'=>$thumb,
            'levelid'=>$levelid,
            'addtime'=>$nowtime,
            'uptime'=>$nowtime,
            'status'=>0,
            'switch'=>0,
            'reason'=>'',
        ];


		if($auth){
			$result=Db::name("skill_auth")->where(['uid'=>$uid,'skillid'=>$skillid])->update($data);
		}else{
			$result=Db::name("skill_auth")->insert($data);
		}

		echo json_encode($rs);
        exit;
	}
}
