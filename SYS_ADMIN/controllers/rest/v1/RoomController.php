<?php
/**
 * User: liwj
 * Date:2018/11/14
 * Time:20:57
 */

namespace SYS_ADMIN\controllers\rest\v1;


use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Video;

class RoomController extends CommonController
{
    /**
     * 获取 所有视频
     */

    public function actionVideos()
    {
        $id = \Yii::$app->request->get('id');
        $videos = [];
        $video_list = Video::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['room_id' => $id])
            ->select(['id', 'video_name', 'video_url', 'start_num', 'room_id'])
            ->asArray()
            ->orderBy('id desc')
            ->all();

        if(count($video_list)){
            foreach ($video_list as $v){
                $videos[] = [
                    'start' => $v['start_num'] ?? 1,
                    'name' => $v['video_name'],
                    'vurl' => $v['video_url'],
                ];
            }
        }

        return $this->successInfo(['videos' => $videos]);
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