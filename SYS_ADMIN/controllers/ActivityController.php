<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/26
 * Time: 23:15
 */

namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Activity;

/**
 * Class ActivityController
 * @package SYS_ADMIN\controllers
 * 活动管理
 */
class ActivityController extends CommonController
{
    /**
     * @return array|string
     * 获取列表
     */
    public function actionIndex()
    {
        if (\Yii::$app->request->get('api')) {
            $lists = Activity::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->asArray()
                ->all();

            foreach ($lists as &$item) {
                $item['created_at'] = date('Y-m-d H:i', strtotime($item['created_at']));
                $item['status'] = ConStatus::$STATUS_LIST[$item['status']];
            }

            return $this->successInfo($lists);
        } else {
            return $this->render('list');
        }
    }


    public function actionOne()
    {
        $id = \Yii::$app->request->get('id');

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $info = Activity::findOne($id)->toArray();
        return $this->successInfo($info);
    }

    /**
     * @return array|void
     * 删除活动
     */
    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model = Activity::findOne($id);
        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model->status = ConStatus::$STATUS_DELETED;
        if (!$model->save()) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
        } else {
            return $this->successInfo(ConStatus::$STATUS_SUCCESS);
        }
    }

    /**
     * @return array|void
     * 更新活动
     */
    public function actionSave()
    {
        $id = \Yii::$app->request->post('id');
        $title = \Yii::$app->request->post('title');
        $activity_url = \Yii::$app->request->post('activity_url');
        $activity_time = \Yii::$app->request->post('activity_time');
        $status = \Yii::$app->request->post('status', ConStatus::$STATUS_ENABLE);
        $sort_num = \Yii::$app->request->post('sort_num');

        $model = new Activity();
        $model->attributes = \Yii::$app->request->post();

        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!empty($id)) { // 更新
            $model = Activity::findOne($id);
            if (empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }
        }

        $model->title = $title;
        $model->activity_url = $activity_url;
        $model->activity_time = $activity_time;
        $model->status = $status;
        $model->sort_num = $sort_num;

        if (!$model->save()) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
        } else {
            return $this->successInfo(ConStatus::$STATUS_SUCCESS);
        }
    }


}