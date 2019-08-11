<?php
/**
 * User: liwj
 * Date:2018/11/14
 * Time:20:57.
 */

namespace SYS_ADMIN\controllers\rest\v2;

use app\models\Comment;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\controllers\rest\v2\BaseController;
use SYS_ADMIN\models\Article;
use SYS_ADMIN\models\Banner;
use SYS_ADMIN\models\ClientStart;
use SYS_ADMIN\models\EquipmentBack;
use SYS_ADMIN\models\Lens;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\LiveRoomExtend;
use SYS_ADMIN\models\Log;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\ShoppingMall;
use SYS_ADMIN\models\Snapshot;
use SYS_ADMIN\models\User;
use SYS_ADMIN\models\Video;

class RoomController extends BaseController
{
    /**
     * 获取 所有视频.
     */
    public function actionVideos()
    {
        $id = \Yii::$app->request->get('id');

        $room_info = LiveRoom::findOne($id);
        if (empty($id) || empty($room_info)) {
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

        if (count($video_list)) {

            foreach ($video_list as $v) {
                $videos[] = [
                    'id' => $v['id'],
                    'start' => array_key_exists($v['id'], $video_start) ? 1 : 0,
                    'name' => $v['video_name'],
                    'vurl' => $v['video_url'],
                    'vlength' => $v['video_length'],
                    'click' => number_format($v['click_num']),
                    'pic' => $v['cover_img'],
                    'sort_num' => $v['sort_num'],
                    'video_date' => date('m/d', $v['created_at']),
                    'vnum' => md5($v['id']),
                ];
            }
        }

        return $this->successInfo($videos);
    }

    /**
     * 点击次数.
     */
    public function actionClickNum()
    {
        $ctype = \Yii::$app->request->post('ctype'); // 类型
        $cid = \Yii::$app->request->post('cid');

        if (empty($ctype) || empty($cid)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        switch ($ctype) {
            case 'video':
                $model = Video::findOne($cid);
                if (empty($model)) {
                    return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
                }

                $model->updateCounters(['click_num' => 1]);
                break;

            case 'lens':
                $model = Lens::findOne($cid);
                if (empty($model)) {
                    return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
                }

                $model->updateCounters(['click_num' => 1]);
                break;
        }

        return $this->successInfo(true);
    }

    /**
     * 获取直播间镜头.
     */
    public function actionLens()
    {
        $room_id = \Yii::$app->request->post('room_id');
        $room_info = LiveRoom::find()
            ->where(['id' => $room_id])
            ->andWhere(['status' => ConStatus::$STATUS_ENABLE])
            ->filterWhere(['user_id' => isset($this->isAdmin) ? null : $this->user_info['id']])
            ->asArray();

        if (empty($room_info)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $lens_online = []; // 在线
        $lens_offline = []; //不在线
        $lens = Lens::find()
            ->where(['room_id' => $room_id])
            ->andWhere(['status' => ConStatus::$STATUS_ENABLE])
            ->asArray()
            ->all();

        if (count($lens) > 0) {
            foreach ($lens as &$len) {
                $rtmp_url = "";
                if (strpos($len['online_url'], 'https') !== false) {
                    $rtmp_url = str_replace('https', 'rtmp', $len['online_url']);
                    $rtmp_url = str_replace('.m3u8', '', $rtmp_url);
                }

                if ($len['stream_status'] == ConStatus::$STREAM_STAUS_ONLINE) {
                    $len['cover_img'] = $len["online_cover_url"];
                    $len['video_url'] = $rtmp_url;
                    $len['video_type'] = "live";
                    $lens_online[] = $len;
                } else {
                    $len['cover_img'] = $len["marvellous_url"];
                    $len['video_url'] = $len['playback_url'];
                    $len['video_type'] = "playback";
                    $lens_offline[] = $len;
                }
            }
        }

        return $this->successInfo(['online' => $lens_online, 'offline' => $lens_offline]);
    }

    /**
     * 根据ID获取直播间信息.
     */
    public function actionInfo()
    {
        $id = \Yii::$app->request->get('id');

        $list = LiveRoom::find()
            ->alias('lr')
            ->select(['lr.*', 'lre.cover_img', 'lre.introduce', 'lre.content'])
            ->leftJoin('sys_live_room_extend as lre', 'lr.id = lre.room_id')
            ->where(['lr.id' => $id])
            ->andWhere(['lr.status' => ConStatus::$STATUS_ENABLE])
            ->asArray()
            ->one();
        if (empty($list)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS);
        }

        if (!empty($list['logo_img'])) { // logo
            $logo_pic = Pictrue::getPictrueById($list['logo_img']);
            $list['logo_img'] = $logo_pic['pic_path'] ?? '';
        }

        if (!empty($list['cover_img'])) { // cover
            $cover_pic = Pictrue::getPictrueById($list['cover_img']);
            $list['cover_img'] = $cover_pic['pic_path'] ?? '';
        }

        $list['content'] = str_replace('/uploads/images/', CommonHelper::getDomain() . '/uploads/images/',
            $list['content']);
        // 增加点击量
        $model = LiveRoom::findOne($id);
        $model->updateCounters(['click_num' => 1]);
        ++$list['click_num'];
        // 联系电话
        $user_info = User::findOne($list['user_id']);
        $list['mobile'] = $user_info['phone'];

        // 商城信息
        $list['title'] = '';
        $list['sub_title'] = '';
        $list['intro'] = '';
        $list['wx_logo'] = '';
        $list['deliver'] = 0; // 起送条件
        $mall = ShoppingMall::find()
            ->where(['room_id' => $id])
            ->andWhere(['status' => ConStatus::$STATUS_ENABLE])
            ->asArray()
            ->one();

        // 更新访问量

        if ($mall) {
            $list['title'] = $mall['title'];
            $list['sub_title'] = $mall['sub_title'];
            $list['intro'] = $mall['introduction'];
            $list['deliver'] = $mall['deliver'];
            $list['wx_logo'] = $mall['image_src'] ? CommonHelper::getImgPath($mall['image_src']) : $list['cover_img'];
        }

        // 获取天气预报
        $weatherInfo = CommonHelper::getWeatherInfo($list['addr']);
        $list['weather'] = $weatherInfo;
        $list['click_num'] = CommonHelper::numberFormat($list['click_num']);
        return $this->successInfo($list);
    }

    /**
     * 获取直播间的评论.
     */
    public function actionComments()
    {
        //$user_id = $this->user_info['uid'];
        $id = \Yii::$app->request->post('id');
//        $id = 9;
//        $user_id = 8;
        $room_info = LiveRoom::findOne($id);

        if (empty($id) || empty($room_info)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $lists = Comment::find()
            ->where(['from_id' => $id, 'type' => ConStatus::$COMMENT_TYPE_ROOM])
            ->andWhere(['status' => ConStatus::$STATUS_ENABLE])
            ->asArray()
            ->orderBy('id desc')
            ->all();

        if (count($lists)) {
            $start_list = [];
            /*if ($user_id > 0) { // 用户收藏
                $start_list = ClientStart::find()
                    ->where(['client_id' => $user_id])
                    ->select(['target_id', 'client_id'])
                    ->indexBy('target_id')
                    ->asArray()
                    ->all();
            }*/

            $i = 1;
            foreach ($lists as &$com) {
                $com['date'] = date('Y-m-d H:i', strtotime($com['created_at']));
                $com['num'] = $i++;
                $com['start'] = array_key_exists($com['id'], $start_list) ? 1 : 0;
            }
        }

        $this->successInfo($lists);
    }

    /**
     * 直播间列表.
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

    /**
     * 直播间轮播图.
     */
    public function actionBanner()
    {
        $id = \Yii::$app->request->post('id');

        $lists = Banner::find()
            ->select(['title', 'cover_img', 'links'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['banner_type' => ConStatus::$BANNER_TYPE_ROOM])
            ->andWhere(['room_id' => $id])
            ->limit(4)
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        if (count($lists)) {
            $picIds = array_column($lists, 'cover_img');
            $picLists = Pictrue::getPictrueList($picIds);

            foreach ($lists as &$item) {
                $item['cover'] = $picLists[$item['cover_img']]['pic_path'] ?? '';
            }
        }

        return $this->successInfo(['banner' => $lists]);
    }

    /**
     * 获取直播间模板信息.
     */
    public function actionTemplate()
    {
        $id = \Yii::$app->request->post('id', 0);

        $room = LiveRoom::findOne($id);
        $template = 1;
        $secret = 0;
        $auth_template = 0;
        if (!empty($room)) {
            $room_info = LiveRoomExtend::findOne(['room_id' => $id]);
            $template = $room->templet_id ?? 1;
            $secret = !empty($room_info->secret_key) ? 1 : 0;
            $auth_template = !empty($room_info->secret_key) ? $room_info->auth_template : 0;
        }

        return $this->successInfo([
            'template' => $template,
            'secret' => $secret,
            'auth_template' => $auth_template,
            ]);
    }

    /**
     * 房间密钥检测
     */
    public function actionCheckSecret()
    {
        $id = \Yii::$app->request->post('id', 0);
        $secret_key = \Yii::$app->request->post('secretKey', 0);

        $check = false;
        $auth_template = 0;
        $room_info = LiveRoomExtend::findOne(['room_id' => $id]);
        if (!empty($room_info)) {
            if (strpos($room_info->secret_key, $secret_key) !== false) {
                $check = true;
            }
        }

        return $this->successInfo(['check' => $check]);
    }

    public function actionAuthRoom()
    {
        $id = \Yii::$app->request->post('id', 0);
        $mobile= \Yii::$app->request->post('mobile', 0);
        $code = \Yii::$app->request->post('sms', 0);

        if (empty($id) || empty($mobile) || empty($code)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SMS, ConStatus::$ERROR_MOBILE_CODE_MSG);
        }

        $room_info = LiveRoomExtend::findOne(['room_id' => $id]);
        if (empty($room_info)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (strpos($room_info->secret_key, $mobile) === false) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_MOBILE, ConStatus::$ERROR_MOBILE_MSG);
        }

        $redis = \Yii::$app->redis;
        $uid = !empty($this->user_info) ? $this->user_info['uid'] : 0;
        $key = ConStatus::$RECEIVER.$uid.':'.$mobile.':'.$code;
        if (!$redis->get($key)) { // 无效验证码
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_MOBILE_CODE_MSG);
        }

        return $this->successInfo(['check' => true]);
    }


    /**
     * 溯源视频
     */
    public function actionSourceVideo()
    {

        $id = \Yii::$app->request->post('id');
        $sid = \Yii::$app->request->post('sid');
        $page = \Yii::$app->request->post('page', ConStatus::$PAGE_NUM);
        $lists = [];

        $lens_info = Lens::findOne($sid);
        if (!empty($lens_info)) {
            $offset = ($page - 1) * ConStatus::$INDEX_SNAPSHOT_PAGE_SIZE;
            $storage = $lens_info->storage ? $lens_info->storage : ConStatus::$DEFAULT_STORAGE;
            $lists = EquipmentBack::find()
                ->select(['uri', 'duration', 'start_time'])
                ->where(['stream' => $lens_info->stream_name])
                ->andWhere(['>', 'start_time', date('Y-m-d', strtotime("-{$storage} day"))])
                ->offset($offset)
                ->limit(ConStatus::$INDEX_SNAPSHOT_PAGE_SIZE)
                ->orderBy('id desc')
                ->asArray()
                ->all();

            if (count($lists)) {
                foreach ($lists as &$sitem) {
                    $sitem['start_time'] = date('Y-m-d H:i', strtotime($sitem['start_time']));
                    $sitem['duration'] = round($sitem['duration']);
                }
            }
        }

        return $this->successInfo($lists);
    }

    /**
     * 截图
     */
    public function actionSnapshot()
    {
        $id = \Yii::$app->request->post('id');
        $lists = Snapshot::find()
            ->select(['title', 'cover', 'created_at'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['room_id' => $id])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        if (count($lists)) {
            $picIds = array_column($lists, 'cover');
            $picLists = Pictrue::getPictrueList($picIds);

            foreach ($lists as &$item) {
                $item['cover'] = $picLists[$item['cover']]['pic_path'] ?? '';
                $item['created_time'] = date('Y-m-d', strtotime($item['created_at']));
            }
        }

        return $this->successInfo($lists);
    }

    /**
     * 文章列表
     */
    public function actionArticle()
    {
        $room_id = \Yii::$app->request->post('room_id');
        $page = \Yii::$app->request->post('page', ConStatus::$PAGE_NUM);
        $lists = [];

        $room_info = LiveRoom::findOne($room_id);
        if (!empty($room_info)) {
            $offset = ($page - 1) * ConStatus::$INDEX_ARTICLE_PAGE_SIZE;
            $lists = Article::find()
                ->select(['id', 'room_id', 'title', 'cover', 'click_num', 'created_at'])
                ->where(['room_id' => $room_id])
                ->andWhere(['status' => ConStatus::$STATUS_ENABLE])
                ->offset($offset)
                ->limit(ConStatus::$INDEX_ARTICLE_PAGE_SIZE)
                ->orderBy('sort_num asc, id desc')
                ->asArray()
                ->all();

            if (count($lists)) {
                $pic_id = array_column($lists, 'cover');
                $pic_list = Pictrue::getPictrueList($pic_id);
                foreach ($lists as &$sitem) {
                    $sitem['pic_path'] = isset($pic_list[$sitem['cover']]) ? $pic_list[$sitem['cover']]['pic_path'] : '';
                    $sitem['created_at'] = date('Y-m-d H:i', strtotime($sitem['created_at']));
                }
            }
        }

        return $this->successInfo($lists);
    }

    /**
     * 文章详情
     */
    public function actionArticleDetail()
    {
        $id = \Yii::$app->request->post('article_id');
        $room_id = \Yii::$app->request->post('room_id');

        if (empty($id) ||empty($room_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $room_info = LiveRoom::findOne($room_id);
        if (!empty($room_info)) {
            $article = Article::find()
                ->select(['id', 'room_id', 'title','cover', 'content', 'click_num', 'created_at'])
                ->where(['room_id' => $room_id])
                ->andWhere(['id' => $id])
                ->andWhere(['status' => ConStatus::$STATUS_ENABLE])
                ->one()
                ->toArray();

            if (count($article)) {
                Article::findOne($article['id'])->updateCounters(['click_num' => 1]);

                // 设置封面图
                if (!empty($article['cover'])) {
                    $picture = Pictrue::getPictrueById($article['cover']);
                    $article['cover'] = $picture['pic_path'];
                }
                $article['room_name'] = $room_info['room_name'];
                $article['click_num'] ++;
            }
            return $this->successInfo($article);
        }

        return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);


    }

    /**
     * 镜头回放
     */
    public function actionLensPlayBack()
    {
        $id = \Yii::$app->request->get('id');
        $lens = [];
        $lens_list = Lens::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['room_id' => $id])
            ->asArray()
            ->orderBy('sort_num asc, id desc')
            ->all();

        if (count($lens_list)) {
            foreach ($lens_list as $v) {
                $cover_img = $v['online_cover_url'];

                $lens[] = [
                    'aid' => $v['id'],
                    'name' => $v['lens_name'],
                    'video' => $v['playback_url'],
                    'cover' => $cover_img,
                ];
            }
        }


        return $this->successInfo($lens);
    }



}
