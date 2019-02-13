<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/23
 * Time: 22:06
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Category;

class CategoryController extends CommonController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->get('api')) {
            $lists = Category::find()
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

        $info = Category::findOne($id)->toArray();
        return $this->successInfo($info);
    }

    public function actionSave()
    {
        $id = \Yii::$app->request->post('id');
        $title = \Yii::$app->request->post('title');
        $status = \Yii::$app->request->post('status', ConStatus::$STATUS_ENABLE);
        $sort_num = \Yii::$app->request->post('sort_num');

        $model = new Category();
        $model->attributes = \Yii::$app->request->post();

        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!empty($id)) { // 更新
            $model = Category::findOne($id);
            if (empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }
        }

        $model->title = $title;
        $model->status = $status;
        $model->sort_num = $sort_num;

        if (!$model->save()) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
        } else {
            return $this->successInfo(ConStatus::$STATUS_SUCCESS);
        }
    }

    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model = Category::findOne($id);
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
}