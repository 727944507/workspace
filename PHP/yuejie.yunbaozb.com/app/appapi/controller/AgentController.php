<?php

/* 分享 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class AgentController extends HomebaseController{

	public function index() {
        $data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        $configpri=getConfigPri();
        if($configpri['agent_switch']!=1){
            $reason='信息错误';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
        }
        
        $code=Db::name('agent_code')->where("uid={$uid}")->value('code');
        if(!$code){
            $code=$this->createCode();
            Db::name('agent_code')->insert(['uid'=>$uid,'code'=>$code]);
        }
        
        $code_a=str_split($code);
        
        $one_nums=Db::name('agent')->where("one={$uid}")->count();
        
        $this->assign('uid', $uid);
        $this->assign('token', $token);
        $this->assign('code', $code);
        $this->assign('code_a', $code_a);
        $this->assign('one_nums', $one_nums);
        $this->assign('configpri', $configpri);
        

        return $this->fetch();
	}	
    
    public function share() {
        $data = $this->request->param();
        $code=checkNull($data['code']);
        

        $configpri=getConfigPri();
        if($configpri['agent_switch']!=1){
            $reason='信息错误';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
        }
        
        $uid=Db::name('agent_code')->where("code='{$code}'")->value('uid');
        if(!$uid){
            $reason='信息错误';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
        }
        
        $userinfo=getUserInfo($uid);


        $this->assign('code', $code);
        $this->assign('configpri', $configpri);
        $this->assign('userinfo', $userinfo);
        

        return $this->fetch();
	}

    public function one() {
        
        $data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			$reason='您的登陆状态失效，请重新登陆！';
			$this->assign('reason', $reason);
			return $this->fetch(':error');
		}
        

        $map[]=['one','=',$uid];
        
        $list = Db::name('agent')
            ->where($map)
            ->order("addtime desc")
            ->paginate(50);
        
        $list->each(function($v,$k){
            $coin='0';
            $info=Db::name('agent_profit')->where("uid={$v['uid']}")->value('one');
            if($info){
                $coin=$info;
            }
            $v['coin']=$coin;
            
            $userinfo=getUserInfo($v['uid']);
            $v['userinfo']=$userinfo;
           
            $v['addtime']=date('Y.m.d',$v['addtime']);
           
            return $v; 
        });        
       

        $this->assign('list', $list);
        $this->assign('uid', $uid);
        $this->assign('token', $token);
        

        return $this->fetch();
	}
    
	function one_more(){
        $result=array(
			'list'=>[],
			'nums'=>0,
			'isscroll'=>0,
		);
        
        $data = $this->request->param();
        $uid=(int)checkNull($data['uid']);
        $token=checkNull($data['token']);
        
        $checkToken=checkToken($uid,$token);
		if($checkToken==700){
			return $result;
		}
		
		$map[]=['one','=',$uid];
        $pnums=50;
        $list = Db::name('agent')
            ->where($map)
            ->order("addtime desc")
            ->paginate($pnums);
        
        $list->each(function($v,$k){
            $coin='0';
            $info=Db::name('agent_profit')->where("uid={$v['uid']}")->value('one');
            if($info){
                $coin=$info;
            }
            $v['coin']=$coin;
            
            $userinfo=getUserInfo($v['uid']);
            $v['userinfo']=$userinfo;
           
            $v['addtime']=date('Y.m.d',$v['addtime']);
           
            return $v; 
        });
		
        $nums=count($list);
		if($nums<$pnums){
			$isscroll=0;
		}else{
			$isscroll=1;
		}
        
        $result=array(
			'list'=>$list,
			'nums'=>$nums,
			'isscroll'=>$isscroll,
		);

        return $result;
	}
	
	/* 生成邀请码 */
	protected function createCode($len=6,$format='ALL2'){
        $is_abc = $is_numer = 0;
        $password = $tmp =''; 
        switch($format){
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'ALL2':
                $chars='ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
                break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars='0123456789';
                break;
            default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        
        while(strlen($password)<$len){
            $tmp =substr($chars,(mt_rand()%strlen($chars)),1);
            if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
                $is_numer = 1;
            }
            if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
                $is_abc = 1;
            }
            $password.= $tmp;
        }
        if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
            $password = $this->createCode($len,$format);
        }
        if($password!=''){
            
            $oneinfo=Db::name('agent_code')
	            ->where("code='{$password}'")
	            ->find();
	        
            if(!$oneinfo){
                return $password;
            }            
        }
        $password = $this->createCode($len,$format);
        return $password;
    }
    
}