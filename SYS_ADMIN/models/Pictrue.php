<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/4
 * Time: 15:26
 */

namespace SYS_ADMIN\models;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use yii\base\Model;
use yii\db\ActiveRecord;

class Pictrue extends ActiveRecord
{
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'jpeg', 'gif'], 'on' => 'pic'],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'jpeg', 'gif'], 'maxFiles' => 4, 'on' => 'pics'],
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
                return ['status' => 1, 'images' => $pic_list, 'img_path' => '/'.$file_path];
            } else {
                return ['status' => 401, 'info' => '上传失败'];
            }

        } else {
            return ['status' => 401, 'info' => implode(',', $this->getFirstErrors())];
        }
    }


    public function multiUpload()
    {
        $err = "";
        $pic_list = [];
        $this->setScenario('pics');
        $base_path = "/uploads/images/".date('Ymd').'/';
        if($this->validate()){
            var_dump('aaa');
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
            var_dump('aaaa12');
            return ['status' => 401, 'info' => implode(',', $this->getFirstErrors())];
        }
    }


    /**
     * @param $pic_id 图片ID
     * 获取图片信息
     */
    public static function getPictrueById($pic_id)
    {
        $pictrue_info = self::find()
            ->where(['id' => $pic_id])
            ->select(['pic_name', 'pic_path', 'pic_size'])
            ->asArray()
            ->one();

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        if($pictrue_info['pic_path']){
            $pictrue_info['pic_path'] = $protocol.$_SERVER['HTTP_HOST'].CommonHelper::getPicPath($pictrue_info['pic_path']);
        }

        return $pictrue_info;

    }

    /**
     * @param Array $pic_id 图片id
     * @param bool $domain 图片路径是否需要带域名
     * 获取 图片列表
     */
    public static function getPictrueList($pic_id)
    {
        if(empty($pic_id) || !is_array($pic_id)){
            return [];
        }

        $pic_list =self::find()
            ->where(['in', 'id', $pic_id])
            ->select(['id', 'pic_name', 'pic_path', 'pic_size'])
            ->indexBy('id')
            ->asArray()
            ->all();

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        if(count($pic_list)){
            foreach ($pic_list as &$pic){
                $pic['pic_path'] = $protocol.$_SERVER['HTTP_HOST'].CommonHelper::getPicPath($pic['pic_path']);
            }
        }

        return $pic_list;
    }
}