<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/21
 * Time: 21:02.
 */

namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Banner;
use SYS_ADMIN\models\Pictrue;

class BannerController extends CommonController
{
    /**
     * 获取轮播图列表.
     */
    public function actionIndex()
    {
        $room_id = \Yii::$app->request->get('room_id');
        $bannerType = \Yii::$app->request->get('bannerType', ConStatus::$BANNER_TYPE_SYS);
        if (\Yii::$app->request->get('api')) {
            if (!empty($room_id) && !CommonHelper::checkRoomId($room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
            }

            if (!in_array($bannerType, [ConStatus::$BANNER_TYPE_SYS, ConStatus::$BANNER_TYPE_ROOM])) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
            }

            if (!$this->isAdmin) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
            }

            $query = Banner::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->andWhere(['banner_type' => $bannerType]);

            if (!empty($room_id)) {
                $query->andWhere(['room_id' => $room_id]);
            }

            $lists = $query->asArray()->all();
            $picIds = array_column($lists, 'cover_img');
            $picLists = Pictrue::getPictrueList($picIds);

            foreach ($lists as &$item) {
                $item['cover'] = $picLists[$item['cover_img']]['pic_path'] ?? '';
                $item['created_at'] = date('Y-m-d H:i', strtotime($item['created_at']));
                $item['status'] = ConStatus::$STATUS_LIST[$item['status']];
            }

            return $this->successInfo($lists);
        } else {
            return $this->render('list', ['room_id' => $room_id]);
        }
    }


    /**
     * 获取轮播图列表.
     */
    public function actionRoom()
    {
        $room_id = \Yii::$app->request->get('room_id');
        $bannerType = \Yii::$app->request->get('bannerType', ConStatus::$BANNER_TYPE_ROOM);

        if (empty($room_id) || empty($bannerType)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (!empty($room_id) && !CommonHelper::checkRoomId($room_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (!in_array($bannerType, [ConStatus::$BANNER_TYPE_SYS, ConStatus::$BANNER_TYPE_ROOM])) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }


        $query = Banner::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['banner_type' => $bannerType])
            ->andWhere(['room_id' => $room_id]);

        $lists = $query->asArray()->all();

        $picIds = array_column($lists, 'cover_img');
        $picLists = Pictrue::getPictrueList($picIds);

        foreach ($lists as &$item) {
            $item['cover'] = $picLists[$item['cover_img']]['pic_path'] ?? '';
            $item['created_at'] = date('Y-m-d H:i', strtotime($item['created_at']));
            $item['status'] = ConStatus::$STATUS_LIST[$item['status']];
        }

        return $this->successInfo($lists);
    }

    public function actionOne()
    {
        $id = \Yii::$app->request->get('id');
        $room_id = \Yii::$app->request->get('room_id');
        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (!empty($room_id) && !CommonHelper::checkRoomId($room_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $info = Banner::findOne($id)->toArray();
        if (!CommonHelper::checkRoomId($info['room_id'])) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        //获取图片信息
        $picInfo = Pictrue::getPictrueById($info['cover_img']);
        $info['cover'] = $picInfo['pic_path'] ?? '';
        return $this->successInfo($info);
    }

    /**
     * 删除轮播图
     */
    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        $room_id = \Yii::$app->request->get('room_id');

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (!empty($room_id) && !CommonHelper::checkRoomId($room_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model = Banner::findOne($id);
        $room_id = array_keys($this->user_room);
        if (!$this->isAdmin && !in_array($model->room_id, $room_id)) {
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
     * 保存
     */
    public function actionSave()
    {
        $id = \Yii::$app->request->post('id');
        $banner_type = \Yii::$app->request->post('banner_type', ConStatus::$BANNER_TYPE_SYS);
        $title = \Yii::$app->request->post('title');
        $cover_img = \Yii::$app->request->post('cover_img');
        $remarks = \Yii::$app->request->post('remarks');
        $sort_num = \Yii::$app->request->post('sort_num');
        $status = \Yii::$app->request->post('status', ConStatus::$STATUS_ENABLE);
        $room_id = \Yii::$app->request->post('room_id');
        $links = \Yii::$app->request->post('links');

        $bannerModel = new Banner();
        $bannerModel->attributes = \Yii::$app->request->post();
        if (!$bannerModel->validate()) {
            $errors = implode($bannerModel->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!$this->isAdmin && !array_key_exists($room_id, $this->user_room)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (!empty($id)) { // 更新
            $bannerModel = Banner::findOne($id);
            if (!CommonHelper::checkRoomId($bannerModel->room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }
        }

        $bannerModel->title = $title;
        $bannerModel->banner_type = $banner_type;
        $bannerModel->cover_img = $cover_img;
        $bannerModel->remarks = $remarks;
        $bannerModel->status = $status;
        $bannerModel->sort_num = $sort_num;
        $bannerModel->links = $links;
        $bannerModel->room_id = $room_id;

        if (!$bannerModel->save()) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
        } else {
            return $this->successInfo(ConStatus::$STATUS_SUCCESS);
        }
    }
}
