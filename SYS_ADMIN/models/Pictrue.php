<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 15:26
 */

namespace SYS_ADMIN\models;


use yii\base\Model;
use yii\db\ActiveRecord;

class Pictrue extends ActiveRecord
{
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'gif'], 'on' => 'pic'],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'gif'], 'maxFiles' => 4, 'on' => 'pics'],
        ];
    }

    public function upload()
    {
        $err = "";
        $pic_list = [];
        $this->setScenario('pic');
        $base_path = "uploads/images/".date('Ymd').'/';
        if($this->validate()){
            if(!is_dir($base_path) || !is_writable($base_path)){
                \yii\helpers\FileHelper::createDirectory($base_path, 0777, true);
            }

            $file_name = md5(uniqid().mt_rand(100000, 9999999)). '.' . $this->imageFile->extension;
            $file_path = $base_path.$file_name;
            if($this->imageFile->saveAS($file_path)){
                $model = new Pictrue();
                $model->pic_name = $this->imageFile->baseName;
                $model->md5_name = $file_name;
                $model->pic_path = $file_path;
                $model->pic_size = $this->imageFile->size;
                $model->created_at = time();
                $model->save(false);

                $pic_list = \Yii::$app->db->getLastInsertID();
            } else {
                return ['status' => 401, 'info' => '上传失败'];
            }

            return ['status' => 1, 'images' => $pic_list];
        } else {
            return ['status' => 401, 'info' => implode(',', $this->getFirstErrors())];
        }
    }


    public function multiUpload()
    {
        $err = "";
        $pic_list = [];
        $this->setScenario('pics');
        $base_path = "uploads/images/".date('Ymd').'/';
        if($this->validate()){
            if(!is_dir($base_path) || !is_writable($base_path)){
                \yii\helpers\FileHelper::createDirectory($base_path, 0777, true);
            }

            foreach ($this->imageFile as $file){
                $file_name = md5(uniqid().mt_rand(100000, 9999999)). '.' . $file->extension;
                $file_path = $base_path.$file_name;

                if($file->saveAS($file_path)){
                    $model = self::find();
                    $model->pic_name = $file->baseName;
                    $model->md5_name = $file_name;
                    $model->pic_path = $file_path;
                    $model->created_at = time();
                    $model->save(false);

                    $pic_list[] = \Yii::$app->db->getLastInsertID();
                } else {
                    $err = ['status' => 401, 'info' => '上传失败'];
                    break;
                }
            }

            if(!empty($err)){
                return $err;
            }

            return ['status' => 1, 'images' => $pic_list];
        } else {
            return ['status' => 401, 'info' => implode(',', $this->getFirstErrors())];
        }
    }


}