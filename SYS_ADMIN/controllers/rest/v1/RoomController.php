<?php
/**
 * User: liwj
 * Date:2018/11/14
 * Time:20:57
 */

namespace SYS_ADMIN\controllers\rest\v1;


use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Lens;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Video;
use SYS_ADMIN\models\VideoStart;

class RoomController extends CommonController
{
    /**
     * 获取 所有视频
     */

    public function actionVideos()
    {
        $user_id = 10;
        $id = \Yii::$app->request->get('id');
        $videos = [];
        $video_start = [];
        $video_list = Video::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['room_id' => $id])
            ->asArray()
            ->orderBy('sort_num asc, id desc')
            ->all();

        if(count($video_list)){
            $pic_id = array_column($video_list, 'cover_img');
            $pic_list = Pictrue::getPictrueList($pic_id);

            if($user_id > 0){
                $video_start = VideoStart::find()
                    ->where(['user_id' => $user_id])
                    ->select(['video_id', 'user_id'])
                    ->indexBy('video_id')
                    ->asArray()
                    ->all();
            }

            foreach ($video_list as $v){
                $pic_path = isset($pic_list[$v['cover_img']]) ? $pic_list[$v['cover_img']]['pic_path'] : "";
                $videos[] = [
                    'id' => $v['id'],
                    'start' => array_key_exists($v['id'], $video_start) ? 1 : 0,
                    'name' => $v['video_name'],
                    'vurl' => $v['video_url'],
                    'vlength' => $v['video_length'],
                    'click' => number_format($v['click_num']),
                    'pic' => $pic_path,
                    'vnum' => md5($v['id']),
                ];
            }
        }

        return $this->successInfo(['videos' => $videos]);
    }


    /**
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     * 取消收藏 | 收藏
     */
    public function actionVideoStart()
    {
        $user_id = 10;
        $vid = \Yii::$app->request->post('vid', 0);

        $info = Video::findOne($vid);
        if(empty($info)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model =VideoStart::find()
            ->where(['video_id' => $vid, 'user_id' => $user_id])
            ->one();

        if(empty($model)){ // 收藏
            $model = new VideoStart();
            $model->video_id = $vid;
            $model->user_id = $user_id;
            $model->save();

        } else { // 取消收藏
            $model->delete();
        }

        $this->successInfo(true);
    }


    /**
     * 点击次数
     */
    public function actionClickNum()
    {
        $ctype = \Yii::$app->request->post('ctype'); // 类型
        $cid = \Yii::$app->request->post('cid');

        if(empty($ctype) || empty($cid)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        switch ($ctype){
            case 'video':
                $model = Video::findOne($cid);
                if(empty($model)){
                    return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
                }

                $model->updateCounters(['click_num' => 1]);
                break;

            case 'lens':
                $model = Lens::findOne($cid);
                if(empty($model)){
                    return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
                }

                $model->updateCounters(['click_num' => 1]);
                break;
        }

        return $this->successInfo(true);
    }

    /**
     * 获取直播间镜头
     */
    public function actionLens()
    {
        $id = \Yii::$app->request->get('id');
        $lens = [];
        $lens_list = Lens::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['room_id' => $id])
            ->asArray()
            ->orderBy('sort_num asc, id desc')
            ->all();


        if(count($lens_list)){
            $pic_id = array_column($lens_list, 'cover_img');
            $pic_list = Pictrue::getPictrueList($pic_id);

            foreach ($lens_list as $v){
                $pic_path = isset($pic_list[$v['cover_img']]) ? $pic_list[$v['cover_img']]['pic_path'] : "";
                $lens[] = [
                    'name' => $v['video_name'],
                    'vurl' => $v['video_url'],
                    'vlength' => $v['video_length'],
                    'click' => number_format($v['click_num']),
                    'pic' => $pic_path,
                    'vnum' => md5($v['id']),
                ];
            }
        }

    }


    /**
     * 根据ID获取直播间信息
     */
    public function actionInfo()
    {
        $id = \Yii::$app->request->get('id');

        $list = LiveRoom::find()
            ->alias('lr')
            ->select(['lr.*', 'lre.cover_img', 'lre.introduce', 'lre.content'])
            ->leftJoin('sys_live_room_extend as lre', 'lr.id = lre.room_id')
            ->where(['lr.id' => $id])
            ->asArray()
            ->one();
        if (empty($list)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS);
        }

        return $this->successInfo($list);
    }

}