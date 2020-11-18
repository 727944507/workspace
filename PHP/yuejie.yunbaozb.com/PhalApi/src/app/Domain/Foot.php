<?php
namespace App\Domain;

use App\Model\Foot as Model_Foot;

class Foot {

    /* 最近来访 */
	public function getVisit($uid,$lasttime) {
        
        $where=[
            'liveuid'=>$uid,
			'type'=>1,
        ];
        if($lasttime>0){
            $where['addtime<?']=$lasttime;
        }

        $model = new Model_Foot();
        if($lasttime==0){
            /* 重置新增来访 */
            $model->resetNew($uid);
        }
        $list= $model->getFoot($where);

        foreach($list as $k=>$v){
            $v['datetime']=\App\offtime($v['addtime']);
            $v['userinfo']=\App\getUserInfo($v['uid']);
            unset($v['uid']);
            unset($v['liveuid']);
            $list[$k]=$v;
        }
		return $list;
	}
    
    /* 浏览足迹 */
	public function getView($uid,$lasttime) {
        
        $where=[
            'uid'=>$uid,
			'type'=>0,
        ];
        if($lasttime>0){
            $where['addtime<?']=$lasttime;
        }
        
        $model = new Model_Foot();
        $list= $model->getFoot($where);

        foreach($list as $k=>$v){
            $v['datetime']=\App\offtime($v['addtime']);
            $v['userinfo']=\App\getUserInfo($v['liveuid']);
            unset($v['uid']);
            unset($v['liveuid']);
            unset($v['nums']);
            $list[$k]=$v;
        }
		return $list;
	}
    
    /* 清空足迹 */
	public function clearView($uid) {
        
        $where=[
            'uid'=>$uid,
        ];
        
        $model = new Model_Foot();
        $rs= $model->delFoot($where,0);
        
		return $rs;
	}
    
    /* 添加足迹 */
	public function addFootBF($uid,$liveuid) {
        
        $model = new Model_Foot();
        $rs= $model->addFoot($uid,$liveuid);
        
        if($rs){
            /* 更新主播来访 */
            $this->addVisit($liveuid);
            /* 更新用户访问 */
            $this->addView($uid);
        }

		return $rs;
	}
    
	/* 添加足迹 */
	public function addFoot($uid,$liveuid) {
        
        // $model = new Model_Foot();
       /*  $rs= $model->addFoot($uid,$liveuid);
        
        if($rs){ */
            /* 更新主播来访 */
          $rs= $this->addVisit($uid,$liveuid,1);
		  if($rs){ 
            /* 更新用户访问 */
            $rs= $this->addView($uid,$liveuid,0);
		  }
        /* } */

		return $rs;
	}
	
    /* 获取来访次数 */
	public function getVisitNums($uid) {
        
        $model = new Model_Foot();
        $rs= $model->getVisitNums($uid);

		return $rs;
	}
    
    /* 更新来访次数 */
	public function addVisit($uid,$liveuid,$type) {
        
        $model = new Model_Foot();
        $rs= $model->addVisit($uid,$liveuid,$type);
        if($rs){
            $model->addNew($liveuid);
        }

		return $rs;
	}
    
    /* 获取访问次数 */
	public function getViewNums($uid) {
        
        $model = new Model_Foot();
        $rs= $model->getViewNums($uid);

		return $rs;
	}
	
    
    /* 更新访问次数 */
	public function addView($uid,$liveuid,$type) {
        
        $model = new Model_Foot();
        $rs= $model->addView($uid,$liveuid,$type);

		return $rs;
	}

    /* 获取新增来访次数 */
	public function getNewNums($uid) {
        
        $model = new Model_Foot();
        $rs= $model->getNewNums($uid);

		return $rs;
	}
	/* 清空来访记录 */
	public function clearVisit($uid) {
        
        $where=[
            'uid'=>$uid,
        ];
        
        $model = new Model_Foot();
        $rs= $model->delFoot($where,1);
        
		return $rs;
	}
	
}
