<?php


namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Campus;
use SYS_ADMIN\models\Pictrue;
use yii\web\UploadedFile;

class CampusController extends CommonController
{

    /**
     * 查看信息
     */
    public function actionIndex()
    {
        $user_id = \Yii::$app->user->id;
        $key = decoct($user_id) * ConStatus::$USER_SECRET_KEY;
        $preview = CommonHelper::getDomain(). "/front/#/campus?key=".$key;
        $model = Campus::findOne(['user_id' => $user_id]);
        $info = [];
        $logo_info = [];
        $bg_img_info = [];

        if ($model) {
            $info = $model->toArray();
        }

        // 获取封面图
        if (!empty($info['cover_id'])) {
            $logo_info = Pictrue::getPictrueById($info['cover_id']);
        }

        // 获取背景图
        if (isset($info['bg_cover_id'])) {
            $bg_img_info = Pictrue::getPictrueById($info['bg_cover_id']);
        }

        $info['preview'] = $preview;
        $title = "校园主页";

        return $this->render('index', [
            'info' => $info,
            'title' => $title,
            'logo_info' => $logo_info,
            'bg_img_info' => $bg_img_info
        ]);
    }

    /**
     * 保存信息
     */
    public function actionSave()
    {
        $user_id = \Yii::$app->user->id;
        $title = \Yii::$app->request->post('title');
        $cover_id = \Yii::$app->request->post('cover_id');
        $bg_cover_id = \Yii::$app->request->post('bg_cover_id');

        $model = Campus::findOne(['user_id' => $user_id]);
        if (empty($model)) {
            $model = new Campus();
        }

        $model->user_id = $user_id;
        $model->title = $title;
        $model->cover_id = $cover_id;
        $model->bg_cover_id = $bg_cover_id;

        if (isset($_FILES['logo_img']) && !empty($_FILES['logo_img']['name'])) {
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('logo_img');
            $img_list = $picModel->upload();
            if (isset($img_list['images'])) {
                $model->cover_id = $img_list['images'];
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_Upload, $img_list['info']);
            }
        }

        if (isset($_FILES['bg_img']) && !empty($_FILES['bg_img']['name'])) {
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('bg_img');
            $img_list = $picModel->upload();
            if (isset($img_list['images'])) {
                $model->bg_cover_id = $img_list['images'];
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_Upload, $img_list['info']);
            }
        }

        if (!$model->save()) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
        } else {
            return $this->successInfo(ConStatus::$STATUS_SUCCESS);
        }
    }
}