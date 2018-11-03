<?php
/**
 * User: liwj
 * Date:2018/11/1
 * Time:20:05
 */

namespace SYS_ADMIN\controllers;
use SYS_ADMIN\models\Video;

/**
 * Class VideoController
 * @package SYS_ADMIN\controllers
 * 视频管理
 */
class VideoController extends CommonController
{
    /**
     * 视频列表
     */
    public function actionList()
    {
        if(\Yii::$app->request->get('api')){
            $data = \Yii::$app->request->post();
            $model = new Video();
            $video_list = $model->getVideoList($data);
            return $this->successInfo($video_list);
        } else {
            return $this->render('list');
        }

    }


    public function actionInfo()
    {
        $video_id = \Yii::$app->request->post('id');
        $video_id = intval($video_id);
        if(empty($video_id)){
            return $this->errorInfo(400, "参数错误");
        }

        $model = Video::find()->where(['<>', 'status', 0]);
        $where['id'] = $video_id;
        // 是否是管理员
        if(false){
            $where['room_id'] = 1;
        }

        $info = $model->where($where)->asArray()->one();
        if(!empty($info)){
            return $this->successInfo($info);
        } else {
            return $this->errorInfo(400, "参数错误");
        }
    }
    /**
     * @return array|void
     * 删除
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('id');
        $id = intval($id);

        if(empty($id)){
            return $this->errorInfo(400, "参数错误");
        }

        $model = Video::findOne(['id' => $id]);
        $model->status = 0;
        $model->updated_at = time();
        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, "操作失败，请稍后重试");
        }
    }

    /**
     * 保存视频信息
     */
    public function actionSave()
    {
        $data = \Yii::$app->request->post();
        $id = \Yii::$app->request->post('id');
        $video_name = \Yii::$app->request->post('video_name');
        $video_url = \Yii::$app->request->post('video_url');
        $sort_num = \Yii::$app->request->post('sort_num', 10);
        $status = \Yii::$app->request->post('status', 1);

        $model = new Video();
        $model->attributes = $data;
        if(!$model->validate()){
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(400, $errors);
        }

        if(!empty($id)){
            $model = Video::findOne($id);
            $model->video_name = $video_name;
            $model->video_url = $video_url;
            $model->sort_num = $sort_num;
            $model->status = $status;
            $model->updated_at = time();
        } else {
            $room_id = 1; //
            $model = new Video();
            $model->video_name = $video_name;
            $model->video_url = $video_url;
            $model->sort_num = $sort_num;
            $model->status = $status;
            $model->room_id = $room_id;
            $model->click_num = 1;
            $model->updated_at = time();
            $model->created_at = time();
        }

        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }

    }
}