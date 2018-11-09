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

class ShoppingMallController extends CommonController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        if (\Yii::$app->request->get('api')) {
            $roomPairs = BaseDataBuilder::instance('LiveRoom');
            $list = ShoppingMall::find()
                ->select(['*'])
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
        $id = \Yii::$app->request->get('id');
        $list = ShoppingMall::findOne($id)->toArray();
        print_r($list);
        exit;
    }

    /**
     * save
     */
    public function actionSave()
    {
        $roomId = \Yii::$app->request->post('room_id');
        $title = \Yii::$app->request->post('title');
        $subTitle = \Yii::$app->request->post('sub_title');
        $introduction = \Yii::$app->request->post('introduction');
        $imageSrc = \Yii::$app->request->post('image_src', '');

        $liveUserPairs = BaseDataBuilder::instance('LiveRoomUser');
        $shoreMapM = new ShoppingMall();
        $shoreMapM->room_id = $roomId;
        $shoreMapM->user_id = $liveUserPairs[$roomId] ?? '';
        $shoreMapM->title = $title;
        $shoreMapM->sub_title = $subTitle;
        $shoreMapM->introduction = $introduction;
        $shoreMapM->image_src = $imageSrc;
        if (!$shoreMapM->save()) {
            return $this->errorInfo('400', 'error');
        }

        return $this->successInfo(200);
    }

}