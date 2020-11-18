<?php
namespace App\Domain;

use App\Model\Im as Model_Im;

class Im {

    /* 系统通知 */
	public function getSysNotice($uid,$p) {

        $model = new Model_Im();
        $list= $model->getSysNotice($uid,$p);

        foreach($list as $k=>$v){
            unset($v['ip']);
            $v['addtime']=date("Y-m-d H:i:s",$v['addtime']);
            
            $list[$k]=$v;
        }
		return $list;
	}
	
	/* 系统通知：读取状态 */
	public function getStatus($uid) {

        $model = new Model_Im();
        $rs= $model->getStatus($uid);

		return $rs;
	}
}
