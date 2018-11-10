<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:52
 */

namespace SYS_ADMIN\controllers;


use common\models\User;
use SYS_ADMIN\components\BaseDataBuilder;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\LiveRoomExtend;
use SYS_ADMIN\models\Pictrue;
use yii\web\UploadedFile;

class LiveController extends CommonController
{

    public function actionIndex()
    {

        if(\Yii::$app->request->get('api')){

            // 管理员
            $model = LiveRoom::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED]);

            if(!$this->isAdmin){
                $model->andWhere(['user_id', \Yii::$app->user->id]);
            }

            $live_list = $model->asArray()->all();
            if(count($live_list) > 0){
                $picid_list = array_column($live_list, 'logo_img');
                $userid_list = array_column($live_list, 'user_id');
                $user_list = User::find()
                    ->where(['in', 'id', $userid_list])
                    ->select(['name', 'id'])
                    ->indexBy('id')
                    ->asArray()
                    ->all(); // 所属用户


                $pic_list = Pictrue::find()
                    ->where(['in', 'id', $picid_list])
                    ->indexBy('id')
                    ->asArray()
                    ->all(); // logo

                foreach ($live_list as &$live){
                    $live['pic_name'] = isset($pic_list[$live['logo_img']]) ? $pic_list[$live['logo_img']]['pic_name'] : "";
                    $live['pic_path'] = isset($pic_list[$live['logo_img']]) ?  $pic_list[$live['logo_img']]['pic_path'] : "";
                    $live['pic_size'] = isset($pic_list[$live['logo_img']]) ?  $pic_list[$live['logo_img']]['pic_size'] : "";
                    $live['uname'] = $user_list[$live['user_id']]['name'];
                }

            }

            $this->successInfo($live_list);
        } else {
            return $this->render('list');
        }
    }

    /**
     * 直播间 基础信息
     */
    public function actionBaseInfo()
    {
        $room_info = [];
        $pic_info = [];
        $user_room = LiveRoom::getUserRoomId();
        $room_id = \Yii::$app->request->get('id');

        if(!array_key_exists($room_id, $user_room) && !$this->isAdmin){ // 商家查看自己资料
            \Yii::$app->getSession()->setFlash('info', 'This is the message');
        }

        $room_info = LiveRoom::findOne(['id' => $room_id]);
        if($room_info){
            $room_info = $room_info ->toArray();
            $room_name = LiveRoom::getRoomNameById($room_id);
            if(isset($room_info['logo_img'])){ // 封面图片
                $pic_info = Pictrue::getPictrueById($room_info['logo_img']);
            }
        }


        return $this->render("base", [
            'info' => $room_info,
            'room_id' => $room_id,
            'pic_info' => $pic_info,
            'is_admin' => $this->isAdmin,
        ]);
    }

    /**
     * @return string|void
     * 直播间 扩展信息
     */
    public function actionExtInfo()
    {
        $pic_info = [];
        $user_room = LiveRoom::getUserRoomId();
        $room_id = \Yii::$app->request->get('id');

        if(!array_key_exists($room_id, $user_room) && !$this->isAdmin){ // 商家查看自己资料
            return $this->errorInfo(400, "参数错误");
        }

        $room_info = LiveRoomExtend::getExtRoomInfo($room_id);
        $room_name = LiveRoom::getRoomNameById($room_id);
        if(isset($room_info['cover_img'])){ // 封面图片
            $pic_info = Pictrue::getPictrueById($room_info['cover_img']);
        }

        return $this->render("ext", [
            'info' => $room_info,
            'room_name' => $room_name,
            'pic_info' => $pic_info,
            'room_id' => $room_id,
            'is_admin' => $this->isAdmin,
        ]);
    }


    /**
     * 扩展信息保存
     */
    public function actionSaveExtInfo()
    {
        $id = \Yii::$app->request->post('id');
        $cover_img = \Yii::$app->request->post('cover_img');
        $introduce = \Yii::$app->request->post('introduce');
        $content = \Yii::$app->request->post('content');

        $user_room = LiveRoom::getRoomId();
        if($user_room > 0 && $id != $user_room){ // 普通人编辑直播间不一致
            return $this->errorInfo(400, "参数错误");
        }

        if($user_room == 0 && empty($id)){ // 超级管理员
            return $this->errorInfo(400, "参数错误");
        }


        $model = LiveRoomExtend::findOne(['room_id' => $id]);
        if(empty($model)){
            $model = new LiveRoomExtend();
            $model->created_at = time();
            $model->room_id = $id;
        }

        $model->content = $content;
        $model->introduce = $introduce;
        $model->cover_img = $cover_img;
        $model->updated_at = time();

        if(isset($_FILES['pcover_img']) && !empty($_FILES['pcover_img']['name'])){ // 新上传图片
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('pcover_img');
            $img_list = $picModel->upload();
            if(isset($img_list['images'])){
                $model->cover_img = $img_list['images'];
            } else {
                return $this->errorInfo(400, $img_list['info']);
            }
        }

        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }
    }

    /**
     * @return array|void
     * 删除直播间
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('rid');
        $id = intval($id);
        if(empty($id)){
            return $this->errorInfo(400, "参数错误");
        }

        if(LiveRoom::getRoomId() && LiveRoom::getRoomId() !== $id){
            return $this->errorInfo(400, "无权操作");
        }

        $where['id'] = $id;
        $model = LiveRoom::find()
            ->where($where)
            ->andWhere(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->one();

        $model->status = ConStatus::$STATUS_DELETED;
        $model->updated_at = time();

        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, "操作失败，请稍后重试");
        }
    }


}