<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 0:56
 */

namespace SYS_ADMIN\controllers;
use Codeception\Lib\Connector\Yii2;
use SYS_ADMIN\models\Lens;
use SYS_ADMIN\models\LiveRoom;

/**
 * Class LensController
 * @package SYS_ADMIN\controllers
 * 镜头管理
 */

class LensController extends  CommonController
{
    /**
     * 镜头列表
     */
    public function actionList()
    {
        if(\Yii::$app->request->get('api')){
            $lens = Lens::getLensList();
            return $this->successInfo($lens);
        } else {
            return $this->render('list');
        }
    }

    /**
     * 获取镜头信息
     */
    public function actionInfo()
    {
        $lens_info = [];
        $id = \Yii::$app->request->get('id');
        $id = intval($id);
        if(!empty($id)){
            $model = Lens::find()->where(['<>', 'status', 0]);
            $model->andWhere(['id' => $id]);
            if(LiveRoom::getRoomId()){
                $model->andWhere(['room_id' => LiveRoom::getRoomId()]);
            }
            $lens_info = $model->asArray()->one();
        }


        return $this->render("detail", ['info' => $lens_info, ]);
    }

    /**
     * 镜头编辑
     */
    public function actionSave()
    {
        $id =\Yii::$app->request->post('id');
        $lens_name =\Yii::$app->request->post('lens_name');
        $cover_img =\Yii::$app->request->post('cover_img');
        $online_url =\Yii::$app->request->post('online_url');
        $playback_url =\Yii::$app->request->post('playback_url');
        $bgm_url =\Yii::$app->request->post('bgm_url');
        $marvellous_url =\Yii::$app->request->post('marvellous_url');
        $status =\Yii::$app->request->post('status');
        $sort_num =\Yii::$app->request->post('sort_num');

        $model = new Lens();
        $model->attributes = \Yii::$app->request->post();
        if(!$model->validate()){
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(400, $errors);
        }

        if(!empty($id)){ // 更新
            $where['id'] = $id;
            if(LiveRoom::getRoomId()){
                $where['room_id'] = LiveRoom::getRoomId();
            }

            $model = Lens::find()
                ->where($where)
                ->andWhere(['<>', 'status', 0])
                ->one();


        } else { // 新增
            $room_id = LiveRoom::getRoomId();
            if(empty($room_id)){
                return $this->errorInfo(400, "参数错误room_id");
            }

            $model->click_num = 1;
            $model->created_at = time();
        }

        $model->lens_name = $lens_name;
        $model->cover_img = $cover_img;
        $model->online_url = $online_url;
        $model->playback_url = $playback_url;
        $model->bgm_url = $bgm_url;
        $model->cover_img = $cover_img;
        $model->marvellous_url = $marvellous_url;
        $model->status = $status;
        $model->sort_num = $sort_num;
        $model->room_id = $room_id;
        $model->updated_at = time();

        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }

    }

    /**
     * 镜头删除
     */
    public function actionDel()
    {

        $id = \Yii::$app->request->post('id');
        $id = intval($id);

        if(empty($id)){
            return $this->errorInfo(400, "参数错误");
        }

        $where['id'] = $id;
        $room_id = LiveRoom::getRoomId();
        if($room_id){
            $where['room_id'] = $room_id;
        }

        $model = Lens::find()
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