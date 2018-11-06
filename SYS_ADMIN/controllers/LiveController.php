<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:52
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;

class LiveController extends CommonController
{

    public function actionIndex()
    {
        if(LiveRoom::getRoomId()){ // 商家 资料
            $this->redirect("/live/info");
        }

        // 管理员
        if(\Yii::$app->request->get('api')){
            ini_set('display_errors', 'on');
            ini_set('error_reporting', E_ALL);
            $live_list = LiveRoom::find()
                ->alias('r')
                ->innerJoin('sys_user u', 'u.id = r.user_id')
                ->leftJoin('sys_pictrue p', 'p.id = r.logo_img')
                ->where(['<>', 'r.status', 0])
                ->select(['r.*', 'u.name as uname', 'p.pic_name', 'p.pic_path', 'p.pic_size'])
                ->asArray()
                ->all();
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
        $pic_logo = [];
        $pic_cover = [];
        $room_extend = [];
        $room_id = \Yii::$app->request->get('id');

        if(LiveRoom::getRoomId()){ // 商家查看自己资料
            $room_id = LiveRoom::getRoomId();
        }

        if(!empty($room_id)){
            $room_info = LiveRoom::getRoomInfo($room_id);
        }


        return $this->render("base", [
            'info' => $room_info,
            'room_id' => $room_id,
        ]);
    }

    /**
     * @return string|void
     * 直播间 扩展信息
     */
    public function actionExtInfo()
    {
        $room_info = [];
        $pic_logo = [];
        $pic_cover = [];
        $room_extend = [];
        $room_id = \Yii::$app->request->get('id');

        if(LiveRoom::getRoomId()){ // 商家查看自己资料
            $room_id = LiveRoom::getRoomId();
        }

        if(!empty($room_id)){
            $room_info = LiveRoom::getRoomInfo($room_id);
        }


        return $this->render("ext", [
            'info' => $room_info,
            'room_id' => $room_id,
        ]);
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
            ->andWhere(['<>', 'status', 0])
            ->one();

        $model->status = 0;
        $model->updated_at = time();

        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, "操作失败，请稍后重试");
        }
    }


}