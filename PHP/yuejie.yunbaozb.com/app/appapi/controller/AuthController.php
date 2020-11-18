<?php

/* 实名认证 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use cmf\lib\Upload;

class AuthController extends HomeBaseController
{
    public function index()
    {
        $data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason=lang('您的登陆状态失效，请重新登陆！');
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
        $this->assign("uid",$uid);
        $this->assign("token",$token);
        
        $info=Db::name("user_auth")->where(['uid'=>$uid])->find();

		if($info){
            $info['front_view1']=get_upload_path($info['front_view']);
            $info['back_view1']=get_upload_path($info['back_view']);
            $info['handset_view1']=get_upload_path($info['handset_view']);
            $this->assign("info",$info);
            if($info['status']!=1){
                return $this->fetch('status');
            }
            
           return $this->fetch('apply');
		}
        
        return $this->fetch();
    }

    public function apply()
    {
        $data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
        $reset=checkNull($data['reset']);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason=lang('您的登陆状态失效，请重新登陆！');
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        
        session('user.id',$uid);
        
        $this->assign("uid",$uid);
        $this->assign("token",$token);
        
        if($reset!=1){
            $info=Db::name("user_auth")->where(['uid'=>$uid])->find();

            if($info){
                $info['front_view1']=get_upload_path($info['front_view']);
                $info['back_view1']=get_upload_path($info['back_view']);
                $info['handset_view1']=get_upload_path($info['handset_view']);
                $this->assign("info",$info);
                if($info['status']!=1){
                    return $this->fetch('status');
                }
            }
        }
        
		//获取后台插件配置的七牛信息
        $qiniu_plugin=Db::name("plugin")->where("name='Qiniu'")->find();

        if(!$qiniu_plugin){
            $reason='请联系管理员确认配置信息';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }
        $qiniu_config=json_decode($qiniu_plugin['config'],true);

        if(!$qiniu_config){
            $reason='请联系管理员确认配置信息';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        $protocol=$qiniu_config['protocol']; //协议名称
        $domain=$qiniu_config['domain']; //七牛加速域名
        $zone=$qiniu_config['zone']; //存储区域代号

        if(!$protocol || !$domain || !$zone){
            $reason='请联系管理员确认配置信息';
            $this->assign('reason', $reason);
            return $this->fetch(':error');
        }

        $upload_url='';

        switch ($zone) {
            case 'z0': //华东
                $upload_url='up.qiniup.com';
                break;
            case 'z1': //华北
                $upload_url='up-z1.qiniup.com';
                break;
            case 'z2': //华南
                $upload_url='up-z2.qiniup.com';
                break;
            case 'na0': //北美
                $upload_url='up-na0.qiniup.com';
                break;
            case 'as0': //东南亚
                $upload_url='up-as0.qiniup.com';
                break;
            
            default:
                $upload_url='up.qiniup.com';
                break;
        }
        
		$this->assign("protocol",$protocol);
        $this->assign("domain",$domain);
        $this->assign("upload_url",$upload_url);
		
		
        return $this->fetch();

    }
    
	/*认证保存*/
	public function auth_save(){

		$rs=array("code"=>0,"msg"=>lang("申请成功"),"info"=>array());
        $data = $this->request->param();
        
		$uid=checkNull($data["uid"]);
		$token=checkNull($data["token"]);
		$realname=checkNull($data["realname"]);
		$phone=checkNull($data["phone"]);
		$cardno=checkNull($data["cardno"]);
		$front=checkNull($data["front"]);
		$back=checkNull($data["back"]);
		$hand=checkNull($data["hand"]);
        
        if(checkToken($uid,$token)==700){
            $rs['code']=700;
            $rs['msg']=lang('您的登陆状态失效，请重新登陆！');
            echo json_encode($rs);
			exit;
		}
        
        $auth=Db::name("user_auth")->where(['uid'=>$uid])->find();
        if($auth && $auth['status']==0){
            $rs['code']=1001;
			$rs['msg']=lang("认证审核中，不能申请");
			echo json_encode($rs);
            exit;
        }

		if($realname==""){
			$rs['code']=1001;
			$rs['msg']=lang("请输入姓名");
			echo json_encode($rs);
            exit;
		}

        if($cardno==""){
			$rs['code']=1002;
			$rs['msg']=lang("请输入身份证号码");
			echo json_encode($rs);
            exit;
		}

		if($phone==""){
			$rs['code']=1002;
			$rs['msg']=lang("请输入手机号码");
			echo json_encode($rs);
            exit;
		}
        
        if($front==""){
			$rs['code']=1002;
			$rs['msg']=lang("请上传证件正面");
			echo json_encode($rs);
            exit;
		}
        
        if($back==""){
			$rs['code']=1002;
			$rs['msg']=lang("请上传证件反面");
			echo json_encode($rs);
            exit;
		}
        
        if($hand==""){
			$rs['code']=1002;
			$rs['msg']=lang("请上传手持证件正面照");
			echo json_encode($rs);
            exit;
		}
        
        $nowtime=time();
        
        $data=[
            'uid'=>$uid,
            'name'=>$realname,
            'cer_no'=>$cardno,
            'mobile'=>$phone,
            'front_view'=>$front,
            'back_view'=>$back,
            'handset_view'=>$hand,
            'addtime'=>$nowtime,
            'uptime'=>$nowtime,
            'status'=>0,
            'reason'=>'',
        ];


		if($auth){
			$result=Db::name("user_auth")->where(['uid'=>$uid])->update($data);
		}else{
			$result=Db::name("user_auth")->insert($data);
		}

		echo json_encode($rs);
        exit;
	}
	
	public function getuploadtoken(){
        
        $uploader = new Upload();
        $result = $uploader->getuploadtoken();

        if ($result === false) {
            echo json_encode(array("ret"=>0,'file'=>'','msg'=>'获取失败'));
            exit;
        }
        echo json_encode(array("ret"=>200,"token"=>$result['token'],'domain'=>$result['domain'],'msg'=>''));
        exit;
	}
}
