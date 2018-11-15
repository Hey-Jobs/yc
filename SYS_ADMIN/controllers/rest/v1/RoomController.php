<?php
/**
 * User: liwj
 * Date:2018/11/14
 * Time:20:57
 */

namespace SYS_ADMIN\controllers\rest\v1;


use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;
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
            ->asArray()
            ->orderBy('sort_num asc, id desc')
            ->all();

        if(count($video_list)){
            $pic_id = array_column($video_list, 'cover_img');
            $pic_list = Pictrue::getPictrueList($pic_id);

            foreach ($video_list as $v){
                $pic_path = isset($pic_list[$v['cover_img']]) ? $pic_list[$v['cover_img']]['pic_path'] : "";
                $videos[] = [
                    'start' => $v['start_num'] ?? 1,
                    'name' => $v['video_name'],
                    'vurl' => $v['video_url'],
                    'vlength' => $v['video_length'],
                    'click' => number_format($v['click_num']),
                    'pic' => $pic_path,
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