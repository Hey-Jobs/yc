<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/29
 * Time: 23:05
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\SearchWidget;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Snapshot;

class SnapshotController extends CommonController
{

    /**
     * 截图列表
     */
    public function actionList()
    {
        if (\Yii::$app->request->get('api')) {
            $video_list = Snapshot::getSnapshotList();
            return $this->successInfo($video_list);
        } else {
            $room_html = SearchWidget::instance()->liveRoom('room_id');
            return $this->render('list', [
                'room_html' => $room_html
            ]);
        }
    }


    /**
     * 上传
     */
    public function actionUpload()
    {
        $data = \Yii::$app->request->post();
        $room_id = \Yii::$app->request->post('room_id');
        $cover = \Yii::$app->request->post('cover');
        $title = \Yii::$app->request->post('title');
        $remark = \Yii::$app->request->post('remark');
        $sort_num = \Yii::$app->request->post('sort_num', 10);

        $model = new Snapshot();
        $model->attributes = $data;
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        $model->cover = $cover;
        $model->title = $title;
        $model->remark = $remark;
        $model->sort_num = $sort_num;
        $model->room_id = $room_id;

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }
    }


    public function actionInfo()
    {
        $video_id = \Yii::$app->request->post('id');
        $video_id = intval($video_id);
        if (empty($video_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, "参数错误");
        }

        $model = Snapshot::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['id' => $video_id]);

        $info = $model->asArray()->one();
        if (!empty($info)) {
            if(!CommonHelper::checkRoomId($info['room_id'])){
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, "参数错误");
            }

            $info['pic_path'] = "";
            if(!empty($info['cover'])){
                $pic_info = Pictrue::getPictrueById($info['cover']);
                $info['pic_path'] = $pic_info['pic_path'] ?? "";
            }

            return $this->successInfo($info);

        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }
    }

    /**
     * 保存信息
     */
    public function actionSave()
    {
        $data = \Yii::$app->request->post();
        $id = \Yii::$app->request->post('id');
        $room_id = \Yii::$app->request->post('room_id');
        $title = \Yii::$app->request->post('title');
        $cover = \Yii::$app->request->post('cover');
        $remark = \Yii::$app->request->post('remark');
        $sort_num = \Yii::$app->request->post('sort_num', 10);
        $status = \Yii::$app->request->post('status', 1);

        $model = Snapshot::findOne($id);
        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model->attributes = $data;
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!CommonHelper::checkRoomId($model->room_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model->title = $title;
        $model->remark = $remark;
        $model->sort_num = $sort_num;
        $model->room_id = $room_id;
        $model->status = $status;
        $model->cover = $cover;

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
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

        $model = Snapshot::findOne($id);
        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        if (!$this->isAdmin && !array_key_exists($model->room_id, $this->user_room)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, "参数错误");
        }

        $model->status = ConStatus::$STATUS_DELETED;
        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, "操作失败，请稍后重试");
        }
    }

}