<?php

namespace App\Api;

use PhalApi\Api;
use App\Domain\Comment as Domain_Comment;

/**
 * 评论
 */
 
class Comment extends Api {

	public function getRules() {
		return array(
            'setComment' => array(
                'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单id'),
                'content' => array('name' => 'content', 'type' => 'string', 'desc' => '内容'),
                'star' => array('name' => 'star', 'type' => 'int', 'desc' => '星级'),
                'label' => array('name' => 'label', 'type' => 'string', 'default'=>'', 'desc' => '标签ID，多个用,拼接'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),
            
            'setEvaluate' => array(
                'orderid' => array('name' => 'orderid', 'type' => 'int', 'desc' => '订单id'),
                'content' => array('name' => 'content', 'type' => 'string', 'desc' => '内容'),
                'star' => array('name' => 'star', 'type' => 'int', 'desc' => '星级'),
                'sign' => array('name' => 'sign', 'type' => 'string', 'desc' => '签名'),
			),
            'getComment' => array(
                'skillid' => array('name' => 'skillid', 'type' => 'int', 'desc' => '技能ID'),
                'liveuid' => array('name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'),
                'p' => array('name' => 'p', 'type' => 'int', 'default'=>'1', 'desc' => '页码'),
            ),
		);
	}
    
    /**
     * 评论
     * @desc 用于用户评论主播
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string msg 提示信息
     */
    public function setComment() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid = \App\checkNull($this->uid);
        $token = \App\checkNull($this->token);
        $orderid = \App\checkNull($this->orderid);
        $content = \App\checkNull($this->content);
        $star = \App\checkNull($this->star);
        $label = \App\checkNull($this->label);
        $sign = \App\checkNull($this->sign);

        if($uid<1 || $token=='' || $orderid<1 || $star<1 || $star > 5 || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'orderid'=>$orderid,
            'star'=>$star,
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
        
        $data=[
            'uid'=>$uid,
            'orderid'=>$orderid,
            'content'=>$content,
            'star'=>$star,
            'label'=>$label,
        ];
        
        $domain = new Domain_Comment();
		$info = $domain->setComment($data);
        
        return $info;
    }

    /**
     * 评价
     * @desc 用于主播评价用户
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string msg 提示信息
     */
    public function setEvaluate() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $uid = \App\checkNull($this->uid);
        $token = \App\checkNull($this->token);
        $orderid = \App\checkNull($this->orderid);
        $content = \App\checkNull($this->content);
        $star = \App\checkNull($this->star);
        $sign = \App\checkNull($this->sign);

        if($uid<1 || $token=='' || $orderid<1 || $star<1 || $star > 5 || $sign==''){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $checkdata=array(
            'uid'=>$uid,
            'token'=>$token,
            'orderid'=>$orderid,
            'star'=>$star,
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
        
        $data=[
            'uid'=>$uid,
            'orderid'=>$orderid,
            'content'=>$content,
            'star'=>$star,
        ];
        
        $domain = new Domain_Comment();
		$info = $domain->setEvaluate($data);
        
        return $info;
    }

    /**
     * 技能评论
     * @desc 用于获取技能评论
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].id 分类ID
     * @return string info[].content 内容
     * @return string info[].star 星级
     * @return string info[].add_time 时间
     * @return object info[].userinfo 用户信息
     * @return array info[].label_a 评论标签
     * @return string info[].label_a[] 标签
     * @return string msg 提示信息
     */
    public function getComment() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        
        $skillid = \App\checkNull($this->skillid);
        $liveuid = \App\checkNull($this->liveuid);
        $p = \App\checkNull($this->p);

        
        if($skillid<1 || $liveuid<1){
            $rs['code'] = 1001;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $where=[];
        $where['liveuid']=$liveuid;
        $where['skillid']=$skillid;
        
        $domain = new Domain_Comment();
		$list = $domain->getComment($p,$where);

        $rs['info']=$list;
        
        return $rs;
    }
	


}
