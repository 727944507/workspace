<?php
namespace App\Domain;

use App\Model\Livetalk as Model_Livetalk;

class Livetalk {
    
    /* 音视频聊天成功 */
	public function start($data) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $model = new Model_Livetalk(); 
        $info= $model->setInfo($data);
		
		if($info===false){
			$rs['code']="1001";
			$rs['msg']=\PhalApi\T('操作失败');
		}
        $rs['info'][0]=$info;
        
		return $rs;
	}

    /* 关闭聊天室 */
	public function stop($uid) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $model = new Model_Livetalk();
        $liveinfo= $model->getInfo($uid);
        if($liveinfo){
            $data2=['islive'=>0];
			$info=$model->upInfo($uid,$data2);
			$rs['info'][0]=$info;
        }else{
			$rs['info'][0]=0;
		}
		return $rs;
	}

  
    /* 修改聊天室状态 */
	public function changeLive($uid,$type) {
	
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
		
        if($type==1){
            $data=['islive'=>1];
            $model = new Model_Livetalk();
            $res=$model->upInfo($uid,$data);
        }else{
            $res=$this->stop($uid);
            return $res;
        }
        $rs['info'][0]=$res;
		return $rs;
	}
    
	
}
