<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Photo as Domain_Photo;

/**
 * 相册
 */
 
class Photo extends Api {

	public function getRules() {
		return array(
            'getMyPhotos' => array(
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
            
            'setPhoto' => array(
				'thumbs' => array('name' => 'thumbs', 'type' => 'string', 'desc' => '照片，多个以逗号分割'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token thumbs'),
			),
            
            'delPhoto' => array(
				'id' => array('name' => 'id', 'type' => 'int', 'desc' => '照片ID'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名 uid token id'),
			),
            
            'getPhotos' => array(
				'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
				'p' => array('name' => 'p', 'type' => 'int', 'desc' => '页码'),
			),
		);
	}
    
	/**
	 * 我的相册 
	 * @desc 用于获取我的相册
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string info[].id 照片ID
	 * @return string info[].thumb 照片链接
	 * @return string info[].status 状态，0待审核，1通过，-1拒绝
	 * @return string msg 提示信息
	 */
	public function getMyPhotos() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $p=\App\checkNull($this->p);
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $where=[
            'uid'=>$uid,
        ];
        $order='id desc';
        
        $domain = new Domain_Photo();
		$list = $domain->getPhotos($where,$order,$p);

        $rs['info']=$list;
		return $rs;
	}
    
    /**
	 * 上传照片 
	 * @desc 用于上传照片
	 * @return int code 操作码，0表示成功
	 * @return array info 
	 * @return string msg 提示信息
	 */
	public function setPhoto() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $thumbs=\App\checkNull($this->thumbs);
        $sign=\App\checkNull($this->sign);
        
        if($uid<1 || $token=='' || $thumbs=='' || $sign==''){
            $rs['code'] = 1000;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'thumbs'=>$thumbs,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $domain = new Domain_Photo();
		$res = $domain->setPhoto($uid,$thumbs);
        
		return $res;
	}
    
	/**
	 * 删除照片 
	 * @desc 用于删除照片
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function delPhoto() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $id=\App\checkNull($this->id);
        $sign=\App\checkNull($this->sign);
        
        if($id<1 || $sign==''){
            $rs['code'] = 1000;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'id'=>$id,
        );
        
        $issign=\App\checkSign($checkdata,$sign);
        if(!$issign){
            $rs['code']=1001;
			$rs['msg']=\PhalApi\T('签名错误');
			return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        
        $domain = new Domain_Photo();
		$res = $domain->delPhoto($uid,$id);
        
		return $res;
	}

	/**
	 * 个人主页相册 
	 * @desc 用于获取个人主页相册
	 * @return int code 操作码，0表示成功
	 * @return array info 列表
	 * @return string msg 提示信息
	 */
	public function getPhotos() {
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
        $uid=\App\checkNull($this->uid);
        $token=\App\checkNull($this->token);
        $liveuid=\App\checkNull($this->liveuid);
        $p=\App\checkNull($this->p);
        
        if($liveuid<1){
            $rs['code'] = 1000;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkToken=\App\checkToken($uid,$token);
		if($checkToken==700){
			$rs['code'] = $checkToken;
			$rs['msg'] = \PhalApi\T('您的登陆状态失效，请重新登陆！');
			return $rs;
		}
        
        $where=[
            'uid'=>$liveuid,
            'status'=>1,
        ];
        $order='id desc';
        
        $domain = new Domain_Photo();
		$list = $domain->getPhotos($where,$order,$p);

        $rs['info']=$list;
        
		return $rs;
	}

}
