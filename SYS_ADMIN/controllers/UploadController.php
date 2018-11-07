<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7
 * Time: 8:15
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\models\Pictrue;
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
}