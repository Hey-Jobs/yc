<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/26
 * Time: 12:17
 */

namespace SYS_ADMIN\controllers\rest\v1;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Activity;
use SYS_ADMIN\models\Banner;
use SYS_ADMIN\models\Campus;
use SYS_ADMIN\models\Category;
use SYS_ADMIN\models\Lens;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\ShoppingMall;
use SYS_ADMIN\models\Video;

/**
 * Class LiveController
 * @package SYS_ADMIN\controllers\rest\v1
 * 系统信息
 */
class LiveController extends  CommonController
{
    /**
     * @return array
     * 轮播图
     */
    public function actionBanner()
    {
        $lists = Banner::find()
            ->select(['title', 'cover_img', 'links'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['banner_type' => ConStatus::$BANNER_TYPE_SYS])
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

        return $this->successInfo($lists);
    }

    /**
     * 最新活动，只取一条
     */
    public function actionActivity()
    {
        $info = Activity::find()
            ->select(['title', 'activity_time', 'activity_url'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->one();

        return $this->successInfo($info);
    }

    /**
     * 分类
     */
    public function actionCategory()
    {
        $lists = Category::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        return $this->successInfo($lists);
    }

    /**
     * 直播间列表
     * @param integer vtype  查询类型 2 首页推荐  1 列表
     * @param integer category 类型
     * @param integer page 分页
     */
    public function actionRoom()
    {
        $room_type = \Yii::$app->request->post('room_type', 1);
        $category = \Yii::$app->request->post('category', 0);
        $room_name = \Yii::$app->request->post('room_name', '');
        $page = \Yii::$app->request->post('page', ConStatus::$PAGE_NUM);

        $model = LiveRoom::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['>', 'category_id', 0]);

        if ($room_type == 2) { // 首页推荐，显示直播间  排序值为 1~10之间
            $model->andWhere(['<=', 'sort_num', 10]);
        } else {
            if (!in_array($category, [1, 2])) { // 去除特殊分类id
                $model->andWhere(['category_id' => $category]);
            }

            if (!empty($room_name)) {
                $model->andWhere(['like', 'room_name', $room_name]);
            }
        }

        $offset = ($page - 1) * ConStatus::$INDEX_ROOM_PAGE_SIZE;
        $lists = $model->offset($offset)
            ->limit(ConStatus::$INDEX_ROOM_PAGE_SIZE)
            ->select(['id as room_id', 'room_name', 'click_num', 'templet_id', 'category_id', 'online_cover', 'logo_img'])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        if (count($lists)) {
            $picIds = array_column($lists, 'logo_img');
            $picList = Pictrue::getPictrueList($picIds);

            $roomIds = array_column($lists, 'room_id');
            $malls = ShoppingMall::find()
                ->select(['title', 'introduction', 'room_id'])
                ->where(['status' => ConStatus::$STATUS_ENABLE])
                ->andWhere(['in', 'room_id', $roomIds])
                ->indexBy('room_id')
                ->asArray()
                ->all();

            foreach ($lists as &$item) {
                $item['title'] = $malls[$item['room_id']]['title'] ?? $item['room_name'];
                $item['intro'] = $malls[$item['room_id']]['introduction'] ?? $item['room_name'];
                $item['click_num'] = CommonHelper::numberFormat($item['click_num']);
                $item['logo_img'] = $picList[$item['logo_img']]['pic_path'] ?? CommonHelper::getDefaultLogo();
            }
        }

        return $this->successInfo($lists);
    }

    /**
     * 所有视频列表
     */
    public function actionVideo()
    {
        $page = \Yii::$app->request->post('page', ConStatus::$PAGE_NUM);

        $model = Video::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE]);

        $offset = ($page - 1) * ConStatus::$INDEX_VIDEO_PAGE_SIZE;
        $lists = $model->offset($offset)
            ->limit(ConStatus::$INDEX_VIDEO_PAGE_SIZE)
            ->select(['id', 'room_id', 'video_name', 'cover_img',
                'video_length', 'video_url', 'click_num', 'video_url as vurl'])
            ->where(['<=', 'sort_num', 50])  // 只显示排序值小于50
            ->andWhere(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['<>', 'cover_img', ''])
            ->orderBy('id desc')
            ->asArray()
            ->all();

        if (count($lists)) {
            $roomIds = array_column($lists, 'room_id');
            $rooms = LiveRoom::find()
                ->select(['room_name', 'id as room_id', 'logo_img'])
                ->andWhere(['in', 'id', $roomIds])
                ->indexBy('room_id')
                ->asArray()
                ->all();

            $malls = ShoppingMall::find()
                ->select(['title', 'introduction', 'room_id'])
                ->where(['status' => ConStatus::$STATUS_ENABLE])
                ->andWhere(['in', 'room_id', $roomIds])
                ->indexBy('room_id')
                ->asArray()
                ->all();

            $picIds = array_column($rooms, 'logo_img');
            $picList = Pictrue::getPictrueList($picIds);
            foreach ($lists as &$item) {
                $logo_img = $rooms[$item['room_id']]['logo_img'];
                $item['title'] = isset($malls[$item['room_id']]) ?
                    $malls[$item['room_id']]['title'] : $rooms[$item['room_id']]['room_name'];
                $item['click_num'] = CommonHelper::numberFormat($item['click_num']);
                $item['video_length'] = CommonHelper::numberFormat($item['video_length'], 2);
                $item['logo_img'] = !empty($logo_img) ? $picList[$logo_img]['pic_path'] : CommonHelper::getDefaultLogo();
            }
        }

        return $this->successInfo($lists);
    }

