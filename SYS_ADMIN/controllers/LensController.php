<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 0:56.
 */

namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\SearchWidget;
use SYS_ADMIN\models\Lens;
use SYS_ADMIN\models\Pictrue;
use yii\web\UploadedFile;

/**
 * Class LensController.
 */
class LensController extends CommonController
{
    /**
     * 镜头列表.
     */
    public function actionList()
    {
        if (\Yii::$app->request->get('api')) {
            $lens = Lens::getLensList();

            return $this->successInfo($lens);
        } else {
            return $this->render('list');
        }
    }

    /**
     * 获取镜头信息.
     */
    public function actionInfo()
    {
        $lens_info = [];
        $pic_info = [];
        $room_id = 0;
        $id = \Yii::$app->request->get('id');
        $id = intval($id);

        $model = new Lens();
        $title = $id ? '编辑镜头' : '新增镜头';

        if (!empty($id)) { // 编辑资料
            $model = Lens::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->andWhere(['id' => $id]);

            $lens_info = $model->asArray()->one();
            if ($lens_info) {
                if (!$this->isAdmin && !array_key_exists($lens_info['room_id'], $this->user_room)) {
                    return $this->render('/site/error', [
                        'name' => $title,
                        'message' => '访问错误',
                    ]);
                }

                $room_id = $lens_info['room_id'];
            }

            if (isset($lens_info['cover_img'])) { // 封面图信息
                $pic_info = Pictrue::find()
                    ->where(['id' => $lens_info['cover_img']])
                    ->asArray()
                    ->one();
            }
        }

        $room_html = SearchWidget::instance()->liveRoom('room_id', $room_id);

        return $this->render('detail', [
            'info' => $lens_info,
            'model' => $model,
            'pic_info' => $pic_info,
            'name' => $title,
            'room_html' => $room_html,
        ]);
    }

    /**
     * 镜头编辑.
     */
    public function actionSave()
    {
        $id = \Yii::$app->request->post('id');
        $lens_name = \Yii::$app->request->post('lens_name');
        $room_id = \Yii::$app->request->post('room_id');
        $cover_img = \Yii::$app->request->post('cover_img', '');
        $online_url = \Yii::$app->request->post('online_url');
        $playback_url = \Yii::$app->request->post('playback_url');
        $bgm_url = \Yii::$app->request->post('bgm_url');
        $marvellous_url = \Yii::$app->request->post('marvellous_url');
        $status = \Yii::$app->request->post('status');
        $sort_num = \Yii::$app->request->post('sort_num');
        $online_cover_url = \Yii::$app->request->post('online_cover_url');
        $stream_name = \Yii::$app->request->post('stream_name');
        $app_name = \Yii::$app->request->post('app_name');

        $model = new Lens();
        $model->attributes = \Yii::$app->request->post();
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");

            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!$this->isAdmin && !array_key_exists($room_id, $this->user_room)) {
            if (empty($room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, '参数错误');
            }
        }

        if (!empty($id)) { // 更新
            $model = Lens::find()
                ->where(['id' => $id])
                ->andWhere(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->one();
        } else { // 新增
            if (empty($room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, '参数错误');
            }

            $model->click_num = 1;
            $model->created_at = time();
        }

        $model->lens_name = $lens_name;
        $model->online_url = $online_url;
        $model->playback_url = $playback_url;
        $model->bgm_url = $bgm_url;
        $model->marvellous_url = $marvellous_url;
        $model->status = $status;
        $model->sort_num = $sort_num;
        $model->updated_at = time();
        $model->cover_img = $cover_img;
        $model->room_id = $room_id;
        $model->online_cover_url = $online_cover_url;
        $model->stream_name = $stream_name;
        $model->app_name = $app_name;

        if (isset($_FILES['pcover_img']) && !empty($_FILES['pcover_img']['name'])) {
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('pcover_img');
            $img_list = $picModel->upload();
            if (isset($img_list['images'])) {
                $model->cover_img = $img_list['images'];
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_Upload, $img_list['info']);
            }
        }

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }
    }

    /**
     * 镜头删除.
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('id');
        $id = intval($id);

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, '参数错误');
        }

        $model = Lens::find()
            ->where(['id' => $id])
            ->andWhere(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->one();

        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_NONE, '参数错误');
        }

        if (!$this->isAdmin && array_key_exists($model->room_id, $this->user_room)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, '参数错误');
        }

        $model->status = ConStatus::$STATUS_DELETED;
        $model->updated_at = time();

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, '操作失败，请稍后重试');
        }
    }
}
