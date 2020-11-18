<?php
namespace App\Domain;

use App\Model\Agent as Model_Agent;

class Agent {
	
    /* 检测必填 */
	public function check($uid) {

        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $configpri=\App\getConfigPri();
        
        $isfill='0';
        $ismust='0';
        
        if($configpri['agent_switch']){
            
            $model = new Model_Agent();
            $info= $model->getAgentInfo($uid);
            if(!$info){
                /* 未填写邀请码 */
                $info2= $model->getAgentLower($uid);
                if($info2){
                    /* 已有下级 */
                    $isfill='1';
                }
            }else{
                /* 已填写邀请码 */
                $isfill='1';
            }
            
            if($configpri['agent_must']){
                $ismust='1';
            }
            
        }else{
           $isfill='1'; 
        }

        $rs['info'][0]['ismust']=$ismust;
        $rs['info'][0]['isfill']=$isfill;

		return $rs;
	}

    /* 生成并获取邀请码 */
	public function getMyCode($uid) {
		
		$where=['uid'=>$uid];
        $model = new Model_Agent();
        $codeinfo= $model->getCode($where);
		if(!$codeinfo){
			$code=$this->createCode();
			$data=[
				'uid'=>$uid,
				'code'=>$code,
			];
			$model->setCode($data);
		}else{
			$code=$codeinfo['code'];
		}
        
		return $code;
	}

	/* 获取邀请码 */
	public function getCode($where) {
		
        $model = new Model_Agent();
        $codeinfo= $model->getCode($where);
        
		return $rs;
	}

    /* 设置 */
	public function setAgent($uid,$code) {

        $rs = array('code' => 0, 'msg' => \PhalApi\T('设置成功'), 'info' => array());

		$where=['code'=>$code];
        $model = new Model_Agent();
        $codeinfo= $model->getCode($where);
        
        if(!$codeinfo){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('邀请码错误');
            return $rs;	
        }
        
        $touid=$codeinfo['uid'];
        
        if($touid==$uid){
            $rs['code'] = 1004;
            $rs['msg'] = \PhalApi\T('不能填写自己的邀请码');
            return $rs;	
        }
        
        $agent_uid=$model->getAgentInfo($uid);
        if($agent_uid){
            $rs['code'] = 1005;
            $rs['msg'] = \PhalApi\T('已填写过邀请码');
            return $rs;	
        }
        
        $agent_uid=$model->getAgentLower($uid);
        if($agent_uid){
            $rs['code'] = 1005;
            $rs['msg'] = \PhalApi\T('已经有下级了，不能再填写');
            return $rs;	
        }
        
        $nowtime=time();
        $data=[
            'uid'=>$uid,
            'one'=>$touid,
            'addtime'=>$nowtime,
        ];
        
        $agent_touid=$model->getAgentInfo($touid);
        if($agent_touid){
            if($agent_touid['one']==$uid ){
                $rs['code'] = 1006;
                $rs['msg'] = \PhalApi\T('不能互相填写');
                return $rs;	
            }
        }
        
        
        
        $result=$model->setAgent($data);
        
        $this->setAgentReward($touid);

		return $rs;
	}
    
    /* 邀请奖励 */
    public function setAgentReward($uid){
        
        $date = date("Ymd");
        
        $configpri=\App\getConfigPri();
        
        $agent_reward=$configpri['agent_reward'];
        $agent_daytimes=$configpri['agent_daytimes'];
        $agent_times=$configpri['agent_times'];

        
        \App\addVotes($uid,$agent_reward);
        
        return 1;
        
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
            
			$where=['code'=>$password];
			$model = new Model_Agent();
			$codeinfo= $model->getCode($where);
	        
            if(!$codeinfo){
                return $password;
            }            
        }
        $password = $this->createCode($len,$format);
        return $password;
    }
}
