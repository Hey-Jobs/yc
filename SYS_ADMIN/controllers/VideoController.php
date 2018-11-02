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
        $model = new Video();
        $res = $model->saveVideo($data);
        if($res['status'] == 1){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, $res['info']);
        }
    }
}