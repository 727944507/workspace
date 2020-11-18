<?php
namespace App\Domain;

use App\Model\Photo as Model_Photo;

class Photo {

    /* 获取相册 */
	public function getPhotos($where,$order='id desc',$p) {
        
        if(!$where){
            return [];
        }
        
        $model = new Model_Photo();
        $list= $model->getPhotos($where,$order,$p);

        foreach($list as $k=>$v){
            $v['thumb']=\App\get_upload_path($v['thumb']);
            $list[$k]=$v;
        }
        
		return $list;
	}
    
    /* 上传照片 */
	public function setPhoto($uid,$thumbs) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('上传成功，等待审核'), 'info' => array());
        
        $thumbs_a=array_filter(preg_split('/,|，/',$thumbs));
        
        if(!$thumbs_a){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('信息错误');
            return $rs;
        }
        
        $nowtime=time();
        
        $model = new Model_Photo();
        foreach($thumbs_a as $k=>$v){
            $data=[
                'uid'=>$uid,
                'thumb'=>$v,
                'addtime'=>$nowtime,
            ];
            $res= $model->setPhoto($data);
        }
        
        
		return $rs;
	}

    /* 删除照片 */
	public function delPhoto($uid,$id) {
        
        $rs = array('code' => 0, 'msg' => \PhalApi\T('删除成功'), 'info' => array());
                
        $model = new Model_Photo();
        
        $where1="id={$id}";
        $info= $model->getPhoto($where1);
        if(!$info){
            $rs['code'] = 1002;
            $rs['msg'] = \PhalApi\T('照片不存在');
            return $rs;
        }
        
        if($info['uid']!=$uid){
            $rs['code'] = 1003;
            $rs['msg'] = \PhalApi\T('无权操作');
            return $rs;
        }
        
        $where="id={$id} and uid={$uid}";
        $res= $model->delPhoto($where);

        
		return $rs;
	}
    
}
