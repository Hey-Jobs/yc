<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7
 * Time: 8:15
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Video;
use yii\web\UploadedFile;

class UploadController extends CommonController
{
    // 上传图片 api
    public function actionImg()
    {
        if(isset($_FILES['img']) && !empty($_FILES['img']['name'])){
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('img');
            $img_list = $picModel->upload();
            if(isset($img_list['images'])){
                $this->successInfo($img_list);
            } else {
                return $this->errorInfo(400, $img_list['info']);
            }
        } else {
            return $this->errorInfo(400, "请上传图片");
        }
    }

    // 上传图片 api
    public function actionOssImg()
    {
        if(isset($_FILES['img']) && !empty($_FILES['img']['name'])){
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('img');
            $img_list = $picModel->uploadOss();
            if(isset($img_list['images'])){
                $this->successInfo($img_list);
            } else {
                return $this->errorInfo(400, $img_list['info']);
            }
        } else {
            return $this->errorInfo(400, "请上传图片");
        }
    }


    // 上传图片 api
    public function actionOssFile()
    {
        if(isset($_FILES['file']) && !empty($_FILES['file']['name'])){
            $uploadFile = UploadedFile::getInstanceByName('file');
            $base_path = "uploads/".date('Ymd').'/';
            $file_name = md5(uniqid().mt_rand(100000, 9999999)). '.' . $uploadFile->extension;
            $file_path = $base_path.$file_name;
            $video_path = CommonHelper::OssUploadFile($uploadFile->tempName, $file_path, ConStatus::$OSS_BASE_DIR);
            if(!empty($video_path)){
                $this->successInfo($video_path);
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
            }
        } else {
            return $this->errorInfo(400, "请上传图片");
        }
    }

    // 上传图片 api
    public function actionImgBase64()
    {
        $base64_img = \Yii::$app->request->post("img");
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {
            $picModel = new Pictrue();
            $data = base64_decode(str_replace($result[1], '', $base64_img));
            $img_list = $picModel->uploadBase64($data, $result[2]);
            if(isset($img_list['images'])){
                $this->successInfo($img_list);
            } else {
                return $this->errorInfo(400, $img_list['info']);
            }
        } else {
            return $this->errorInfo(400, "请上传图片");
        }


    }

}