    /**
     * 短信
     */
    public function actionSms()
    {
        //$res = CommonHelper::sendSms('13750509674', 'verify', ['code' => rand(1000, 9999)]);
        //var_dump($res);
    }

    /**
     * 监控查看在线视频
     */
    public function actionMonitor()
    {
        $secret = \Yii::$app->request->post('secret');
        $secret = intval($secret);
        $room_id = octdec($secret / ConStatus::$ROOM_SECRET_KEY);

        if ($room_id <= 0) {
            return  $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $room_info = LiveRoom::findOne($room_id);
        if (empty($room_info)) {
            return  $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }


        // 获取在线镜头
        $lens = Lens::find()
            ->select(['lens_name', 'online_url', 'online_cover_url', 'addr_url'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['room_id' => $room_id])
            ->andWhere(['stream_status' => ConStatus::$STEARM_STATUS_ONLINE])
            ->asArray()
            ->all();

        if (count($lens) > 0) {
            foreach ($lens as &$len) {
                if (strpos($len['online_url'], 'https') !== false) {
                    $rtmp_url = str_replace('https', 'rtmp', $len['online_url']);
                    $rtmp_url = str_replace('.m3u8', '', $rtmp_url);
                    $len['online_url'] = $rtmp_url;
                }
            }
        }
        $info['room_name'] = $room_info->room_name;
        $info['lens'] = $lens;
        return $this->successInfo($info);
    }

    // 获取校园直播间
    public function actionCampus()
    {
        $key = \Yii::$app->request->post('key');
        $user_id = octdec($key / ConStatus::$USER_SECRET_KEY);

        $campus_model = Campus::findOne(['user_id' => $user_id]);
        $campus_info = [];
        $default_bg = CommonHelper::getDomain().'/images/campus_bg.jpg';
        if (!empty($campus_model)) {
            $campus_info = $campus_model->toArray();
            // 获取封面
            if (!empty($campus_info['cover_id'])) {
                $logo_info = Pictrue::getPictrueById($campus_info['cover_id']);
                $campus_info['cover'] = $logo_info['pic_path'];
            }

            // 获取背景图
            if (!empty($campus_info['bg_cover_id'])) {
                $bg_img_info = Pictrue::getPictrueById($campus_info['bg_cover_id']);
                $campus_info['bg_cover'] = $bg_img_info['pic_path'];
            }
        }

        $room_list = LiveRoom::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['user_id' => $user_id])
            ->select(['id', 'room_name'])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        $data['title'] = $campus_info['title'] ?? '';
        $data['cover'] = $campus_info['cover'] ?? '';
        $data['bg_cover'] = $campus_info['bg_cover'] ?? $default_bg;
        $data['room_list'] = $room_list;

        return $this->successInfo($data);
    }
}