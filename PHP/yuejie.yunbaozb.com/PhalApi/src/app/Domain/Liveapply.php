<?php
namespace App\Domain;

use App\Model\Liveapply as Model_Liveapply;

class Liveapply {

    /* 申请详情 */
	public function getInfo($uid) {

		$where=['uid'=>$uid];
        $model = new Model_Liveapply();
        $info= $model->getInfo($where);
        
		return $info;
	}

    /* 申请 */
	public function apply($uid,$data) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('操作成功'), 'info' => array());
        
        $data['addtime']=time();
        $data['status']=0;
        $data['reason']='';

        $model = new Model_Liveapply();
        $where=[
            'uid'=>$uid
        ];
        $res= $model->up($where,$data);
        if(!$res){
            $data['uid']=$uid;
            $res=$model->set($data);
        }
        
		return $rs;
	}
	
}
