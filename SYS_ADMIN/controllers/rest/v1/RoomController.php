<?php
/**
 * User: liwj
 * Date:2018/11/14
 * Time:20:57
 */

namespace SYS_ADMIN\controllers\rest\v1;


use app\models\Comment;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\models\Log;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Lens;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\ShoppingMall;
use SYS_ADMIN\models\User;
use SYS_ADMIN\models\Video;
use SYS_ADMIN\models\ClientStart;

class RoomController extends CommonController
{
    /**
     * 获取 所有视频
     */

    public function actionVideos()
    {
        $user_id = $this->user_info['uid'];
        $id = \Yii::$app->request->get('id');

        $room_info = LiveRoom::findOne($id);
        if(empty($id) || empty($room_info)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $videos = [];
        $video_start = [];
        $video_list = Video::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['room_id' => $id])
            ->asArray()
            ->orderBy('sort_num asc, id desc')
            ->all();

        if(count($video_list)){
            $pic_id = array_column($video_list, 'cover_img');
            $pic_list = Pictrue::getPictrueList($pic_id);

            if($user_id > 0){
                $video_start = ClientStart::find()
                    ->where(['client_id' => $user_id])
                    ->select(['target_id', 'client_id'])
                    ->indexBy('target_id')
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
            ->where(['status' => ConStatus::$STATUS_ENABLE])
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
                    'aid' => $v['id'],
                    'name' => $v['lens_name'],
                    'vurl' => $v['online_url'],
                    'vurl_reback' => $v['playback_url'],
                    'click' => number_format($v['click_num']),
                    'pic' => $pic_path,
                    'vnum' => md5($v['id']),
                    'vtype' => 'lens',
                ];
            }
        }

        //最多显示三个精彩视频
        $video_list = Video::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['room_id' => $id])
            ->andWhere(['sort_num' => 1])
            ->asArray()
            ->orderBy('sort_num asc, id desc')
            ->all();

        if(count($video_list)){
            $pic_id = array_column($video_list, 'cover_img');
            $pic_list = Pictrue::getPictrueList($pic_id);
            foreach ($video_list as $v){
                $pic_path = isset($pic_list[$v['cover_img']]) ? $pic_list[$v['cover_img']]['pic_path'] : "";
                $lens[] = [
                    'aid' => $v['id'],
                    'name' => $v['video_name'],
                    'vurl' => $v['video_url'],
                    'vurl_reback' => $v['video_url'],
                    'click' => number_format($v['click_num']),
                    'pic' => $pic_path,
                    'vnum' => md5($v['id']),
                    'vtype' => 'video',
                ];
            }
        }

        return $this->successInfo($lens);

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

        if(!empty($list['logo_img'])){ // logo
            $logo_pic = Pictrue::getPictrueById($list['logo_img']);
            $list['logo_img'] = $logo_pic['pic_path'] ?? "";
        }

        if(!empty($list['cover_img'])){ // cover
            $cover_pic = Pictrue::getPictrueById($list['cover_img']);
            $list['cover_img'] = $cover_pic['pic_path'] ?? "";
        }
        $list['content'] = str_replace("/uploads/images/", CommonHelper::getDomain()."/uploads/images/", $list['content']);
        // 增加点击量
        $model = LiveRoom::findOne($id);
        $model->updateCounters(['click_num' => 1]);
        $list['click_num']++;
        // 联系电话
        $user_info = User::findOne($list['user_id']);
        $list['mobile'] = $user_info['phone'];

        // 商城信息
        $list['title'] = '';
        $list['sub_title'] = '';
        $list['intro'] = '';
        $mall = ShoppingMall::find()
            ->where(['room_id' => $id])
            ->asArray()
            ->one();

        // 更新访问量

        if($mall){
            $list['title'] = $mall['title'];
            $list['sub_title'] = $mall['sub_title'];
            $list['intro'] = $mall['introduction'];
        }

        return $this->successInfo($list);
    }

    /**
     * 获取直播间的评论
     */
    public function actionComments()
    {
        $user_id = $this->user_info['uid'];
        $id = \Yii::$app->request->post('id');
//        $id = 9;
//        $user_id = 8;
        $room_info = LiveRoom::findOne($id);

        $log['user_id'] = $user_id;
        $log['from_id'] = $id;
        $logM = new Log();
        $logM->content = json_encode($log);
        $logM->url = $this->action->controller->module->requestedRoute ?? '';
        $logM->save();

        if(empty($id) || empty($room_info)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $lists =  Comment::find()
            ->where(['from_id' => $id, 'type' => ConStatus::$COMMENT_TYPE_ROOM])
            ->asArray()
            ->orderBy('id desc')
            ->all();

        if(count($lists)){

            if($user_id > 0){ // 用户收藏
                $start_list = ClientStart::find()
                    ->where(['client_id' => $user_id])
                    ->select(['target_id', 'client_id'])
                    ->indexBy('target_id')
                    ->asArray()
                    ->all();
            }

            $i = 1;
            foreach ($lists as &$com){
                $com['date'] = date('Y-m-d H:i', strtotime($com['created_at']));
                $com['num'] = $i++;
                $com['start'] = array_key_exists($com['id'], $start_list) ? 1 : 0;
            }
        }

        $this->successInfo($lists);
    }

    /**
     * 直播间列表
     */
    public function actionList()
    {
        $roomList = LiveRoom::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['<>', 'online_cover', ''])
            ->asArray()
            ->all();

        return $this->successInfo($roomList);
    }

}