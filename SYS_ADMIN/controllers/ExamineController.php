<?php


namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Snapshot;
use SYS_ADMIN\models\Video;

class ExamineController extends CommonController
{
    public function actionSnapshot()
    {
        if (\Yii::$app->request->get('api')) {

            $snapshot_list = [];
            if (!CommonHelper::isAdmin()) {
                return $this->successInfo($snapshot_list);
            }

            $snapshot_list = Snapshot::find()
                ->where(['status' => ConStatus::$STATUS_NEED_CHECK])
                ->orderBy('updated_at desc')
                ->asArray()
                ->all();

            if (count($snapshot_list) > 0) {
                $user_room = LiveRoom::getUserRoomId();
                $picid_list = array_column($snapshot_list, 'cover');
                $pic_list = Pictrue::getPictrueList($picid_list);
                foreach ($snapshot_list as &$snapshot) {
                    $snapshot['pic_path'] = isset($pic_list[$snapshot['cover']]) ? $pic_list[$snapshot['cover']]['pic_path'] : '';
                    $snapshot['created_at'] = date('Y-m-d H:i', strtotime($snapshot['created_at']));
                    $snapshot['status'] = ConStatus::$STATUS_LIST[$snapshot['status']];
                    $snapshot['room_name'] = $user_room[$snapshot['room_id']]['room_name'];
                }
            }
            return $this->successInfo($snapshot_list);
        } else {
            return $this->render('snapshot');
        }
    }

    public function actionVideo()
    {
        if (\Yii::$app->request->get('api')) {

            $video_list = [];
            if (!CommonHelper::isAdmin()) {
                return $this->successInfo($video_list);
            }

            $video_list = Video::find()
                ->where(['status' => ConStatus::$STATUS_NEED_CHECK])
                ->orderBy('updated_at desc')
                ->asArray()
                ->all();

            if (count($video_list) > 0) {
                $user_room = LiveRoom::getUserRoomId();
                foreach ($video_list as &$video) {
                    $video['created_at'] = date('Y-m-d H:i', $video['created_at']);
                    $video['status'] = ConStatus::$STATUS_LIST[$video['status']];
                    $video['room_name'] = $user_room[$video['room_id']] ? $user_room[$video['room_id']]['room_name'] : "";
                }
            }
            return $this->successInfo($video_list);
        } else {
            return $this->render('video');
        }
    }

    /**
     * 审批图片
     */
    public function actionCheckSnapshot()
    {
        $checktype = \Yii::$app->request->post('checktype');
        $checklist = \Yii::$app->request->post('checklist');

        if (!array_key_exists($checktype, ConStatus::$CHECK_RESULT_TYPE) || empty($checklist)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }
        $checkresult = $checktype == ConStatus::$CHECK_RESULT_TYPE_PASS ? ConStatus::$STATUS_ENABLE: ConStatus::$STATUS_CHECK_FAIL;

        $check_id_list = explode(',', $checklist);
        $res = Snapshot::updateAll(['status' => $checkresult], ['in', 'id', $check_id_list]);
        if ($res) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
        }
    }


    /**
     * 审核视频
     */
    public function actionCheckVideo()
    {
        $checktype = \Yii::$app->request->post('checktype');
        $checklist = \Yii::$app->request->post('checklist');

        if (!array_key_exists($checktype, ConStatus::$CHECK_RESULT_TYPE) || empty($checklist)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $checkresult = $checktype == ConStatus::$CHECK_RESULT_TYPE_PASS ? ConStatus::$STATUS_ENABLE: ConStatus::$STATUS_CHECK_FAIL;
        $check_id_list = explode(',', $checklist);
        $res = Video::updateAll(['status' => $checkresult], ['in', 'id', $check_id_list]);
        if ($res) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG);
        }
    }
}