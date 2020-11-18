<?php

/* 滴滴订单处理 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class UpdripController extends HomebaseController{
    
    /* 处理超时 */
    public function Up() {

        $now=time();
        $addtime=$now;
        
        $where=[];
        $where[]=['status','=',0];
        $where[]=['svctm','<',$addtime];

        $list=Db::name("drip")->where($where)->order("id asc")->update(['status'=>-2]);

        echo 'OK';
        exit;

	}
    
    /* 发送IM */
    public function send() {
        
        $data = $this->request->param();
        $lastpetid=isset($data['lastpetid']) ? $data['lastpetid']: '0';
        $lastpetid = (int)checkNull($lastpetid);
        if(!$lastpetid){
            $lastpetid=0;
        }

        //file_put_contents(CMF_ROOT.'log/Updrip_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 lastpetid:'.$lastpetid."\r\n",FILE_APPEND);
        //file_put_contents(CMF_ROOT.'log/Updrip_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 开始:'.$lastpetid.'--'.time()."\r\n",FILE_APPEND);
        
        $limit=1000;
        $where=[];
        $where[]=['id','>',$lastpetid];
        $where[]=['status','=',0];
        $where[]=['issend','=',0];
        
        $list=Db::name("drip")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();

        $nums=count($list);
        
        $type=1;
        $api=getTxRestApi($type);
        
        foreach($list as $k=>$v){
            
            $skillid=$v['skillid'];
            $levelid=$v['levelid'];
            $sex=$v['sex'];
            
            $where2="status=1 and switch=1 and skillid={$skillid} and levelid>={$levelid}";
            if($sex==1){
                $where2.=" and sex=1";
            }else if($sex==2){
                $where2.=" and sex!=1";
            }
            
            $list2=Db::name("skill_auth")->field('uid')->where($where2)->select()->toArray();
            foreach($list2 as $k2=>$v2){
                
                $ext=['method'=>'drip'];

                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem',       //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($ext),
                        'Desc' => '',
                        //  'Ext' => $ext,
                        //  'Sound' => '',
                    )
                );
                //将创建的元素$msg_content_elem, 加入array $msg_content
                array_push($msg_content, $msg_content_elem);
                
                $account_id=(string)0;
                $receiver=(string)$v2['uid'];

                $ret = $api->openim_send_msg_custom($account_id, $receiver, $msg_content,2);
                //file_put_contents(CMF_ROOT.'log/Updrip_send_'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 receiver:'.json_encode($receiver)."\r\n",FILE_APPEND);
                //file_put_contents(CMF_ROOT.'log/Updrip_send_'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
            }
            Db::name("drip")->where(['id'=>$v['id']])->update(['issend'=>1]);
                
            $lastpetid=$v['id'];
        }

        //file_put_contents(CMF_ROOT.'log/Updrip_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 结束:'.$lastpetid.'--'.time()."\r\n",FILE_APPEND);
        
        if($nums<$limit){
            //file_put_contents(CMF_ROOT.'log/Updrip_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 echo NO:'.$lastpetid.'--'."\r\n",FILE_APPEND);
            echo "NO";
            exit;   
        }
        //file_put_contents(CMF_ROOT.'log/Updrip_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 echo lastpetid:'.$lastpetid.'--'.$this->lastpetid."\r\n",FILE_APPEND);
        echo 'OK-'.$lastpetid;
        exit;

	}

}