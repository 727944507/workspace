<?php

/* 派单处理 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class UpdispatchController extends HomebaseController{
        
    /* 发送IM */
    public function send() {
        
        $data = $this->request->param();
        $lastid=isset($data['lastid']) ? $data['lastid']: '0';
        $lastid = (int)checkNull($lastid);
        if(!$lastid){
            $lastid=0;
        }

        //file_put_contents(CMF_ROOT.'log/Updispatch_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 lastid:'.$lastid."\r\n",FILE_APPEND);
        //file_put_contents(CMF_ROOT.'log/Updispatch_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 开始:'.$lastid.'--'.time()."\r\n",FILE_APPEND);
        
        $limit=1000;
        $where=[];
        $where[]=['id','>',$lastid];
        $where[]=['issend','=',0];
        
        $skilllist=getSkillList();
        
        $list=Db::name("dispatch")->where($where)->order("id asc")->limit(0,$limit)->select()->toArray();

        $nums=count($list);
        
        $type=2;
        $api=getTxRestApi($type);
        
        foreach($list as $k=>$v){
            
            Db::name("dispatch")->where(['id'=>$v['id']])->update(['issend'=>1]);
            
            $uid=$v['uid'];
            $stream=$v['stream'];
            $skillid=$v['skillid'];
            $levelid=$v['levelid'];
            $sex=$v['sex'];
            $age=$v['age'];
            $coin=$v['coin'];
            
            /* 技能信息 */
            $skill=[];
            foreach($skilllist as $k=>$v){
                if($v['id']==$skillid){
                    $skill=$v;
                    break;
                }
            }
            if(!$skill){
                continue;
            }
            
            $liveinfo=Db::name("live")->field('*')->where(['uid'=>$uid,'stream'=>$stream,'islive'=>1])->find();
            if(!$liveinfo){
                continue;
            }
            unset($liveinfo['deviceinfo']);
            unset($liveinfo['starttime']);
            
            $liveinfo['thumb']=get_upload_path($liveinfo['thumb']);
            $liveinfo['pull']=PrivateKeyA('http',$liveinfo['uid']);
            
            $userinfo=getUserInfo($liveinfo['uid']);
            $liveinfo['user_nickname']=$userinfo['user_nickname'];
            $liveinfo['avatar']=$userinfo['avatar'];
            $liveinfo['avatar_thumb']=$userinfo['avatar_thumb'];
            
            $where2=[
                ['status','=',1],
                ['switch','=',1],
                ['skillid','=',$skillid],
                ['uid','<>',$uid],
            ];
            
            if($levelid!=0){
                $where2[]=['levelid','>=',$levelid];
            }
            
            if($coin!=0){
                $where2[]=['coin','<=',$coin];
            }
            
            if($sex==1){
                $where2[]=['sex','=',1];
            }else if($sex==2){
                $where2[]=['sex','<>',1];
            }
            
            if($age!=0){
                $time=getAges($age);
                if(!$time){
                   continue;
                }
                $where3=[
                    ['user_type','=',2],
                ];
                $where3[]=['birthday','>=',$time[0]];
                $where3[]=['birthday','<',$time[1]];
                $list_user=Db::name("user")->field('id')->where($where3)->select()->toArray();
                if(!$list_user){
                    continue;
                }
                
                $users_a=array_column($list_user,'id');
                
                $where2[]=['uid','in',$users_a];
                
            }
            
            
            
            
            $liveinfo['skillname']=$skill['name'];
            
            $liveinfo['method']='dispatch';
            $list2=Db::name("skill_auth")->field('uid')->where($where2)->select()->toArray();
            
            foreach($list2 as $k2=>$v2){

                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem',       //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($liveinfo),
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
                //file_put_contents(CMF_ROOT.'log/Updispatch_send_'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 receiver:'.json_encode($receiver)."\r\n",FILE_APPEND);
                //file_put_contents(CMF_ROOT.'log/Updispatch_send_'.date('Y-m-d').'.txt',date('y-m-d H:i:s').'提交参数信息 ret:'.json_encode($ret)."\r\n",FILE_APPEND);
            }
            
                
            $lastid=$v['id'];
        }

        //file_put_contents(CMF_ROOT.'log/Updispatch_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 结束:'.$lastid.'--'.time()."\r\n",FILE_APPEND);
        
        if($nums<$limit){
            //file_put_contents(CMF_ROOT.'log/Updispatch_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 echo NO:'.$lastid.'--'."\r\n",FILE_APPEND);
            echo "NO";
            exit;   
        }
        //file_put_contents(CMF_ROOT.'log/Updispatch_send_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 echo lastid:'.$lastid.'--'.$this->lastid."\r\n",FILE_APPEND);
        echo 'OK-'.$lastid;
        exit;

	}

}