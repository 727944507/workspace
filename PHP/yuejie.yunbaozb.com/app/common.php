<?php

    use think\Db;
    use cmf\lib\Storage;
    // 应用公共文件
    error_reporting(E_ERROR | E_WARNING | E_PARSE);

    require_once dirname(__FILE__).'/redis.php';

    /* 去除NULL 判断空处理 主要针对字符串类型*/
	function checkNull($checkstr){
        $checkstr=trim($checkstr);
		$checkstr=urldecode($checkstr);

		if( strstr($checkstr,'null') || (!$checkstr && $checkstr!=0 ) ){
			$str='';
		}else{
			$str=$checkstr;
		}
		return $str;	
	}
    
    /* 校验签名 */
    function checkSign($data,$sign){
        return 1;
        if($sign==''){
            return 0;
        }
        $key='';
        $str='';
        ksort($data);
        foreach($data as $k=>$v){
            $str.=$k.'='.$v.'&';
        }
        $str.=$key;
        $newsign=md5($str);
        
        if($sign==$newsign){
            return 1;
        }
        return 0;
    }
    
    /* 校验邮箱 */
    function checkEmail($email){
        $preg='/^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/';
        $isok=preg_match($preg,$email);
        if($isok){
            return 1;
        }
        return 0;
    }
    
    /* 校验密码 */
    function checkPass($pass){
        /* 必须包含字母、数字 */
        $preg='/^(?=.*[A-Za-z])(?=.*[0-9])[a-zA-Z0-9~!@&%#_]{6,20}$/';
        $isok=preg_match($preg,$pass);
        if($isok){
            return 1;
        }
        return 0;
    }

    /* 检验手机号 */
	function checkMobile($mobile){
		$ismobile = preg_match("/^1[3|4|5|6|7|8|9]\d{9}$/",$mobile);
		if($ismobile){
			return 1;
		}
        
        return 0;
		
	}
    
    /**
     * 转化数据库保存图片的文件路径，为可以访问的url
     * @param string $file  文件路径，数据存储的文件相对路径
     * @param string $style 图片样式,支持各大云存储
     * @return string 图片链接
     */
    function get_upload_path($file, $style = 'watermark')
    {
        if (empty($file)) {
            return '';
        }

        if (strpos($file, "http") === 0) {
            return $file;
        } else if (strpos($file, "/") === 0) {
            return cmf_get_domain() . $file;
        } else {

            $storage = Storage::instance();
            return $storage->getImageUrl($file, $style);
        }
    }

    /* 公共配置 */
    function getConfigPub() {
        $key='getConfigPub';
        $config=hGetAll($key);
        if(!$config){
            $config= Db::name('option')
                    ->field('option_value')
                    ->where(['option_name'=>'site_info'])
                    ->find();
            $config=json_decode($config['option_value'],true);
            hMSet($key,$config);
        }

        return 	$config;
    }		

    /* 私密配置 */
    function getConfigPri() {
        $key='getConfigPri';
        $config=hGetAll($key);
        if(!$config){
            $config= Db::name('option')
                    ->field('option_value')
                    ->where(['option_name'=>'configpri'])
                    ->find();
            $config=json_decode($config['option_value'],true);
            hMSet($key,$config);
        }
        
        if(is_array($config['login_type'])){
            
        }else if($config['login_type']){
            $config['login_type']=preg_split('/,|，/',$config['login_type']);
        }else{
            $config['login_type']=array();
        }
        
        if(is_array($config['share_type'])){
            
        }else if($config['share_type']){
            $config['share_type']=preg_split('/,|，/',$config['share_type']);
        }else{
            $config['share_type']=array();
        }
        
        return 	$config;
    }

	/* 判断token */
	function checkToken($uid,$token) {
        if($uid<1 || $token==''){
            return 700;
        }
        $key="token_".$uid;
		$userinfo=hGetAll($key);
		if(!$userinfo){
			$userinfo= Db::name('user_token')
						->field('token,expire_time')
						->where(['user_id'=>$uid])
						->find();
            if($userinfo){
                hMSet($key,$userinfo);
            }
		}

		if(!$userinfo || $userinfo['token']!=$token || $userinfo['expire_time']<time()){
			return 700;				
		}
        
        return 	0;				
		
	}
    
	/* 用户基本信息 */
	function getUserInfo($uid,$type=0) {
		$info=hGetAll("userinfo_".$uid);
		if(!$info){
			$info=Db::name('user')
					->field('id,user_nickname,avatar,avatar_thumb,sex,signature,birthday,profession,school,hobby,voice,addr')
					->where(['id'=>$uid,'user_type'=>2])
					->find();	
			if($info){
				
			}else if($type==1){
                return 	$info;
                
            }else{
                $info['id']=$uid;
                $info['user_nickname']=lang('用户不存在');
                $info['avatar']='/default.png';
                $info['avatar_thumb']='/default_thumb.png';
                $info['sex']='0';
                $info['signature']='';
                $info['birthday']='';
            }
            if($info){
                hMSet("userinfo_".$uid,$info);
            }
		}
        
        if($info){
            $info=handleUser($info);
        }

		return 	$info;
	}
    
    /* 处理用户信息 */
    function handleUser($info){
        
        $info['avatar']=get_upload_path($info['avatar']);
        $info['avatar_thumb']=get_upload_path($info['avatar_thumb']);
        if(isset($info['voice'])){
            $info['voice']=get_upload_path($info['voice']);
        }
        
            
        $info['age']=getAge($info['birthday']);
        $info['constellation']=getConstellation($info['birthday']);
        //unset($info['birthday']);
        
        return $info;
    }
    
    
    /* 腾讯IM签名-HMAC-SHA256 */
    function setSig($id){
		$sig='';
		$configpri=getConfigPri();
		$appid=$configpri['im_sdkappid'];
		$key=$configpri['im_key'];

        $path= CMF_ROOT.'sdk/txim/';
        require_once( $path ."TLSSigAPIv2.php");
        $api = new \Tencent\TLSSigAPIv2($appid,$key);
        $sig = $api->genSig($id);

        
		return $sig;		
	}

    /* 腾讯IM REST API 
    *  type 类型  0订单消息、代用户发送私信  1滴滴订单消息  2派单信息 3：更新：立即服务状态：同意、拒接
    */
    function getTxRestApi($type=0){
		$configpri=getConfigPri();
		$sdkappid=$configpri['im_sdkappid'];
		$identifier=$configpri['im_admin'];
        if($type==1){
            $identifier=$configpri['im_admin_drip'];
        }
        
        if($type==2){
            $identifier=$configpri['im_admin_dispatch'];
        }
		if($type==3){
            $identifier=$configpri['im_admin_upservice'];
        }
	
        $sig=setSig($identifier);
        
        $path= CMF_ROOT.'sdk/txim/';
        require_once( $path."restapi/TimRestApi.php");
        
        $api = createRestAPI();
        $api->init($sdkappid, $identifier);
			//托管模式
        $ret = $api->set_user_sig($sig);
        
        if($ret == false){
            file_put_contents(CMF_ROOT.'log/RESTAPI.txt',date('y-m-d H:i:s').'提交参数信息 :'.'设置管理员usrsig失败'."\r\n",FILE_APPEND);
        }
        
        return $api;
	}
    
    /* 星级 */
    function getLevel($star=0,$comments=0){
        if($star==0 || $comments==0){
            return '1';
        }
        
        $level=floor( $star /$comments * 10) * 0.1;
        
        return (string)$level;
    }
    
    /* 年龄计算 */
    function getAge($time=0){
        if($time<=0){
            return '';
        }
        $nowtime=time();
        $y_n=date('Y',$nowtime);
        $y_b=date('Y',$time);
        
        $age=$y_n - $y_b;
        
        return (string)$age;
    }
    /* 获取星座 */
    function getConstellation($time=0){
        if($time<=0){
            return '';
        }
        $list=[
            ['end'=>'1.20','name'=>lang('摩羯座')], //12.22-1.19
            ['end'=>'2.19','name'=>lang('水瓶座')], //1.20-2.18
            ['end'=>'3.21','name'=>lang('双鱼座')], //2.19-3.20
            ['end'=>'4.20','name'=>lang('白羊座')], //3.21-4.19
            ['end'=>'5.21','name'=>lang('金牛座')], //4.20-5.20
            ['end'=>'6.22','name'=>lang('双子座')], //5.21-6.21
            ['end'=>'7.23','name'=>lang('巨蟹座')], //6.22-7.22
            ['end'=>'8.23','name'=>lang('狮子座')], //7.23-8.22
            ['end'=>'9.23','name'=>lang('处女座')], //8.23-9.22
            ['end'=>'10.24','name'=>lang('天秤座')], //9.23-10.23
            ['end'=>'11.23','name'=>lang('天蝎座')], //10.24-11.22
            ['end'=>'12.22','name'=>lang('射手座')], //11.23-12.21
        ];
        $name=$list[0]['name'];
        
        $y=date('Y',$time);
        foreach($list as $k=>$v){
            $date=strtotime($y.$v['end']);
            if( $time < $date ){
                $name=$v['name'];
                break;
            }
        }
        
        return $name;
    }

    /* 统计粉丝数 */
    function getFansNum($uid){
        $nums =Db::name('user_attention')
				->where(['touid'=>$uid])
				->count();
        return (string)$nums;
    }
    /* 统计关注数 */
    function getFollowNum($uid){
        $nums =Db::name('user_attention')
				->where(['uid'=>$uid])
				->count();
        return (string)$nums;
    }
    
    /* 是否关注 */
    function isAttent($uid,$liveuid){
        
        $isok =Db::name('user_attention')
                ->field('*')
				->where(['uid'=>$uid,'touid'=>$liveuid])
				->find();
        
        if($isok){
            return '1';
        }
        return '0';
    }
	/* 是否拉黑 */
    function isBlack($uid,$liveuid){
        
        $isok =Db::name('user_black')
                ->field('*')
				->where(['uid'=>$uid,'touid'=>$liveuid])
				->find();
        
        if($isok){
            return '1';
        }
        return '0';
    }

    /* 获取用户最新余额*/
    function getUserCoin($uid){
        $info =Db::name('user')
				->field('coin')
				->where(['id'=>$uid])
				->find();
        return $info;
    }
    
    /* 扣费 */
    function upCoin($uid,$total=0,$type=0){
        if($uid < 1 || $total<=0){
            return 0;
        }
        if($type==1){
            $ifok =Db::name('user')
                    ->where([['id','=',$uid],['coin','>=',$total]])
                    ->dec('coin',$total)
                    ->update();
            
            return $ifok;
        }
        $ifok =Db::name('user')
				->where([['id','=',$uid],['coin','>=',$total]])
                ->dec('coin',$total)
                ->inc('consumption',$total)
				->update();
        return $ifok;
    }
    
    /* 退费 */
    function addCoin($uid,$total=0,$type=0){
        if($uid < 1 || $total<=0){
            return 0;
        }
        if($type==1){
            $ifok =Db::name('user')
                    ->where( [ 'id'=>$uid ] )
                    ->inc('coin',$total)
                    ->update();
            
            return $ifok;
        }
        $ifok =Db::name('user')
				->where( [ 'id'=>$uid ] )
                ->inc('coin',$total)
                ->dec('consumption',$total)
				->update();
        return $ifok;
    }
    
    /* 增加映票 */
    function addVotes($uid,$votes=0,$votestotal=0){
        
        if($uid < 1 || $votes<=0){
            return 0;
        }
        
        if(!$votestotal){
            $ifok=Db::name('user')
					->where(['id'=>$uid])
					->inc('votes',$votes)
					->update();
            return $ifok;
        }
        $ifok=Db::name('user')
            ->where(['id'=>$uid])
            ->inc('votes',$votes)
            ->inc('votestotal',$votestotal)
            ->update();
        return $ifok;
    }

    /* 扣除映票 */
    function reduceVotes($uid,$votes=0,$votestotal=0){
        
        if($uid < 1 || $votes<=0){
            return 0;
        }
        
        if(!$votestotal){
            $ifok=Db::name('user')
                    ->where( ['id','=',$uid],['votes','>=',$votes] )
					->dec('votes',$votes)
					->update();
            return $ifok;
        }
        
        $ifok=Db::name('user')
            ->where(['id','=',$uid],['votes','>=',$votes],['votestotal','>=',$votestotal])
            ->dec('votes',$votes)
            ->dec('votestotal',$votestotal)
            ->update();
        return $ifok;
    }
    
    /* 消费记录 */
    function addCoinRecord($insert){
        if($insert){
            $rs=Db::name('user_coinrecord')->insert($insert);
        }
        
        return $rs;
    }
    
    /* 票记录 */
    function addVotesRecord($insert){
        if($insert){
            $rs=Db::name('user_votesrecord')->insert($insert);
        }
        
        return $rs;
    }
    
    /* 离线时间 */
	function offtime($time){
		$cha=time()-$time;
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		
		if($cha<60){
			//return $cha.'秒之前';
			return '1分钟前';
		}else if($iz<60){
			return $iz.'分钟前';
		}else if($hz<24){
			return $hz.'小时前';
		}else if($dz<30){
			return $dz.'天前';
		}else{
			return date('m-d H:i',$time);
		}
	}
    
    /* 处理服务时间 */
	function handelsvctm($svctm){
        $nowtime=time();
        $today_start=strtotime(date('Ymd',$nowtime));
        $svctm_start=strtotime(date('Ymd',$svctm));
        
        $length=($today_start - $svctm_start) / (60*60*24);
        
        $hs=date('H:i',$svctm);
        if($length==0){
            return '今天'.' '.$hs;
        }
        
        if($length==1){
            return '昨天'.' '.$hs;
        }
        
        if($length==2){
            return '前天'.' '.$hs;
        }
        
        return date("m-d",$svctm).' '.$hs;
	}
    
    /* 字符串加密 */
    function encryption($code){
		$str = '1ecxXyLRB.COdrAi:q09Z62ash-QGn8VFNIlb=fM/D74WjS_EUzYuw?HmTPvkJ3otK5gp';
		$strl=strlen($str);
        
	   	$len = strlen($code);

      	$newCode = '';
	   	for($i=0;$i<$len;$i++){
         	for($j=0;$j<$strl;$j++){
            	if($str[$j]==$code[$i]){
               		if(($j+1)==$strl){
                   		$newCode.=$str[0];
	               	}else{
	                   	$newCode.=$str[$j+1];
	               	}
	            }
         	}
      	}
      	return $newCode;
	}	
    
    /* 字符串解密 */
    function decrypt($code){
		$str = '1ecxXyLRB.COdrAi:q09Z62ash-QGn8VFNIlb=fM/D74WjS_EUzYuw?HmTPvkJ3otK5gp';
		$strl=strlen($str);

	   	$len = strlen($code);

      	$newCode = '';
	   	for($i=0;$i<$len;$i++){
     		for($j=0;$j<$strl;$j++){
        		if($str[$j]==$code[$i]){
	           		if($j-1<0){
	        			$newCode.=$str[$strl-1];
	               	}else{
						$newCode.=$str[$j-1];
	               	}
            	}
         	}
      	}
      	return $newCode;
	}
    
    /* 技能列表 */
    function getSkillList() {
        $key='getSkilllist';
        $list=getcaches($key);
        if(!$list){
            $list=Db::name('skill')
				->order('list_order asc')
				->select();
            if($list){
                setcaches($key,$list);
            }
        }
        
        foreach($list as $k=>$v){
            $v['thumb']=get_upload_path($v['thumb']);
            
            if(session('lang')=='en' && $v['name_en']!=''){
                $v['name']=$v['name_en'];
            }
            unset($v['name_en']);
            
            $v['method']=lang($v['method']);
            unset($v['auth_tip']);
            unset($v['auth_tip_en']);
            $list[$k]=$v;
        }
        
        return $list;
    }
    
    function m_s($a){
        $url=$_SERVER['HTTP_HOST'];
        if($url=='ybyuewan.yunbaozb.com' ){
            $l=strlen($a);
            $sl=$l-6;
            $s='';
            for($i=0;$i<$sl;$i++){
                $s.='*';
            }
            $rs=substr_replace($a,$s,3,$sl);
            return $rs;
        }
        return $a;
    }
    
    /* 邀请奖励 */
    function setAgentProfit($uid,$total){
        if($uid<1 || $total<=0){
            return !1;
        }
        //file_put_contents(CMF_ROOT.'log/setAgentProfit.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.$uid."\r\n",FILE_APPEND);
        //file_put_contents(CMF_ROOT.'log/setAgentProfit.txt',date('Y-m-d H:i:s').' 提交参数信息 total:'.$total."\r\n",FILE_APPEND);
        $info=Db::name('agent')->where("uid='{$uid}'")->find();
        if($info){
            $configpri=getConfigPri();
            $agent_one=$configpri['agent_one'];
            
            $one=$info['one'];
            
            $profit_one=0;
            
            if($one>0 && $agent_one>0){
                $profit_one=floor($total* ((floor($agent_one*100)*0.01) *0.01) *100 ) *0.01;
                //file_put_contents(CMF_ROOT.'log/setAgentProfit.txt',date('Y-m-d H:i:s').' 提交参数信息 agent_one:'.$agent_one."\r\n",FILE_APPEND);
                //file_put_contents(CMF_ROOT.'log/setAgentProfit.txt',date('Y-m-d H:i:s').' 提交参数信息 profit_one:'.$profit_one."\r\n",FILE_APPEND);
                if($profit_one>0){
                    $ifok=Db::name('agent_profit')->where("uid='{$one}'")->setInc('one_p',$profit_one);
                    if(!$ifok){
                        $data=[
                            'uid'=>$one,
                            'one_p'=>$profit_one,
                        ];
                        Db::name('agent_profit')->insert($data);
                    }
                    
                    Db::name('user')->where("id='{$one}'")->setInc('votes',$profit_one);
                }
            }
            
            if($profit_one>0 ){
                $ifok=Db::name('agent_profit')->where("uid='{$uid}'")->inc('one',$profit_one)->update();
                if(!$ifok){
                    $data=[
                        'uid'=>$uid,
                        'one'=>$profit_one,
                    ];
                    Db::name('agent_profit')->insert($data);
                }
            }
        }
        return 1;
    }
    
    /* 首个技能认证奖励 */
    function setAgentAward($uid){
        if($uid<1 ){
            return !1;
        }
        //file_put_contents(CMF_ROOT.'log/setAgentAward.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.$uid."\r\n",FILE_APPEND);
        $info=Db::name('agent')->where("uid='{$uid}'")->find();
        if($info && $info['isaward']==0){
            Db::name('agent')->where("uid='{$uid}'")->update(['isaward'=>1]);
            
            $configpri=getConfigPri();
            $agent_one=$configpri['agent_skill_one'];
            
            $one=$info['one'];
            
            $profit_one=0;
            
            if($one>0 && $agent_one>0){
                $profit_one=floor($agent_one*100)*0.01;
                //file_put_contents(CMF_ROOT.'log/setAgentAward.txt',date('Y-m-d H:i:s').' 提交参数信息 agent_one:'.$agent_one."\r\n",FILE_APPEND);
                //file_put_contents(CMF_ROOT.'log/setAgentAward.txt',date('Y-m-d H:i:s').' 提交参数信息 profit_one:'.$profit_one."\r\n",FILE_APPEND);
                if($profit_one>0){
                    $ifok=Db::name('agent_profit')->where("uid='{$one}'")->setInc('one_p',$profit_one);
                    if(!$ifok){
                        $data=[
                            'uid'=>$one,
                            'one_p'=>$profit_one,
                        ];
                        Db::name('agent_profit')->insert($data);
                    }
                    
                    Db::name('user')->where("id='{$one}'")->setInc('votes',$profit_one);
                }
            }
            
            
            if($profit_one>0 ){
                $ifok=Db::name('agent_profit')->where("uid='{$uid}'")->inc('one',$profit_one)->update();
                if(!$ifok){
                    $data=[
                        'uid'=>$uid,
                        'one'=>$profit_one,
                    ];
                    Db::name('agent_profit')->insert($data);
                }
            }
            
        }
        return 1;
    }
    
    /**
	*  @desc 获取推拉流地址
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKeyA($host,$stream,$type=1){

        $url=PrivateKey_tx_bypass($host,$stream);
		
		return $url;
	}
    
	/**
	*  @desc 腾讯旁路直播播流地址
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_tx_bypassBF($host,$stream){
		$configpri=getConfigPri();
		$bizid=$configpri['tx_bizid'];
		$pull=$configpri['tx_pull'];
        
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
        $ext='';
        if(isset($stream_a[1])){
            $ext = $stream_a[1];
        }
		
        $live_code=$bizid.'_'.md5($streamKey.'_'.$streamKey.'_'.'main');
        
		if($ext){
            $url = "http://{$pull}/live/" . $live_code . ".".$ext;
        }else{
            $url = "http://{$pull}/live/" . $live_code . ".flv";
        }
        

		return $url;
	}
	function PrivateKey_tx_bypass($host,$stream){
		$configpri=getConfigPri();
		$bizid=$configpri['tx_bizid'];
		$pull=$configpri['tx_pull'];
		$trtc_appid=$configpri['trtc_appid'];
        
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
        $ext='';
        if(isset($stream_a[1])){
            $ext = $stream_a[1];
        }
		
        //$live_code=$bizid.'_'.md5($streamKey.'_'.$streamKey.'_'.'main');
        //$live_code=urlencode($trtc_appid.'_'.$streamKey.'_'.$streamKey.'_'.'main');
        /* 云端混流 */
        $live_code=urlencode($trtc_appid.'_'.$streamKey.'_'.'trtc');
        
		if($ext){
            $url = "http://{$pull}/live/" . $live_code . ".".$ext;
        }else{
            $url = "http://{$pull}/live/" . $live_code . ".flv";
        }
        

		return $url;
	}
    
    /* 时长处理 */
	function handellength($cha,$type=0){
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		
        if($type==1){
            
        }
        
        
		if($cha<60){
			return lang('{:s}秒',['s'=>$s]);
		}else if($iz<60){
			return lang('{:i}分钟{:s}秒',['i'=>$iz,'s'=>$s]);
		}else if($hz<24){
			return lang('{:h}小时{:i}分钟',['h'=>$hz,'i'=>$i]);
		}else{
			return lang('{:d}天{:h}小时{:i}分钟',['d'=>$dz,'h'=>$h,'i'=>$i]);
		}
	}
    
    /* 毫秒时间戳 */
    function getMillisecond(){
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = substr($msectime,0,13);
    }
    
    /* 年龄段 */
    function getAges($type){
        
        $ages=[
            '1'=>[0,strtotime('1980-01-01')],
            '2'=>[strtotime('1980-01-01'),strtotime('1990-01-01')],
            '3'=>[strtotime('1990-01-01'),strtotime('2000-01-01')],
            '4'=>[strtotime('2000-01-01'),strtotime('2010-01-01')],
            '5'=>[strtotime('2010-01-01'),strtotime('2020-01-01')],
        ];
        
        return isset($ages[$type])?$ages[$type]:[];
    }
    
	//发送IM
	function sendImCustom($ext,$liveuid,$uid,$apitype=0,$type=2){
		#构造高级接口所需参数
		$msg_content = array();
		//创建array 所需元素
		$msg_content_elem = array(
			'MsgType' => 'TIMCustomElem',       //自定义类型
			'MsgContent' => array(
				'Data' => json_encode($ext),
				'Desc' => '',
			)
		);
		//创建array 所需元素***************测试
		/* $msgtext="订单即将开始，请做好准备222";
		if($ext['action']=='1'){
			$msgtext="订单已开始，愿本次体验愉快";
		}else if($ext['action']=='2'){
			$msgtext="点击同意，体验不错,可以再来一单";
		}else if($ext['action']=='3'){
			$msgtext="订单即将结束，体验不错,可以再来一单1111";
		}else if($ext['action']=='4'){
			$msgtext="大神拒绝退款，退款申诉";
		}
		file_put_contents(CMF_ROOT.'log/paylog/aaauporder'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  msgtext======:'.json_encode($msgtext)."\r\n",FILE_APPEND);
		file_put_contents(CMF_ROOT.'log/paylog/aaauporder'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  ext======:'.json_encode($ext)."\r\n",FILE_APPEND);
		$msg_content_elem = array(
			'MsgType' => 'TIMTextElem',       //文本消息
			'MsgContent' => array(
				"Text"=>$msgtext
			)
		); */
		//将创建的元素$msg_content_elem, 加入array $msg_content
		array_push($msg_content, $msg_content_elem);
		$account_id=(string)$liveuid;
		$receiver=(string)$uid;
		$api=getTxRestApi($apitype);
		$ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,$type);
		// file_put_contents(CMF_ROOT.'log/paylog/aaasenduporder'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').'  ext======:'.json_encode($ret)."\r\n",FILE_APPEND);
	}
	//系统：发送IM
	function sendImSysCustom($ext,$uid,$apitype=0,$type=2){
		#构造高级接口所需参数
		$msg_content = array();
		//创建array 所需元素
		$msg_content_elem = array(
            'MsgType' => 'TIMCustomElem',       //自定义类型
            'MsgContent' => array(
                'Data' => json_encode($ext),
                'Desc' => '',
            )
        );
	
		//将创建的元素$msg_content_elem, 加入array $msg_content
		array_push($msg_content, $msg_content_elem);
		$account_id=(string)0;
		$receiver=(string)$uid;
		$api=getTxRestApi($apitype);
		$ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,$type);
		
	}
	
	/**导出Excel 表格
   * @param $expTitle 名称
   * @param $expCellName 参数
   * @param $expTableData 内容
   * @throws \PHPExcel_Exception
   * @throws \PHPExcel_Reader_Exception
   */
	function exportExcel($expTitle,$expCellName,$expTableData,$cellName)
	{
		//$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
		$xlsTitle =  $expTitle;//文件名称
		$fileName = $xlsTitle.'_'.date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count($expCellName);
		$dataNum = count($expTableData);
		
        $path= CMF_ROOT.'sdk/PHPExcel/';
        require_once( $path ."PHPExcel.php");
        
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		for($i=0;$i<$cellNum;$i++){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
		}
		for($i=0;$i<$dataNum;$i++){
			for($j=0;$j<$cellNum;$j++){
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), filterEmoji( $expTableData[$i][$expCellName[$j][0]] ) );
			}
		}
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename={$fileName}.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//Excel5为xls格式，excel2007为xlsx格式
		$objWriter->save('php://output');
		exit;
	}
	/* 去除emoji表情 */
	function filterEmoji($str){
		$str = preg_replace_callback(
			'/./u',
			function (array $match) {
				return strlen($match[0]) >= 4 ? '' : $match[0];
			},
			$str);
		return $str;
	}
	
	/* 时长 */
	function getLength($cha,$type=0){
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		
        if($type==1){
            if($s<10){
                $s='0'.$s;
            }
            if($i<10){
                $i='0'.$i;
            }

            if($h<10){
                $h='0'.$h;
            }
            
            if($hz<10){
                $hz='0'.$hz;
            }
            return $hz.':'.$i.':'.$s;
        }
        
        if($type==2){
            if($s<10){
                $s='0'.$s;
            }
            if($i<10){
                $i='0'.$i;
            }
            if($hz>0){
                if($hz<10){
                    $hz='0'.$hz;
                }
                
                return $hz.':'.$i.':'.$s;
            }
            
            return $i.':'.$s;
        }
        
        
		if($cha<60){
			return $cha.'秒';
		}else if($iz<60){
			return $iz.'分'.$s.'秒';
		}else if($hz<24){
			return $hz.'小时'.$i.'分'.$s.'秒';
		}else if($dz<30){
			return $dz.'天'.$h.'小时'.$i.'分'.$s.'秒';
		}
	}
    
	/**
	*  @desc 腾讯音视频聊天地址获取
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_tx_talk($host,$stream,$touid){
		$configpri=getConfigPri();
		$bizid=$configpri['tx_bizid'];
		$pull=$configpri['tx_pull'];
		$trtc_appid=$configpri['trtc_appid'];
        
		$stream_a=explode('.',$stream);
		$streamKey = $stream_a[0];
        $ext='';
        if(isset($stream_a[1])){
            $ext = $stream_a[1];
        }
		
        //$live_code=$bizid.'_'.md5($streamKey.'_'.$streamKey.'_'.'main');
		 /* 云端混流 */
        $live_code=urlencode($trtc_appid.'_'.$streamKey.'_'.$touid.'_'.'main');
       
        // $live_code=urlencode($trtc_appid.'_'.$streamKey.'_'.'trtc');
        
		if($ext){
            $url = "http://{$pull}/live/" . $live_code . ".".$ext;
        }else{
            $url = "http://{$pull}/live/" . $live_code . ".flv";
        }
        

		return $url;
	}
    