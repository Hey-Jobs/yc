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
use SYS_ADMIN\models\Video;

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
        if(\Yii::$app->request->get('api')){
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
        if(empty($video_id)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, "参数错误");
        }

        $model = Video::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['id' => $video_id]);

        $info = $model->asArray()->one();
        if(!empty($info)){
            if(!$this->isAdmin && !array_keys($info['room_id'], $this->user_room)){
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, "参数错误");
            }

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

        if(empty($id)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        $model = Video::findOne(['id' => $id]);
        if(empty($model)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        if(!$this->isAdmin && !array_keys($model->room_id, $this->user_room)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, "参数错误");
        }

        $model->status = ConStatus::$STATUS_DELETED;
        $model->updated_at = time();
        if($model->save()){
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
        $sort_num = \Yii::$app->request->post('sort_num', 10);
        $status = \Yii::$app->request->post('status', 1);

        $model = new Video();
        $model->attributes = $data;
        if(!$model->validate()){
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if(!empty($id)){
            $model = Video::findOne($id);
            if(empty($model)){
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
            }

            if(!CommonHelper::checkRoomId($model->room_id)){
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }

        } else {
            $model->click_num = 1;
            $model->created_at = time();
        }

        $model->video_name = $video_name;
        $model->video_url = $video_url;
        $model->sort_num = $sort_num;
        $model->room_id = $room_id;
        $model->status = $status;
        $model->updated_at = time();

        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }

    }
}