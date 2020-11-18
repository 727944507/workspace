<?php
namespace App\Api;

use PhalApi\Api;
use App\Domain\Login as Domain_Login;

/**
 * 注册、登录
 */
if (!session_id()) session_start();

class Login extends Api {
    public function getRules() {
        return array(
            'login' => array(
                'username' => array('name' => 'username', 'type' => 'string', 'desc' => '用户名'),
                'code' => array('name' => 'code', 'type' => 'string', 'desc' => '验证码'),
                'source' => array('name' => 'source', 'type' => 'int',  'default'=>'0', 'desc' => '来源设备,0web，1android，2ios，3小程序'),
            ),
            
            'loginByThird' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'desc' => '第三方openid'),
                'type' => array('name' => 'type', 'type' => 'int', 'default'=>'0', 'desc' => '第三方标识,0PC，1QQ，2微信，3新浪，4facebook，5twitter，6：ios'),
                'nicename' => array('name' => 'nicename', 'type' => 'string',   'default'=>'',  'desc' => '第三方昵称'),
                'avatar' => array('name' => 'avatar', 'type' => 'string',  'default'=>'', 'desc' => '第三方头像'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
                'source' => array('name' => 'source', 'type' => 'int',  'default'=>'0', 'desc' => '来源设备,0web，1android，2ios，3小程序'),
            ),
            
            'getCode' => array(
				'account' => array('name' => 'account', 'type' => 'string', 'desc' => '手机号码'),
                'sign' => array('name' => 'sign', 'type' => 'string',  'default'=>'', 'desc' => '签名'),
			),
			'getCancelCondition'=>array(
            	'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string','desc' => '用户Token'),
            ),
            'cancelAccount'=>array(
                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'desc' => '用户ID'),
                'token' => array('name' => 'token', 'type' => 'string',  'desc' => '用户Token'),
                'time' => array('name' => 'time', 'type' => 'string', 'desc' => '时间戳'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
            ),
        );
    }
    
    
    /**
     * 登录方式开关信息
     * @desc 用于获取登录方式开关信息
     * @return int code 操作码，0表示成功
     * @return array info 
     * @return string info[][0] 登录方式标识
     * @return string msg 提示信息
     */
     
    public function getLoginType() {
		/* $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $configpri=\App\getConfigPri();
        $rs['info'] = $configpri['login_type'];

        return $rs; */
		$rs = array('code' => 0, 'msg' => '', 'info' => array());

        $info = \App\getConfigPub();
		
		$configpri=\App\getConfigPri();
      

        //登录弹框那个地方
        $login_alert=array(
            'title'=>$info['login_alert_title'],
            'content'=>$info['login_alert_content'],
            'login_title'=>$info['login_clause_title'],
            'message'=>array(
                array(
                    'title'=>$info['login_service_title'],
                    'url'=>\App\get_upload_path($info['login_service_url']),
                ),
                array(
                    'title'=>$info['login_private_title'],
                    'url'=>\App\get_upload_path($info['login_private_url']),
                ),
            )
        );
		
		$login_type=$configpri['login_type'];
        foreach ($login_type as $k => $v) {
            if($v=='ios'){
                unset($login_type[$k]);
                break;
            }
        }

        $login_type=array_values($login_type);

        $rs['info'][0]['login_alert'] = $login_alert;
        $rs['info'][0]['login_type'] = $login_type;
		$rs['info'][0]['login_type_ios'] = $configpri['login_type'];

        return $rs;
    }
    
    /**
     * 登陆
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nickname 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].level_anchor 主播等级
     * @return string info[0].birthday 生日
     * @return string info[0].age 年龄
     * @return string info[0].token 用户Token
     * @return string info[0].isauth 是否认证，0否1是
     * @return string info[0].usersig IM签名
     * @return string msg 提示信息
     */
    public function login() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $username = \App\checkNull($this->username);
        $code = \App\checkNull($this->code);
        $source = \App\checkNull($this->source);
        
        /*if($username==''){
            $rs['code'] = 995;
            $rs['msg'] = \PhalApi\T('请输入手机号');
            return $rs;
        }
        
        if($code==''){
            $rs['code'] = 996;
            $rs['msg'] = \PhalApi\T('请输入验证码');
            return $rs;
        }
        
        
        if(!isset($_SESSION['reg_account']) || !$_SESSION['reg_account'] || !isset($_SESSION['reg_code']) || !$_SESSION['reg_code'] ){
            $rs['code'] = 996;
            $rs['msg'] = \PhalApi\T('请先获取验证码');
            return $rs;		
        }

        if(time() - $_SESSION['reg_expiretime'] > 5*60){
            $rs['code'] = 996;
            $rs['msg'] = \PhalApi\T('验证码已过期，请重新获取');
            return $rs;		
        }*/
        
        /*if($_SESSION['reg_account']!=$username){
            $rs['code'] = 995;
            $rs['msg'] = \PhalApi\T('手机号码错误');
            return $rs;		
        }
        
        if($_SESSION['reg_code']!=$code){
            $rs['code'] = 996;
            $rs['msg'] = \PhalApi\T('验证码错误');
            return $rs;		
        }*/
        
        $domain = new Domain_Login();
        $info = $domain->userLogin($username,$source);
		//var_dump($username,$source);die;

        return $info;
    }
    
	/**
	 * 获取注册验证码
	 * @desc 用于注册获取验证码
	 * @return int code 操作码，0表示成功,2发送失败
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function getCode() {
		$rs = array('code' => 0, 'msg' => \PhalApi\T('发送成功，请注意查收'), 'info' => array());
		
		$account = \App\checkNull($this->account);
		$sign = \App\checkNull($this->sign);
		
        if($account==''){
			$rs['code']=995;
			$rs['msg']=\PhalApi\T('请输入手机号');
			return $rs;
		}
        
		$isok=\App\checkMobile($account);
		if(!$isok){
			$rs['code']=995;
			$rs['msg']=\PhalApi\T('请输入正确的手机号');
			return $rs;
		}
        
        $checkdata=array(
            'account'=>$account
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        		
		if(isset($_SESSION['reg_account']) && $_SESSION['reg_account']==$account && isset($_SESSION['reg_expiretime']) && $_SESSION['reg_expiretime']> time() ){
			$rs['code']=996;
			$rs['msg']=\PhalApi\T('验证码5分钟有效，请勿多次发送');
			return $rs;
		}

		$code = \App\random(6);
		
		/* 发送验证码 */
 		$result=\App\sendCode($account,$code);
		if($result['code']==0){
			$_SESSION['reg_account'] = $account;
			$_SESSION['reg_code'] = $code;
			$_SESSION['reg_expiretime'] = time() +60*5;	
		}else if($result['code']==667){
			$_SESSION['reg_account'] = $account;
            $_SESSION['reg_code'] = $result['msg'];
            $_SESSION['reg_expiretime'] = time() +60*5;
            
            $rs['code']=1002;
			$rs['msg']=\PhalApi\T('验证码为：{n}',[ 'n'=>$result['msg'] ]);
		}else{
			$rs['code']=1002;
			$rs['msg']=$result['msg'];
		} 

		return $rs;
	}
    
    /**
     * 三方登录
     * @desc 用于用户三方登录
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nickname 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].level_anchor 主播等级
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string info[0].isauth 是否认证，0否1是
     * @return string info[0].usersig IM签名
     * @return string msg 提示信息
     */
    public function loginByThird() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$openid=\App\checkNull($this->openid);
		$type=\App\checkNull($this->type);
		$nicename=\App\checkNull($this->nicename);
		$avatar=\App\checkNull($this->avatar);
		$source=\App\checkNull($this->source);
		$sign=\App\checkNull($this->sign);
        
        if($openid=='' || $type=='' || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;	
        }
        
        $checkdata=array(
            'type'=>$type,
            'openid'=>$openid
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;	
        }

        $domain = new Domain_Login();
        $info = $domain->userLoginByThird($openid,$type,$nicename,$avatar,$source);
		
        return $info;
    }
	
	/**
     * 获取注销账号的条件
     * @desc 用于获取注销账号的条件
     * @return int code 状态码，0表示成功
     * @return string msg 提示信息
     * @return array info 返回信息
     * @return array info[0]['list'] 条件数组
     * @return string info[0]['list'][]['title'] 标题
     * @return string info[0]['list'][]['content'] 内容
     * @return string info[0]['list'][]['is_ok'] 是否满足条件 0 否 1 是
     * @return string info[0]['can_cancel'] 是否可以注销账号 0 否 1 是
     */
    public function getCancelCondition(){
    	$rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $checkToken=\App\checkToken($uid,$token);
		
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        $domain=new Domain_Login();
        $res=$domain->getCancelCondition($uid);

        return $res;
    }

    /**
     * 用户注销账号
     * @desc 用于用户注销账号
     * @return int code 状态码,0表示成功
     * @return string msg 返回提示信息
     * @return array info 返回信息
     */
    public function cancelAccount(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $time=\App\checkNull($this->time);
        $sign=\App\checkNull($this->sign);

        $checkToken=\App\checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }

        if(!$time||!$sign){
            $rs['code'] = 1001;
            $rs['msg'] = '参数错误';
            return $rs;
        }

        $now=time();
        if($now-$time>300){
            $rs['code']=1001;
            $rs['msg']='参数错误';
            return $rs;
        }

        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'time'=>$time
        );
        
        $issign=\App\checkSign($checkdata,$sign);
		
        if(!$issign){
            $rs['code']=1001;
            $rs['msg']='签名错误';
            return $rs; 
        }

        $domain=new Domain_Login();
        $res=$domain->cancelAccount($uid);

        if($res==1001){
        	$rs['code']=1001;
            $rs['msg']='相关内容不符合注销账号条件';
            return $rs;
        }
		$rs['info'][0]=$res;
        $rs['msg']='注销成功,手机号、身份证号等信息已解除';
        return $rs;
    }
} 
