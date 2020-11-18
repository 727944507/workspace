<?php
namespace App;

    /* curl get请求 */
    function curl_get($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // 从证书中检查SSL加密算法是否存在
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	} 
    /* curl POST 请求 */
	function curl_post($url,$curlPost=''){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, false);
		curl_setopt($curl, CURLOPT_POST, true);
        if($curlPost){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        }
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // 从证书中检查SSL加密算法是否存在
		
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
	}
    /* 去除NULL 判断空处理 主要针对字符串类型*/
	function checkNull($checkstr){
        $checkstr=trim($checkstr);
		$checkstr=urldecode($checkstr);
		$checkstr=html_entity_decode($checkstr);

		if( strstr($checkstr,'null') || (!$checkstr && $checkstr!=0 ) ){
			$str='';
		}else{
			$str=$checkstr;
		}
		return $str;	
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
    
    /* 校验签名 */
    function checkSign($data,$sign){
        if($sign==''){
            return 0;
        }
        $key=\PhalApi\DI()->config->get('app.sign_key');
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
    
     /* 检验手机号 */
	function checkMobile($mobile){
		$ismobile = preg_match("/^1[3|4|5|6|7|8|9]\d{9}$/",$mobile);
		if($ismobile){
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

    /* 检测用户是否存在 */
    function checkUser($where){
        if($where==''){
            return 0;
        }

        $isexist=\PhalApi\DI()->notorm->user->where($where)->fetchOne();
        if($isexist){
            return 1;
        }
        
        return 0;
    }
    
    /* 密码加密 */
	function setPass($pass){
		$authcode='BXTFbRri1a7ncqRrw7';
		$pass="###".md5(md5($authcode.$pass));
		return $pass;
	}
    
    /* 随机数 */
	function random($length = 6 , $numeric = 1) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}
    
    /* 发送验证码 */
    function sendCode($account,$code){
        $rs = array('code' => 1001, 'msg' => \PhalApi\T('发送失败'));
		$config = getConfigPri();
        
        if(!$config['sendcode_switch']){
            $rs['code']=667;
			$rs['msg']='123456';
            return $rs;
        }
		if($config['code_switch']=='1'){//阿里云
			$res=sendCodeByCCP($account,$code);
		}else{
			$res=sendCodeByCCP_ronglianyun($account,$code);//容联云
		}
    
        //$res=sendEmailCode($account,$code);
        
        return $res;
    }
    /* 公共配置 */
	function getConfigPub() {
		$key='getConfigPub';
		$config=hGetAll($key);
		if(!$config){
			$config= \PhalApi\DI()->notorm->option
					->select('option_value')
					->where("option_name='site_info'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
			hMSet($key,$config);
		}
        
        if(\PhalApi\DI()->lang=='en'){
            $config['name_coin']=$config['name_coin_en'];
        }

		return 	$config;
	}		
	
	/* 私密配置 */
	function getConfigPri() {
		$key='getConfigPri';
		$config=hGetAll($key);
		if(!$config){
			$config=\PhalApi\DI()->notorm->option
					->select('option_value')
					->where("option_name='configpri'")
					->fetchOne();
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

	/* 邮箱配置 */
	function getEmail() {
		$key='getEmail';
		$config=hGetAll($key);
		if(!$config){
			$config=\PhalApi\DI()->notorm->option
					->select('option_value')
					->where("option_name='smtp_setting'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
			if($config){
                hMSet($key,$config);
            }
		}


		return 	$config;
	}
    
    /* 邮箱模板 */
	function getEmailTemp() {
		$key='getEmailTemp';
		$config=hGetAll($key);
		if(!$config){
			$config=\PhalApi\DI()->notorm->option
					->select('option_value')
					->where("option_name='email_template_verification_code'")
					->fetchOne();
            $config=json_decode($config['option_value'],true);
            if($config){
                hMSet($key,$config);
            }
			
		}
        if(!$config){
            $config=[
                'subject'=>\PhalApi\T('验证码'),
                'template'=>\PhalApi\T('您的验证码是：{$code}。请不要把验证码泄露给其他人'),
            ];
        }

		return 	$config;
	}
    
 	/**
	 * 返回带协议的域名
	 */
	function get_host(){
		$config=getConfigPub();
		return $config['site_url'];
	}
	
	/**
	 * 转化数据库保存的文件路径，为可以访问的url
	 */
	function get_upload_path($file){
        if($file==''){
            return '';
        }
		if(strpos($file,"http")===0){
            $filepath= $file;
		}else if(strpos($file,"/")===0){
			$filepath= get_host().$file;
		}else{
            $uptype=\PhalApi\DI()->config->get('app.uptype');
            if($uptype==1){
                 /* 七牛上传 */
                $space_host= \PhalApi\DI()->config->get('app.Qiniu.space_host');
            }else{
                /* 本地 上传 */
                $space_host= get_host().'/upload';
            }
			$filepath=$space_host."/".$file;
		}

        return html_entity_decode($filepath);
	}
    
	/* 判断token */
	function checkToken($uid,$token) {
        /*if($uid<1 || $token==''){
            return 700;
        }
        
        $key="token_".$uid;
		$userinfo=hGetAll($key);
		if(!$userinfo){
			$userinfo=\PhalApi\DI()->notorm->user_token
						->select('token,expire_time')
						->where('user_id = ? ', $uid)
						->fetchOne();
            if($userinfo){
                hMSet($key,$userinfo);
            }
		}

		if(!$userinfo || $userinfo['token']!=$token || $userinfo['expire_time']<time()){
			return 700;
		}
		
		/* 是否禁用、拉黑 */
        /*$info=\PhalApi\DI()->notorm->user
					->select('user_status')
					->where('id=? and user_type="2"',$uid)
					->fetchOne();	
        if(!$info || $info['user_status']==0){
        	
            return 700;	
        }*/
        return 	0;
		
	}
	
	/**
	 * 根据经纬度和半径计算出范围
	 * @param string $lat 纬度
	 * @param String $lng 经度
	 * @param float $radius 半径
	 * @return Array 范围数组
	 */
	
	function calcScope($lat, $lng, $radius) {
	
	  $degree = (24901*1609)/360.0;
	  $dpmLat = 1/$degree;
	  $radiusLat = $dpmLat*$radius;
	  $minLat = $lat - $radiusLat;    // 最小纬度
	  $maxLat = $lat + $radiusLat;    // 最大纬度
	 
	  $mpdLng = $degree*cos($lat * (PI/180));
	  $dpmLng = 1 / $mpdLng;
	  $radiusLng = $dpmLng*$radius;
	  $minLng = $lng - $radiusLng;   // 最小经度
	  $maxLng = $lng + $radiusLng;   // 最大经度
	
	  /** 返回范围数组 */
	  $scope = array(
	    'minLat'  => $minLat,
	    'maxLat'  => $maxLat,
	    'minLng'  => $minLng,
	    'maxLng'  => $maxLng
	    );
	  return $scope;
	}
	
	function calcDistance($lat1, $lng1, $lat2, $lng2) {
	
	  /** 转换数据类型为 double */
	  $lat1 = doubleval($lat1);
	  $lng1 = doubleval($lng1);
	  $lat2 = doubleval($lat2);
	  $lng2 = doubleval($lng2);
	
	  /** 以下算法是 Google 出来的，与大多数经纬度计算工具结果一致 */
	  $theta = $lng1 - $lng2;
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  $dist = acos($dist);
	  $dist = rad2deg($dist);
	  $miles = $dist * 60 * 1.1515;
	  return ($miles * 1.609344);
	}
    
    /* 用户基本信息 */
	function getUserInfo($uid,$type=0) {
		// $info=hGetAll("userinfo_".$uid);
		if(!$info){
			$info=\PhalApi\DI()->notorm->user
					->select('id,user_nickname,avatar,avatar_thumb,sex,signature,birthday,profession,school,hobby,voice,voice_l,addr,stars,star_nums,user_status,orders,onlineStatus,lat,lng')
					->where('id=? and user_type="2"',$uid)
					->fetchOne();	
			if($info){

			}else if($type==1){
                return 	$info;
            }else{
                $info['id']=$uid;
                $info['user_nickname']=\PhalApi\T('用户不存在');
                $info['avatar']='/default.png';
                $info['avatar_thumb']='/default_thumb.png';
                $info['sex']='0';
                $info['signature']='';
                $info['birthday']='0';
                $info['profession']='';
                $info['school']='';
                $info['hobby']='';
                $info['voice']='';
                $info['voice_l']='0';
                $info['addr']='';
                $info['stars']='0';
                $info['star_nums']='0';
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
        
        $age=getAge($info['birthday']);  
		if(!$age){
			$age=0;
		}
        $info['age']=(string)$age;
        $info['constellation']=getConstellation($info['birthday']);
        
        $info['star']=getLevel($info['stars'],$info['star_nums']);
        
        if($info['hobby']){
            $hobby='';
            $hobby_a=preg_split('/,|，/',$info['hobby']);
            $hobbylist=getHobby();
            foreach($hobby_a as $k=>$v){
                foreach($hobbylist as $k2=>$v2){
                    if($v==$v2['id']){
                        $hobby.=$v2['name'].';';
                    }
                }
            }
            $info['hobby']=$hobby;
        }
        
        //unset($info['birthday']);
        unset($info['stars']);
        unset($info['star_nums']);
        
        return $info;
    }
    
    /* 兴趣爱好 */
    function getHobby() {
        $key='getHobby';
        $list=getcaches($key);
        if(!$list){
            $list=\PhalApi\DI()->notorm->hobby
                ->select('*')
                ->order('list_order asc')
                ->fetchAll($data);
            if($list){
                setcaches($key,$list);
            }
        }
        
        foreach($list as $k=>$v){
            if(\PhalApi\DI()->lang=='en' && $v['name_en']!=''){
                $v['name']=$v['name_en'];
            }
            unset($v['name_en']);
            $list[$k]=$v;
        }
        
        return $list;
    }
        
    /* 腾讯IM签名-HMAC-SHA256 */
    function setSig($id){
		$sig='';
		$configpri=getConfigPri();
		$appid=$configpri['im_sdkappid'];
		$key=$configpri['im_key'];

        $path= API_ROOT.'/../sdk/txim/';
        require_once( $path ."TLSSigAPIv2.php");
        $api = new \Tencent\TLSSigAPIv2($appid,$key);
        $sig = $api->genSig($id);

        
		return $sig;		
	}

    /* 腾讯IM REST API 
    *  type 类型  0订单消息、代用户发送私信  1滴滴订单消息  2派单信息，3：更新：立即服务状态：同意、拒接
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
        
        $path= API_ROOT.'/../sdk/txim/';
        require_once( $path."restapi/TimRestApi.php");
        
        $api = createRestAPI();
        $api->init($sdkappid, $identifier);
			//托管模式
        $ret = $api->set_user_sig($sig);
        
        if($ret == false){
            file_put_contents(API_ROOT.'/../log/RESTAPI.txt',date('y-m-d H:i:s').'提交参数信息 :'.'设置管理员usrsig失败'."\r\n",FILE_APPEND);
        }
        
        return $api;
	}
    
    /* 星级 */
    function getLevel($star=0,$comments=0){
        if(!$star || !$comments){
            return '0.0';
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
            ['end'=>'01-20','name'=>\PhalApi\T('摩羯座')], //12.22-1.19
            ['end'=>'02-19','name'=>\PhalApi\T('水瓶座')], //1.20-2.18
            ['end'=>'03-21','name'=>\PhalApi\T('双鱼座')], //2.19-3.20
            ['end'=>'04-20','name'=>\PhalApi\T('白羊座')], //3.21-4.19
            ['end'=>'05-21','name'=>\PhalApi\T('金牛座')], //4.20-5.20
            ['end'=>'06-22','name'=>\PhalApi\T('双子座')], //5.21-6.21
            ['end'=>'07-23','name'=>\PhalApi\T('巨蟹座')], //6.22-7.22
            ['end'=>'08-23','name'=>\PhalApi\T('狮子座')], //7.23-8.22
            ['end'=>'09-23','name'=>\PhalApi\T('处女座')], //8.23-9.22
            ['end'=>'10-24','name'=>\PhalApi\T('天秤座')], //9.23-10.23
            ['end'=>'11-23','name'=>\PhalApi\T('天蝎座')], //10.24-11.22
            ['end'=>'12-22','name'=>\PhalApi\T('射手座')], //11.23-12.21
        ];
        $name=$list[0]['name'];
        
        $y=date('Y',$time);
        foreach($list as $k=>$v){
            $day=$y.'-'.$v['end'];
            $date=strtotime($day);
            if( $time < $date ){
                $name=$v['name'];
                break;
            }
        }
        
        return $name;
    }
    
    /* 统计粉丝数 */
    function getFansNum($uid){
        $nums =\PhalApi\DI()->notorm->user_attention
				->where('touid = ?', $uid)
				->count();
        return (string)$nums;
    }
    /* 统计关注数 */
    function getFollowNum($uid){
        $nums =\PhalApi\DI()->notorm->user_attention
				->where('uid = ?', $uid)
				->count();
        return (string)$nums;
    }
    
    /* 是否关注 */
    function isAttent($uid,$liveuid){
        if($uid<1 || $liveuid<1 || $uid==$liveuid){
            return '0';
        }
        
        $isok =\PhalApi\DI()->notorm->user_attention
                ->select('*')
				->where('uid = ? and touid=?', $uid,$liveuid)
				->fetchOne();
        
        if($isok){
            return '1';
        }
        return '0';
    }
    
    /* 获取用户最新余额*/
    function getUserCoin($uid){
        $info =\PhalApi\DI()->notorm->user
				->select('coin')
				->where('id = ?', $uid)
				->fetchOne();
        return $info;
    }
    
    /* 扣费 */
    function upCoin($uid,$total=0,$type=0){
        if($uid < 1 || $total<=0){
            return 0;
        }
        if($type==1){
            $ifok =\PhalApi\DI()->notorm->user
                    ->where('id = ? and coin >=?', $uid,$total)
                    ->update(array('coin' => new \NotORM_Literal("coin - {$total}") ) );
            
            return $ifok;
        }
        $ifok =\PhalApi\DI()->notorm->user
				->where('id = ? and coin >=?', $uid,$total)
				->update(array('coin' => new \NotORM_Literal("coin - {$total}"),'consumption' => new \NotORM_Literal("consumption + {$total}") ) );
        return $ifok;
    }
    
    /* 退费 */
    function addCoin($uid,$total=0,$type=0){
        if($uid < 1 || $total<=0){
            return 0;
        }
        if($type==1){
            $ifok =\PhalApi\DI()->notorm->user
                    ->where('id = ? ', $uid)
                    ->update(array('coin' => new \NotORM_Literal("coin + {$total}") ) );
            
            return $ifok;
        }
        $ifok =\PhalApi\DI()->notorm->user
				->where('id = ? ', $uid)
				->update(array('coin' => new \NotORM_Literal("coin + {$total}"),'consumption' => new \NotORM_Literal("consumption - {$total}") ) );
        return $ifok;
    }
    
    /* 增加映票 */
    function addVotes($uid,$votes=0,$votestotal=0){
        
        if($uid < 1 || $votes<=0){
            return 0;
        }
        
        if(!$votestotal){
            $ifok=\PhalApi\DI()->notorm->user
					->where('id = ?', $uid)
					->update( array('votes' => new \NotORM_Literal("votes + {$votes}") ));
            return $ifok;
        }
        
        $ifok=\PhalApi\DI()->notorm->user
					->where('id = ?', $uid)
					->update( array('votes' => new \NotORM_Literal("votes + {$votes}"),'votestotal' => new \NotORM_Literal("votestotal + {$votestotal}") ));
        return $ifok;
    }

	/* 增加：礼物收益映票 */
    function addGiftVotes($uid,$votes=0,$votestotal=0){
        
        if($uid < 1 || $votes<=0){
            return 0;
        }
        
        if(!$votestotal){
            $ifok=\PhalApi\DI()->notorm->user
					->where('id = ?', $uid)
					->update( array('votes_gift' => new \NotORM_Literal("votes_gift + {$votes}") ));
            return $ifok;
        }
        
        $ifok=\PhalApi\DI()->notorm->user
					->where('id = ?', $uid)
					->update( array('votes_gift' => new \NotORM_Literal("votes_gift + {$votes}"),'votes_gifttotal' => new \NotORM_Literal("votes_gifttotal + {$votestotal}") ));
        return $ifok;
    }
    /* 消费记录 */
    function addCoinRecord($insert){
        if($insert){
            $rs=\PhalApi\DI()->notorm->user_coinrecord->insert($insert);
        }
        
        return $rs;
    }
    
    /* 票记录 */
    function addVotesRecord($insert){
        if($insert){
            $rs=\PhalApi\DI()->notorm->user_votesrecord->insert($insert);
        }
        
        return $rs;
    }
	/* 时间差计算 */
	function datetime($time){
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
			return $cha.'秒前';
		}else if($iz<60){
			return $iz.'分钟前';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟前';
		}else if($dz<30){
			return $dz.'天前';
		}else{
			return date("Y-m-d",$time);
		}
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
			return \PhalApi\T('1分钟前');
		}else if($iz<60){
			return \PhalApi\T('{n}分钟前',['n'=>$iz]);
		}else if($hz<24){
			return \PhalApi\T('{n}小时前',['n'=>$hz]);
		}else if($dz<30){
			return \PhalApi\T('{n}天前',['n'=>$dz]);
		}else{
			return date('m-d H:i',$time);
		}
	}

    /* 处理服务时间 用于存储 */
	function treatsvctm($type,$svctm){
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $nowtime=time();
        
        $today=date('Y-m-d',$nowtime);
        
        $svctm=strtotime($svctm);
        $h=date("H",$svctm);
        $i=date("i",$svctm);

        $i_allow=['00','15','30','45'];
        if(!in_array($i,$i_allow)){
            $rs['code']=1003;
			$rs['msg']=\PhalApi\T('请选择正确的时间');
			return $rs;
        }
        
        if($type==2){
            /* 后天 */
            $aftertomorrow=date('Y-m-d',strtotime("{$today} + 2 day"));
            $svctm=strtotime($aftertomorrow.' '.$h.':'.$i);
        }elseif($type==1){
            /* 明天 */
            $tomorrow=date('Y-m-d',strtotime("{$today} + 1 day"));
            $svctm=strtotime($tomorrow.' '.$h.':'.$i);
        }else{
            /* 今天 */
            $svctm=strtotime($today.' '.$h.':'.$i);
            
            if($svctm - $nowtime<= 60 * 10){
                $rs['code']=1004;
                $rs['msg']=\PhalApi\T('请选择正确的时间');
                return $rs;
            }
        }
        
        $info['svctm']=$svctm;
        
        $rs['info']=$info;
        
        return $rs;
	}
    
    /* 处理服务时间 用于显示 */
	function handelsvctm($svctm){
        $nowtime=time();
        $today_start=strtotime(date('Ymd',$nowtime));
        $svctm_start=strtotime(date('Ymd',$svctm));
        
        if($today_start<$svctm_start){
            $length=($svctm_start - $today_start) / (60*60*24);
        
            $hs=date('H:i',$svctm);
            if($length==0){
                return \PhalApi\T('今天').' '.$hs;
            }

            if($length==1){
                return \PhalApi\T('明天').' '.$hs;
            }

            if($length==2){
                return \PhalApi\T('后天').' '.$hs;
            }
            
            return date("m-d",$svctm).' '.$hs;
            
        }else{
            
            $length=($today_start - $svctm_start) / (60*60*24);
        
            $hs=date('H:i',$svctm);
            if($length==0){
                return \PhalApi\T('今天').' '.$hs;
            }

            if($length==1){
                return \PhalApi\T('昨天').' '.$hs;
            }

            if($length==2){
                return \PhalApi\T('前天').' '.$hs;
            } 
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
    
    /* 邮箱验证码 */
    function sendEmailCode($account,$code){
        
        $emailtemp = getEmailTemp();
        
        if(!checkEmail($account)){
            return ["code" => 1000, "msg" => \PhalApi\T('邮箱格式错误')];
        }
        
        
        $subject=$emailtemp['subject'];
        $template=$emailtemp['template'];
        
        if(\PhalApi\DI()->lang=='en'){
            if($emailtemp['subject_en']!=''){
                $subject=$emailtemp['subject_en'];
            }
            
            if($emailtemp['template_en']!=''){
                $template=$emailtemp['template_en'];
            }
            
        }
        
        $message=str_replace('{$code}',$code,$template);
        
        $res=sendEmail($account,$subject,$message);
        
        return $res;
        
    }
    
    /* 发送邮件 */
    function sendEmail($address, $subject, $message){

        
        $smtpSetting = getEmail();
        
        $config=[
            'host' => $smtpSetting['host'],
            'secure' => $smtpSetting['smtp_secure'],
            'port' => $smtpSetting['port'],
            'username' => $smtpSetting['username'],
            'password' => $smtpSetting['password'],
            'from' =>  $smtpSetting['from'],
            'fromName' => $smtpSetting['from_name'],
            'sign' => '',
        ];
        
        if(!\PhalApi\DI()->mailer){
            \PhalApi\DI()->mailer= new \PhalApi\PHPMailer\Lite($config,true);
        }

        $rs=\PhalApi\DI()->mailer->send($address,$subject,$message);
        // 发送邮件。
        if (!$rs) {
            return ["code" => 1000, "msg" => \PhalApi\T('发送失败')];
        } 
        
        return ["code" => 0, "msg" => ""];

    }
    
    /* APP微信支付 
    *  orderid  订单号
    *  money    CNY（元）
    *  url      回调URL（全链接）
    *  body     提示标题
    */
	function wxPay($orderid,$money,$url,$body='充值虚拟币') {
        
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
		$configpri = getConfigPri(); 

		 //配置参数检测
		if($configpri['wx_appid']== "" || $configpri['wx_mchid']== "" || $configpri['wx_key']== ""){
			$rs['code'] = 1002;
			$rs['msg'] = '微信未配置';
			return $rs;					 
		}
			 
		$noceStr = md5(rand(100,1000).time());//获取随机字符串
		$time = time();
			
		$paramarr = array(
			"appid"       =>   $configpri['wx_appid'],
			"body"        =>    $body,
			"mch_id"      =>    $configpri['wx_mchid'],
			"nonce_str"   =>    $noceStr,
			"notify_url"  =>    $url,
			"out_trade_no"=>    $orderid,
			"total_fee"   =>    $money*100, 
			"trade_type"  =>    "APP"
		);
		$sign = sign($paramarr,$configpri['wx_key']);//生成签名
		$paramarr['sign'] = $sign;
		$paramXml = "<xml>";
		foreach($paramarr as $k => $v){
			$paramXml .= "<" . $k . ">" . $v . "</" . $k . ">";
		}
		$paramXml .= "</xml>";
			 
		$ch = curl_init ();
		@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
		@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在  
		@curl_setopt($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_POST, 1);
		@curl_setopt($ch, CURLOPT_POSTFIELDS, $paramXml);
		@$resultXmlStr = curl_exec($ch);
		if(curl_errno($ch)){
			//print curl_error($ch);
			file_put_contents(API_ROOT.'/../log/appwxpay_'.date('Y-m-d').'.txt',date('y-m-d H:i:s').' 提交参数信息 ch:'.json_encode(curl_error($ch))."\r\n",FILE_APPEND);
		}
		curl_close($ch);

		$result2 = xmlToArray($resultXmlStr);
        
        if($result2['return_code']=='FAIL'){
            $rs['code']=1005;
			$rs['msg']=$result2['return_msg'];
            return $rs;	
        }
		$time2 = time();
		$prepayid = $result2['prepay_id'];
		$sign = "";
		$noceStr = md5(rand(100,1000).time());//获取随机字符串
		$paramarr2 = array(
			"appid"     =>  $configpri['wx_appid'],
			"noncestr"  =>  $noceStr,
			"package"   =>  "Sign=WXPay",
			"partnerid" =>  $configpri['wx_mchid'],
			"prepayid"  =>  $prepayid,
			"timestamp" =>  $time2
		);
		$paramarr2["sign"] = sign($paramarr2,$configpri['wx_key']);//生成签名
		
		$rs['info']=$paramarr2;
		return $rs;			
	}
	
	/**
	* sign拼装获取
	*/
	function sign($param,$key){
		$sign = "";
		foreach($param as $k => $v){
			$sign .= $k."=".$v."&";
		}
		$sign .= "key=".$key;
		$sign = strtoupper(md5($sign));
		return $sign;
	
	}
	/**
	* xml转为数组
	*/
	function xmlToArray($xmlStr){
		$msg = array(); 
		$postStr = $xmlStr; 
		$msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA); 
		return $msg;
	}
    /* APP微信支付 */
    
    /* 容联云短信验证码 */
    function sendCodeByCCP_ronglianyun($mobile,$code){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $config = getConfigPri();
        
        require_once API_ROOT.'/../sdk/ronglianyun/CCPRestSDK.php';
        
        //主帐号
        $accountSid= $config['ccp_sid'];
        //主帐号Token
        $accountToken= $config['ccp_token'];
        //应用Id
        $appId=$config['ccp_appid'];
        //请求地址，格式如下，不需要写https://
        $serverIP='app.cloopen.com';
        //请求端口 
        $serverPort='8883';
        //REST版本号
        $softVersion='2013-12-26';
        
        $tempId=$config['ccp_tempid'];
        
        file_put_contents(API_ROOT.'/../log/sendCode_ccp_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 post_data: accountSid:'.$accountSid.";accountToken:{$accountToken};appId:{$appId};tempId:{$tempId}\r\n",FILE_APPEND);

        $rest = new \REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);
        
        $datas=[];
        $datas[]=$code;
        
        $result = $rest->sendTemplateSMS($mobile,$datas,$tempId);
        file_put_contents(API_ROOT.'/../log/sendCode_ccp_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
        
         if($result == NULL ) {
            $rs['code']=1002;
			$rs['msg']=\PhalApi\T("发送失败");
            return $rs;
         }
         if($result->statusCode!=0) {
            //echo "error code :" . $result->statusCode . "<br>";
            //echo "error msg :" . $result->statusMsg . "<br>";
            //TODO 添加错误处理逻辑
            $rs['code']=1002;
			//$rs['msg']=$gets['SubmitResult']['msg'];
			$rs['msg']=\PhalApi\T("发送失败");
            return $rs;
         }

		return $rs;
    }
	/* 阿里云短信验证码 */
    function sendCodeByCCP($mobile,$code){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
       
        $config = getConfigPri();
        
        require_once API_ROOT.'/../sdk/aliyunsms/AliSmsApi.php';
        
		$config  = array(
			'accessKeyId' => $config['aly_keydi'], 
			'accessKeySecret' => $config['aly_secret'], 
			'PhoneNumbers' => $mobile, 
			'SignName' => $config['aly_signName'], 
			'TemplateCode' => $config['aly_templateCode'], 
			'TemplateParam' => array("code"=>$code) 
		);
		 
		$go = new \AliSmsApi($config);
		$result = $go->send_sms();
		file_put_contents(API_ROOT.'/../log/sendCode_ccp_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
		
        if($result == NULL ) {
            $rs['code']=1002;
			$rs['msg']=\PhalApi\T("发送失败");
            return $rs;
        }
		if($result['Code']!='OK') {
            //TODO 添加错误处理逻辑
            $rs['code']=1002;
			//$rs['msg']=$result['Code'];
			$rs['msg']=\PhalApi\T("获取失败");
            return $rs;
        }
       
		return $rs;
    }
    
    /* 动态是否点赞 */
	function dynamic_isLike($uid,$did) {
		
        if($uid<1 || $did<1 ){
            return '0';
        }
        
		$info=\PhalApi\DI()->notorm->dynamic_like
                    ->select('*')
					->where('uid=? and did=?',$uid,$did)
					->fetchOne();
        if($info){
            return '1';
        }
		
		return '0';

	}
    
    /* 动态评论是否点赞 */
	function dynamic_comment_isLike($uid,$cid) {
		
        if($uid<1 || $cid<1 ){
            return '0';
        }
        
		$info=\PhalApi\DI()->notorm->dynamic_comment_like
                    ->select('*')
					->where('uid=? and cid=?',$uid,$cid)
					->fetchOne();
        if($info){
            return '1';
        }
		
		return '0';

	}
    
    /* 数值处理 */
    function NumberFormat($num){
		if($num>=10000){
            $num=round($num/10000,1).'w';
		}
        
		return $num;
	}
    
    
    /* 滴滴订单取消：次数限定 */
	function cancel_drip_limit($uid){
		$configpri=getConfigPri();
		/* if($configpri['drip_switch']==0){
			return 0;
		} */
        
        if($configpri['drip_times']==0){
            return 0;
        }
		$date = date("Ymd");
		
		$isexist=\PhalApi\DI()->notorm->drip_limit
				->select('uid,date,times')
				->where(' uid=? ',$uid) 
				->fetchOne();
		if(!$isexist){
			$data=array(
				"uid" => $uid,
				"date" => $date,
				"times" => 1,
			);
			$isexist=\PhalApi\DI()->notorm->drip_limit->insert($data);
			return 0;
		}elseif($date == $isexist['date'] && $isexist['times'] >= $configpri['drip_times'] ){
			return 1;
		}else{
			if($date == $isexist['date']){
				if($isexist['times'] >= $configpri['drip_times']){
					//添加禁单时间
					$isbanorder=\PhalApi\DI()->notorm->user_banorder
								->where(' uid=? and type="2" ',$uid) 
								->fetchOne();
					$nowtime=time();
					$endtime=$nowtime+$configpri['ban_orderlong']*60*60;
					if($isbanorder){
						$addban=\PhalApi\DI()->notorm->user_banorder
								->where(' uid=? and type="2" ',$uid) 
								->update(array('starttime'=> $nowtime,"endtime"=>$endtime,"banlong"=>$configpri['ban_orderlong']));
					}else{
						$addban=\PhalApi\DI()->notorm->user_banorder
								->insert(array('uid'=> $uid,'type'=> "2",'starttime'=> $nowtime,"endtime"=>$endtime,"banlong"=>$configpri['ban_orderlong']));
					}
				}
				$isup=\PhalApi\DI()->notorm->drip_limit
						->where(' uid=? ',$uid) 
						->update(array('times'=> new \NotORM_Literal("times + 1 ")));
				return 0;
			}else{
				$isexist=\PhalApi\DI()->notorm->drip_limit
						->where(' uid=? ',$uid) 
						->update(array('date'=> $date ,'times'=>1));
				return 0;
			}
		}	
	}
    
    /* 邀请分成 */
    function setAgentProfit($uid,$total,$type='0'){
        if($uid<1 || $total<=0){
            return !1;
        }
        //file_put_contents(API_ROOT.'/../log/setAgentProfit_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.$uid."\r\n",FILE_APPEND);
        //file_put_contents(API_ROOT.'/../log/setAgentProfit_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 total:'.$total."\r\n",FILE_APPEND);
        $info=\PhalApi\DI()->notorm->agent->where("uid='{$uid}'")->fetchOne();
        if(!$info){
            return !1;
        }
        
            $configpri=getConfigPri();
            $agent_one=$configpri['agent_one'];
            
            $one=$info['one'];
            
            $profit_one=0;
            
            if($one>0 && $agent_one>0){
                $profit_one=floor($total* ((floor($agent_one*100)*0.01) *0.01) *100 ) *0.01;
                //file_put_contents(API_ROOT.'/../log/setAgentProfit_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 agent_one:'.$agent_one."\r\n",FILE_APPEND);
                //file_put_contents(API_ROOT.'/../log/setAgentProfit_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 profit_one:'.$profit_one."\r\n",FILE_APPEND);
                if($profit_one>0){
                    $ifok=\PhalApi\DI()->notorm->agent_profit
                            ->where("uid='{$one}'")
                            ->update( array('one_p'=> new \NotORM_Literal("one_p + {$profit_one} ")) );
                    if(!$ifok){
                        $data=[
                            'uid'=>$one,
                            'one_p'=>$profit_one,
                        ];
                        \PhalApi\DI()->notorm->agent_profit->insert($data);
                    }
                    if($type=='0'){//订单收益
						\PhalApi\DI()->notorm->user
                            ->where("id='{$one}'")
                            ->update( array('votes'=> new \NotORM_Literal("votes + {$profit_one} ")) );
					}else if($type=='1'){//礼物收益
						\PhalApi\DI()->notorm->user
                            ->where("id='{$one}'")
                            ->update( array('votes_gift'=> new \NotORM_Literal("votes_gift + {$profit_one} ")) );
					}
                }
            }
            
            
            if(!$profit_one){
                return 0;
            }
            $ifok=\PhalApi\DI()->notorm->agent_profit
                    ->where("uid='{$uid}'")
                    ->update( array('one'=> new \NotORM_Literal("one + {$profit_one} ") ) );
            if(!$ifok){
                $data=[
                    'uid'=>$uid,
                    'one'=>$profit_one,
                ];
                \PhalApi\DI()->notorm->agent_profit->insert($data);
            }
    }
    
    
    /* 获取用户累计：订单收益映票 */
    function getUserVotestotal($uid){
        $info =\PhalApi\DI()->notorm->user
				->select('votestotal')
				->where('id = ?', $uid)
				->fetchOne();
        return $info;
    }
	/* 获取用户累计:礼物收益映票 */
    function getUserGiftVotestotal($uid){
        $info =\PhalApi\DI()->notorm->user
				->select('votes_gifttotal as votestotal')
				->where('id = ?', $uid)
				->fetchOne();
        return $info;
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
			return \PhalApi\T('{s}秒',['s'=>$s]);
		}else if($iz<60){
			return \PhalApi\T('{i}分钟{s}秒',['i'=>$iz,'s'=>$s]);
		}else if($hz<24){
			return \PhalApi\T('{h}小时{i}分钟',['h'=>$hz,'i'=>$i]);
		}else{
			return \PhalApi\T('{d}天{h}小时{i}分钟',['d'=>$dz,'h'=>$h,'i'=>$i]);
		}
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
    
    /* 聊天室类型 */
    function getLiveType($k=''){
        
        $type=[
            '1'=>'派单',
            '2'=>'交友',
            '3'=>'闲谈',
            '4'=>'点歌',
        ];
        
        if($k==''){
            return $type;
        }
        
        return isset($type[$k])?$type[$k]:'';
    }
    
	 /* 普通订单取消：次数限定 */
	function cancel_order_limit($uid){
		
		$configpri=getConfigPri();
		
        if($configpri['order_times']==0){
            return 0;
        }
		$date = date("Ymd");
		
		$isexist=\PhalApi\DI()->notorm->orders_limit
				->select('uid,date,times')
				->where(' uid=? ',$uid) 
				->fetchOne();
				
		if(!$isexist){
			$data=array(
				"uid" => $uid,
				"date" => $date,
				"times" => 1,
			);
			$isexist=\PhalApi\DI()->notorm->orders_limit->insert($data);
			return 0;
		}elseif($date == $isexist['date'] && $isexist['times'] >= $configpri['order_times'] ){
			return 1;
		}else{
			
			if($date == $isexist['date']){
				$isup=\PhalApi\DI()->notorm->orders_limit
						->where(' uid=? ',$uid) 
						->update(array('times'=> new \NotORM_Literal("times + 1 ")));
				$newlimit=intval($isexist['times'])+1;
				if($newlimit >= $configpri['order_times']){
					
					//添加禁单时间
					$isbanorder=\PhalApi\DI()->notorm->user_banorder
								->where(' uid=? and type="1" ',$uid) 
								->fetchOne();
					$nowtime=time();
					$endtime=$nowtime+$configpri['ban_orderlong']*60*60;
					if($isbanorder){
						$addban=\PhalApi\DI()->notorm->user_banorder
								->where(' uid=? and type="1" ',$uid) 
								->update(array('starttime'=> $nowtime,"endtime"=>$endtime,"banlong"=>$configpri['ban_orderlong']));
					}else{
						$addban=\PhalApi\DI()->notorm->user_banorder
								->insert(array('uid'=> $uid,'type'=> "1",'starttime'=> $nowtime,"endtime"=>$endtime,"banlong"=>$configpri['ban_orderlong']));
					}
				}
				
				
				return 0;
			}else{
				$isexist=\PhalApi\DI()->notorm->orders_limit
						->where(' uid=? ',$uid) 
						->update(array('date'=> $date ,'times'=>1));
				return 0;
			}
		}	
	}
    
	/****禁止下单状态****/
    function getBanstatus($uid,$type){
		$rs=array("isbanorder"=>'0',"endtime"=>'0');
		$isbanorder=\PhalApi\DI()->notorm->user_banorder
						->where(' uid=? and type =? and endtime > ?',$uid,$type,time()) 
						->fetchOne();
		if($isbanorder){
			$rs['isbanorder']="1";
			$rs['endtime']=date('m月d日H:i',$isbanorder['endtime']);
		}
		return $rs;
	}
	
    /* 是否拉黑 */
    function isBlack($uid,$liveuid){
		if($uid<1 || $liveuid<1 || $uid==$liveuid){
            return '0';
        }
        
        $isok =\PhalApi\DI()->notorm->user_black
                ->select('*')
				->where('uid = ? and touid=?', $uid,$liveuid)
				->fetchOne();
        
        if($isok){
            return '1';
        }
        return '0';
    }
    
	/* 私信：发送私信消息
	 * @param int type 信息是否同步,1是，2否，默认2
	 * @param string account_id 发送人
	 * @param string receiver 接收人
	 * @param array msg_content 消息体
	*/
    function sendImCustom($uid,$touid,$data,$apitype,$type=2){

        /* IM */
		$ext=$data;

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
        
        $account_id=(string)$uid;
        $receiver=(string)$touid; 
        $api=\App\getTxRestApi($apitype);
        $ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,$type);
        
       file_put_contents(API_ROOT.'/runtime/aasendImCustom'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
        /* IM */
		
		return 1;     
    }
    
	/* 系统代发：发送自定义消息 */
    function sendImSysCustom($touid,$data,$apitype,$type=2){


        $ext=$data;

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
        $receiver=(string)$touid;
        $api=\App\getTxRestApi($apitype);
        $ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,$type);
        
      
        // file_put_contents(API_ROOT.'/runtime/sendImOrder'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
        /* IM */
		
		return 1;     
    }
    //判断用户是否注销
	function checkIsDestroyByUid($uid){
		$user_status=\PhalApi\DI()->notorm->user->where("id=?",$uid)->fetchOne('user_status');
		if($user_status==3){
			return 1;
		}

		return 0;
	}
    
    
    /**
	*  @desc 腾讯音视频聊天地址获取
	*  @param string $host 协议，如:http、rtmp
	*  @param string $stream 流名,如有则包含 .flv、.m3u8
	*  @param int $type 类型，0表示播流，1表示推流
	*/
	function PrivateKey_tx_talk($host,$stream){
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
        $live_code=urlencode($trtc_appid.'_'.$streamKey.'_'.$streamKey.'_'.'main');
       
        // $live_code=urlencode($trtc_appid.'_'.$streamKey.'_'.'trtc');
        
		if($ext){
            $url = "http://{$pull}/live/" . $live_code . ".".$ext;
        }else{
            $url = "http://{$pull}/live/" . $live_code . ".flv";
        }
        

		return $url;
	}
    /* 判断是否关注 */
	function isAttention($uid,$touid) {
		$isexist=\PhalApi\DI()->notorm->user_attention
					->select("*")
					->where('uid=? and touid=?',$uid,$touid)
					->fetchOne();
		if($isexist){
			return  '1';
		}
        return  '0';
	}
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
