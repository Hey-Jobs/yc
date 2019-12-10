<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:05
 */

namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\SearchWidget;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Video;
use yii\web\UploadedFile;

/**
 * Class VideoController
 * @package SYS_ADMIN\controllers
 * 视频管理
 */
class VideoController extends CommonController
{
    /**
     * 视频列表
     */
    public function actionList()
    {
        if (\Yii::$app->request->get('api')) {
            $video_list = Video::getVideoList();
            return $this->successInfo($video_list);
        } else {
            $room_html = SearchWidget::instance()->liveRoom('room_id');
            return $this->render('list', [
                'room_html' => $room_html
            ]);
        }

    }


    public function actionInfo()
    {
        $video_id = \Yii::$app->request->post('id');
        $video_id = intval($video_id);
        if (empty($video_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, "参数错误");
        }

        $model = Video::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['id' => $video_id]);

        $info = $model->asArray()->one();
        if (!empty($info)) {
            /*if(!$this->isAdmin && !array_keys($info['room_id'], $this->user_room)){
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, "参数错误");
            }*/
            if (!CommonHelper::checkRoomId($info['room_id'])) {
                return $this->render('/site/error', [
                    "message" => ConStatus::$ERROR_PARAMS_MSG,
                    "name" => "编辑直播间",
                ]);
            }

//            $info['pic_path'] = "";
//            if(!empty($info['cover_img'])){
//                $pic_info = Pictrue::getPictrueById($info['cover_img']);
//                $info['pic_path'] = $pic_info['pic_path'] ?? "";
//            }

            return $this->successInfo($info);

        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }
    }

    /**
     * @return array|void
     * 删除
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('id');
        $id = intval($id);

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        $model = Video::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        if (!$this->isAdmin && !array_key_exists($model->room_id, $this->user_room)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, "参数错误");
        }

        $model->status = ConStatus::$STATUS_DELETED;
        $model->updated_at = time();
        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, "操作失败，请稍后重试");
        }
    }

    /**
     * 保存视频信息
     */
    public function actionSave()
    {
        $data = \Yii::$app->request->post();
        $id = \Yii::$app->request->post('id');
        $room_id = \Yii::$app->request->post('room_id');
        $video_name = \Yii::$app->request->post('video_name');
        $video_url = \Yii::$app->request->post('video_url');
        $cover_img = \Yii::$app->request->post('cover_img');
        $video_length = \Yii::$app->request->post('video_length');
        $sort_num = \Yii::$app->request->post('sort_num', 10);
        $status = \Yii::$app->request->post('status', 1);

        $model = new Video();
        $model->attributes = $data;
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!empty($id)) {
            $model = Video::findOne($id);
            if (empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
            }

            if (!CommonHelper::checkRoomId($model->room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }

        } else {
            $model->click_num = 1;
            $model->created_at = time();
        }

        if(isset($_FILES['file']) && !empty($_FILES['file']['name'])){
            $uploadFile = UploadedFile::getInstanceByName('file');
            $base_path = "uploads/".date('Ymd').'/';
            $file_name = md5(uniqid().mt_rand(100000, 9999999)). '.' . $uploadFile->extension;
            $file_path = $base_path.$file_name;
            $video_path = CommonHelper::OssUploadFile($uploadFile->tempName, $file_path, ConStatus::$OSS_BASE_DIR);
            if(!empty($video_path)){
                $video_url = $video_path;
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_OSS_UPLOAD, ConStatus::$ERROR_OSS_UPLOAD_MSG);
            }
        }

        if (empty($video_url)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model->video_name = $video_name;
        $model->video_url = $video_url;
        $model->sort_num = $sort_num;
        $model->room_id = $room_id;
        $model->status = $status;
        $model->video_length = $video_length;
        $model->cover_img = $cover_img;
        $model->updated_at = time();

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }

    }
}