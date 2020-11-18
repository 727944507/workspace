<?php

/* 腾讯IM回调 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class ImbackController extends HomebaseController{
    
    public function index() {
        
        $rs=[
            'ActionStatus'=>'OK',
            'ErrorCode'=>0,
            'ErrorInfo'=>''
        ];
        

        $data = $this->request->param();
        //file_put_contents(CMF_ROOT .'log/imback.txt',date('Y-m-d H:i:s').' 提交参数信息 data:'.json_encode($data)."\r\n",FILE_APPEND);
        if(!$data || !$data['SdkAppid']){
            //file_put_contents(CMF_ROOT .'log/imback.txt',date('Y-m-d H:i:s').' 提交参数信息 信息错误:'."\r\n",FILE_APPEND);
            echo json_encode($rs);
            exit;
        }
        $configpri=getConfigPri();
        if($data['SdkAppid'] != $configpri['im_sdkappid']){
            //file_put_contents(CMF_ROOT .'public/imback.txt',date('Y-m-d H:i:s').' 提交参数信息 sdkappid不一致:'."\r\n",FILE_APPEND);
            echo json_encode($rs);
            exit;
        }
        

        if($data['CallbackCommand']=='State.StateChange'){
            /* 状态变更 */
            $uid=$data['Info']['To_Account'];
            $action=$data['Info']['Action'];
            
            if($action=='Login'){
                //file_put_contents(CMF_ROOT .'log/imback.txt',date('Y-m-d H:i:s').' 提交参数信息 更新上线:'.$uid."\r\n",FILE_APPEND);
                Db::name('user')
                    ->where("id='{$uid}'")
                    ->update( ['online'=>'3'] );
            }
            if($action=='Logout'){
                //file_put_contents(CMF_ROOT .'log/imback.txt',date('Y-m-d H:i:s').' 提交参数信息 更新下线:'.$uid."\r\n",FILE_APPEND);
                $result=Db::name('user')
                    ->where("id='{$uid}'")
                    ->update( ['online'=>'0'] );
                //file_put_contents(CMF_ROOT .'log/imback.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
            }
        }
        
        
        echo json_encode($rs);
        exit;

	}

}