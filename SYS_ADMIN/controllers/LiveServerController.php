<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/3
 * Time: 23:53
 */

namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\LiveServer;

/**
 * Class LiveServerController
 * @package SYS_ADMIN\controllers
 * 服务器列表管理
 */
class LiveServerController extends CommonController
{
    /**
     * 服务器列表
     */
    public function actionList()
    {
        if (\Yii::$app->request->get('api')) {
            $server_list = LiveServer::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->asArray()
                ->all();

            if (count($server_list) > 0) {
                foreach ($server_list as &$item) {
                    $item['status'] = ConStatus::$STATUS_LIST[$item['status']];
                }
            }
            return $this->successInfo($server_list);
        } else {
            return $this->render('list');
        }

    }


    public function actionInfo()
    {
        $server_id = \Yii::$app->request->post('id');
        $server_id = intval($server_id);
        if (empty($server_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, "参数错误");
        }

        $info = LiveServer::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['id' => $server_id])
            ->asArray()
            ->one();

        if (!empty($info)) {
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

        $model = LiveServer::findOne($id);
        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        $model->status = ConStatus::$STATUS_DELETED;
        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, "操作失败，请稍后重试");
        }
    }

    /**
     * 保存信息
     */
    public function actionSave()
    {
        $data = \Yii::$app->request->post();
        $id = \Yii::$app->request->post('id');
        $title = \Yii::$app->request->post('title');
        $stream_addr = \Yii::$app->request->post('stream_addr');
        $oss_addr = \Yii::$app->request->post('oss_addr');
        $status = \Yii::$app->request->post('status', 1);

        $model = new LiveServer();
        $model->attributes = $data;
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!empty($id)) {
            $model = LiveServer::findOne($id);
            if (empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
            }

        }

        $model->title = $title;
        $model->stream_addr = $stream_addr;
        $model->oss_addr = $oss_addr;
        $model->status = $status;

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }

    }
}