<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:51
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\BaseDataBuilder;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\ShoppingMall;
use Yii;

class ShoppingMallController extends CommonController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('api')) {
            $roomPairs = BaseDataBuilder::instance('LiveRoom');
            $list = ShoppingMall::find()
                ->select(['*'])
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->filterWhere(['user_id' => isset($this->isAdmin) ? null : \Yii::$app->user->id])
                ->asArray()
                ->all();

            foreach ($list as $key => $row) {
                $list[$key]['room_name'] = $roomPairs[$row['room_id']] ?? '';
                $list[$key]['status_name'] = ConStatus::$STATUS_LIST[$row['status']] ?? '';
            }

            return $this->successInfo($list);
        } else {
            return $this->render('list');
        }
    }

    /**
     * get one
     */
    public function actionOne()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->errorInfo(400, 'Id is not empty');
        }

        $list = ShoppingMall::findOne($id)->toArray();
        return $this->successInfo($list);
    }

    /**
     * status
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');

        if (empty($id)) {
            return $this->errorInfo(400, 'Id is not empty');
        }

        $shoreMapM = ShoppingMall::findOne($id);
        $shoreMapM->status = ConStatus::$STATUS_DELETED;
        if (!$shoreMapM->save()) {
            return $this->errorInfo('400', 'error');
        }

        return $this->successInfo(200);
    }

    /**
     * status
     */
    public function actionPut()
    {
        $id = Yii::$app->request->post('id');

        if (empty($id)) {
            return $this->errorInfo(400, 'Id is not empty');
        }

        $shoreMapM = ShoppingMall::findOne($id);
        if (ConStatus::$STATUS_ENABLE == $shoreMapM->status) {
            $shoreMapM->status = ConStatus::$STATUS_DISABLE;
        } else {
            $shoreMapM->status = ConStatus::$STATUS_ENABLE;
        }

        if (!$shoreMapM->save()) {
            return $this->errorInfo('400', 'error');
        }

        return $this->successInfo(200);
    }

    /**
     * save
     */
    public function actionSave()
    {
        $id = Yii::$app->request->post('id');
        $roomId = Yii::$app->request->post('room_id');
        $title = Yii::$app->request->post('title');
        $subTitle = Yii::$app->request->post('sub_title');
        $introduction = Yii::$app->request->post('introduction');
        $imageSrc = Yii::$app->request->post('image_src');

        $model = new ShoppingMall();
        $model->attributes = (\Yii::$app->request->post());
        if (!$model->validate()) {
            $errors = array_values($model->getFirstErrors())[0];
            return $this->errorInfo(400, $errors);
        }

        $liveUserPairs = BaseDataBuilder::instance('LiveRoomUser');
        if ($id) {
            $shoreMapM = ShoppingMall::findOne($id);
            $shoreMapM->room_id = $roomId;
            $shoreMapM->user_id = $liveUserPairs[$roomId] ?? '';
            $shoreMapM->title = $title;
            $shoreMapM->sub_title = $subTitle;
            $shoreMapM->introduction = $introduction;
            $shoreMapM->image_src = $imageSrc;
        } else {
            $shoreMapM = new ShoppingMall();
            $shoreMapM->room_id = $roomId;
            $shoreMapM->user_id = $liveUserPairs[$roomId] ?? '';
            $shoreMapM->title = $title;
            $shoreMapM->sub_title = $subTitle;
            $shoreMapM->introduction = $introduction;
            $shoreMapM->image_src = $imageSrc;
            $shoreMapM->status = ConStatus::$STATUS_ENABLE;
        }

        if (!$shoreMapM->save()) {
            return $this->errorInfo('400', 'error');
        }

        return $this->successInfo(200);
    }
}