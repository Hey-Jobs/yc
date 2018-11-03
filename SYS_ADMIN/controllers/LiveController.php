<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:52
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\models\LiveRoom;

class LiveController extends CommonController
{

    public function actionIndex()
    {
        if(LiveRoom::getRoomId()){ // �̼� ����
            $this->redirect("/live/info");
        }

        // ����Ա
        if(\Yii::$app->request->get('api')){
            $live_list = LiveRoom::find()
                ->where(['<>', 'status', 0])
                ->asArray()
                ->all();
            $this->successInfo($live_list);
        } else {
            $this->render('list');
        }
    }

    /**
     * @return string|void
     * �鿴 | �༭ֱ����
     */
    public function actionInfo()
    {
        $room_info = [];
        $room_id = \Yii::$app->request->get('rid');

        if(LiveRoom::getRoomId()){ // �̼Ҳ鿴�Լ�����
            $room_id = LiveRoom::getRoomId();
        }

        if(!empty($room_id)){
            $room_info = LiveRoom::getRoomInfo($room_id);
        }

        return $this->render("detail", ['room_info' => $room_info]);
    }


    /**
     * @return array|void
     * ɾ��ֱ����
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('rid');
        $id = intval($id);
        if(empty($id)){
            return $this->errorInfo(400, "��������");
        }

        if(LiveRoom::getRoomId() && LiveRoom::getRoomId() !== $id){
            return $this->errorInfo(400, "��Ȩ����");
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
            return $this->errorInfo(400, "����ʧ�ܣ����Ժ�����");
        }
    }


}