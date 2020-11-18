<?php

/* 上传 */
namespace app\appapi\controller;

use cmf\controller\HomeBaseController;
use cmf\lib\Upload;

class UploadController extends HomeBaseController
{
    /* 图片上传 */
	public function upload(){
        $file=isset($_FILES['file']) ? $_FILES['file']: [];
        if(!$file){
            echo json_encode(array( "code"=>0,'data'=>'','msg'=>lang('请选择图片') ));
            exit;
        }
        
        if($file['error']!=0){
            echo json_encode(array( "code"=>0,'data'=>'','msg'=>lang('请重新选择图片') ));
            exit;
        }
        //file_put_contents('./upload.txt',date('Y-m-d H:i:s').' 提交参数信息 file:'.json_encode($_FILES)."\r\n",FILE_APPEND);
        if ($this->request->isPost()) {

            $uploader = new Upload();

            $result = $uploader->upload();

            if ($result === false) {
                //file_put_contents('./upload.txt',date('Y-m-d H:i:s').' 提交参数信息 msg:'.$uploader->getError()."\r\n",FILE_APPEND);
                //$this->error();
                //echo json_encode(array( "code"=>0,'data'=>'','msg'=>$uploader->getError() ));
                echo json_encode(array( "code"=>0,'data'=>'','msg'=>lang('上传失败，请重试') ));
                exit;
            } else {
                //$this->success("上传成功!", '', $result);
                //file_put_contents('./upload.txt',date('Y-m-d H:i:s').' 提交参数信息 result:'.json_encode($result)."\r\n",FILE_APPEND);
                echo json_encode(array("code"=>200,'data'=>$result,'msg'=>''));
                exit;
            }
        }
	}
}
