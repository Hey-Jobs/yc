<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:52.
 */

namespace SYS_ADMIN\controllers;

use abei2017\wx\Application;
use common\models\User;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\SearchWidget;
use SYS_ADMIN\models\Category;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\LiveRoomExtend;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\RoomTemplate;
use yii\web\UploadedFile;

class LiveController extends CommonController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->get('api')) {
            $room_id = array_keys($this->user_room);
            // 管理员
            $model = LiveRoom::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED]);

            if (!$this->isAdmin) {
                $model->andWhere(['in', 'id', $room_id]);
            }

            $live_list = $model->asArray()->all();
            if (count($live_list) > 0) {
                $picid_list = array_column($live_list, 'logo_img');
                $userid_list = array_column($live_list, 'user_id');
                $user_list = User::find()
                    ->where(['in', 'id', $userid_list])
                    ->select(['name', 'id'])
                    ->indexBy('id')
                    ->asArray()
                    ->all(); // 所属用户

                $pic_list = Pictrue::getPictrueList($picid_list);
                foreach ($live_list as &$live) {
                    $live['pic_path'] = isset($pic_list[$live['logo_img']]) ? $pic_list[$live['logo_img']]['pic_path'] : '';
                    $live['uname'] = $user_list[$live['user_id']]['name'] ?? '';
                    $live['status'] = ConStatus::$STATUS_LIST[$live['status']] ?? '';
                }
            }

            $this->successInfo($live_list);
        } else {
            return $this->render('list', [
                'is_admin' => $this->isAdmin,
            ]);
        }
    }

    /**
     * 直播间 基础信息.
     */
    public function actionBaseInfo()
    {
        $room_info = [];
        $pic_info = [];
        $id = \Yii::$app->request->get('id', 0);
        $user_id = 0;

        if (empty($id) && !$this->isAdmin) { // 非管理员，无权新增
            return $this->render('/site/error', [
                'message' => ConStatus::$ERROR_PARAMS_MSG,
                'name' => '编辑直播间',
            ]);
        }

        if (!empty($id)) { // 编辑
            $room_info = LiveRoom::findOne(['id' => $id]);
            $room_info = $room_info->toArray();
            if (!CommonHelper::checkRoomId($room_info['id'])) {
                return $this->render('/site/error', [
                    'message' => ConStatus::$ERROR_PARAMS_MSG,
                    'name' => '编辑直播间',
                ]);
            }

            if (!empty($room_info['logo_img'])) { // 封面图片
                $pic_info = Pictrue::getPictrueById($room_info['logo_img']);
            }
            $user_id = $room_info['user_id'];
        }

        $title = empty($id) ? '新增直播间' : '编辑直播间';
        $user_html = SearchWidget::instance()->userList('user_id', $user_id);
        // 模板列表
        $templateList = RoomTemplate::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->asArray()
            ->all();

        // 分类
        $category = Category::getCategoryList();
        if (empty($room_info['mini_code'])) {
            //小程序码
            $qrcode = (new Application(['conf' => \Yii::$app->params['wx']['mini']]))->driver("mini.qrcode");

            $path = "/pages/index/index?room_id=" . $room_info['id'] . "&title= " . $room_info['room_name'];

            $data = $qrcode->forever($path);
            $base_path = 'uploads/img/' . date('Ymd') . '/';
            if (!is_dir($base_path) || !is_writable($base_path)) {
                \yii\helpers\FileHelper::createDirectory($base_path, 0777, true);
            }
            // 存储小程序
            $file = $base_path . md5($room_info['id']) . '.jpg';
            file_put_contents($file, $data);

            // 保存到数据库
            $model = LiveRoom::findOne($room_info['id']);
            $model->mini_code = $file;
            $model->save();
            $room_info['mini_code'] = $file;
        }

        return $this->render('base', [
            'info' => $room_info,
            'user_html' => $user_html,
            'pic_info' => $pic_info,
            'is_admin' => $this->isAdmin,
            'title' => $title,
            'templateList' => $templateList,
            'category' => $category,
        ]);
    }

    /**
     * 基础信息保存.
     */
    public function actionSave()
    {
        $id = \Yii::$app->request->post('id');
        $user_id = \Yii::$app->request->post('user_id');
        $room_name = \Yii::$app->request->post('room_name');
        $logo_img = \Yii::$app->request->post('logo_img');
        $addr_url = \Yii::$app->request->post('addr_url', '');
        $addr = \Yii::$app->request->post('addr', '');
        $online_url = \Yii::$app->request->post('online_url', '');
        $online_cover = \Yii::$app->request->post('online_cover', '');
        $status = \Yii::$app->request->post('status', ConStatus::$STATUS_ENABLE);
        $sort_num = \Yii::$app->request->post('sort_num');
        $templet_id = \Yii::$app->request->post('templet_id');
        $category_id = \Yii::$app->request->post('category_id');

        $model = new LiveRoom();
        $model->attributes = \Yii::$app->request->post();
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");

            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!empty($id)) {
            $model = LiveRoom::findOne($id);
            if (empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
            }

            if (!CommonHelper::checkRoomId($model->id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }
        } else {
            $model->click_num = 1;
            $model->created_at = time();
        }

        $model->addr = $addr;
        $model->online_url = $online_url;
        $model->online_cover = $online_cover;
        $model->addr_url = $addr_url;
        $model->logo_img = $logo_img;
        $model->room_name = $room_name;
        $model->updated_at = time();
        $model->templet_id = $templet_id;
        $model->category_id = $category_id;

        if ($this->isAdmin) {
            $model->sort_num = $sort_num;
            $model->status = $status;
            $model->user_id = $user_id;
        }

        if (isset($_FILES['pcover_img']) && !empty($_FILES['pcover_img']['name'])) {
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('pcover_img');
            $img_list = $picModel->upload();
            if (isset($img_list['images'])) {
                $model->logo_img = $img_list['images'];
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
     * @return string|void
     *                     直播间 扩展信息
     */
    public function actionExtInfo()
    {
        $pic_info = [];
        $room_id = \Yii::$app->request->get('id');

        $room_info = LiveRoomExtend::findOne(['room_id' => $room_id]);
        if (!CommonHelper::checkRoomId($room_id)) { // 商家查看自己资料
            return $this->render('/site/error', [
                'message' => ConStatus::$STATUS_ERROR_PARAMS,
                'name' => '编辑直播间',
            ]);
        }

        $room_name = LiveRoom::getRoomNameById($room_id);
        if (!empty($room_info)) {
            $room_info = $room_info->toArray();
            if (isset($room_info['cover_img'])) { // 封面图片
                $pic_info = Pictrue::getPictrueById($room_info['cover_img']);
            }
        }

        return $this->render('ext', [
            'info' => $room_info,
            'room_name' => $room_name,
            'pic_info' => $pic_info,
            'room_id' => $room_id,
            'is_admin' => $this->isAdmin,
        ]);
    }

    /**
     * 扩展信息保存.
     */
    public function actionSaveExtInfo()
    {
        $id = \Yii::$app->request->post('id');
        $cover_img = \Yii::$app->request->post('cover_img');
        $introduce = \Yii::$app->request->post('introduce');
        $content = \Yii::$app->request->post('content');

        if (!CommonHelper::checkRoomId($id)) { // 普通人编辑直播间不一致
            return $this->errorInfo(400, '参数错误');
        }

        $model = LiveRoomExtend::findOne(['room_id' => $id]);
        if (empty($model)) {
            $model = new LiveRoomExtend();
            $model->created_at = time();
            $model->room_id = $id;
        }

        $model->content = $content;
        $model->introduce = $introduce;
        $model->cover_img = $cover_img;
        $model->updated_at = time();

        if (isset($_FILES['pcover_img']) && !empty($_FILES['pcover_img']['name'])) { // 新上传图片
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('pcover_img');
            $img_list = $picModel->upload();
            if (isset($img_list['images'])) {
                $model->cover_img = $img_list['images'];
            } else {
                return $this->errorInfo(400, $img_list['info']);
            }
        }

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }
    }

    /**
     * @return array|void
     *                    删除直播间
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('id');
        $id = intval($id);
        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, '参数错误');
        }

        if (!$this->isAdmin && !array_key_exists($id, $this->user_room)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, '参数错误');
        }

        $where['id'] = $id;
        $model = LiveRoom::find()
            ->where($where)
            ->andWhere(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->one();

        $model->status = ConStatus::$STATUS_DELETED;
        $model->updated_at = time();

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, '操作失败，请稍后重试');
        }
    }


    /**
     * 直播间轮播图
     */
    public function actionBanner()
    {
        $id = \Yii::$app->request->get('id');
        if (!CommonHelper::checkRoomId($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $title = "广告栏";
        return $this->render('banner', ['room_id' => $id, 'title' => $title]);
    }


}
