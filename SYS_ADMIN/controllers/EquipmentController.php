<?php

namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\CommonModel;
use SYS_ADMIN\models\Equipment;
use SYS_ADMIN\models\EquipmentBack;
use SYS_ADMIN\models\EquipmentCount;
use SYS_ADMIN\models\Lens;

class EquipmentController extends CommonController
{
    /**
     * Site Index
     */
    public function actionIndex()
    {
        if (\Yii::$app->request->get('api')) {
            $list = [];
            $model = Equipment::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED]);

            if (!$this->isAdmin) { // 非超级管理员
                $lens = Lens::getLensList();
                if (empty($lens)) {
                    return $this->successInfo($list);
                }

                $appnames = array_column($lens, 'app_name');
                $streams = array_column($lens, 'stream_name');
                $model->andWhere(['in', 'appname', $appnames]);
                $model->andWhere(['in', 'stream', $streams]);
            }

            $list = $model->asArray()->all();
            return $this->successInfo($list);
        } else {
            return $this->render('list');
        }
    }

    /**
     * 推断流
     */
    public function actionPush()
    {
        $appname = \Yii::$app->request->post('appname');
        $stream = \Yii::$app->request->post('stream');
        $type = \Yii::$app->request->post('type');

        if (!array_key_exists($type, ConStatus::$STREAM_STATUS)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $info = Equipment::findOne(['appname' => $appname, 'stream' => $stream]);
        if (empty($info)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (!$this->isAdmin) {
            if (!$this->checkEquipement($appname, $stream)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
            }
        }


        $res = $this->httpGetLive($type, $info->appname, $info->stream, $info->app);
        if (!isset($res['Code'])) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, ConStatus::$ERROR_SYS_MSG.$res['Code']);
        }
    }

    /**
     * 视频文件
     */
    public function actionVideo()
    {

        $appname = \Yii::$app->request->get('appname');
        $stream = \Yii::$app->request->get('stream');
        if (\Yii::$app->request->get('api')) {
            $list = [];
            if (!$this->isAdmin) {
                if (!$this->checkEquipement($appname, $stream)) {
                    return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
                }
            }

            // 获取镜头信息
            $lens = Lens::findOne(['app_name' => $appname, 'stream_name' => $stream]);
            $storage = !empty($lens) && $lens->storage ? $lens->storage : ConStatus::$DEFAULT_STORAGE;
            $list = EquipmentBack::find()
                ->where(['app' => $appname, 'stream' => $stream])
                ->andWhere(['>', 'start_time', date('Y-m-d', strtotime("-{$storage} day"))])
                ->orderBy('id desc')
                ->asArray()
                ->all();

            foreach ($list as &$item) {
                $item['online_time'] = CommonHelper::numberFormat($item['duration'], ConStatus::$NUM_FORMAT_DURATION);
            }

            return $this->successInfo($list);
        } else {
            return $this->render('video', ['appname' => $appname, 'stream' => $stream]);
        }
    }

    /**
     * 设备在线时长统计
     */
    public function actionStatistics()
    {
        $appname = \Yii::$app->request->get('appname');
        $stream = \Yii::$app->request->get('stream');
        if (\Yii::$app->request->get('api')) {

            $list = [];
            if (!$this->isAdmin) {
                if (!$this->checkEquipement($appname, $stream)) {
                    return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
                }
            }

            $list = EquipmentCount::find()
                ->where(['appname' => $appname, 'stream' => $stream])
                ->orderBy('id desc')
                ->asArray()
                ->all();

            if (count($list)) {
                foreach ($list as &$item) {
                    $online_time = !empty($item['online_time']) ? $item['online_time'] : (time() - $item['push_time']);
                    $item['online_time'] = CommonHelper::numberFormat($online_time, ConStatus::$NUM_FORMAT_DURATION);
                }
            }

            return $this->successInfo($list);
        } else {
            return $this->render('statistics', ['appname' => $appname, 'stream' => $stream]);
        }

    }

    /**
     * 删除设备
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('id');
        $id = intval($id);

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, '参数错误');
        }

        $model = Equipment::find()
            ->where(['id' => $id])
            ->andWhere(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->one();

        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_NONE, '参数错误');
        }

        if (!$this->isAdmin) {
            // 检测镜头直播流
            if (!$this->checkEquipement($model->appname, $model->stream)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, '参数错误');
            }
        }

        $model->status = ConStatus::$STATUS_DELETED;
        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, '操作失败，请稍后重试');
        }
    }

    /**
     * @param $appname
     * @param $stream
     * 检测设备是否属于当前管理员
     */
    private function checkEquipement($appname, $stream)
    {
        $lensModel = Lens::find()
            ->where(['appname' => $appname, 'stream' => $stream,])
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->one();

        if ($lensModel) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $type publish_done 禁止推流  publish 恢复推流
     * @param $app
     * @param $stream
     * @param $domain
     */
    private function httpGetLive($type, $app, $stream, $domain)
    {
        $liveConfig = \Yii::$app->params['live'];
        $liveType = $type === 'publish_done' ? $liveConfig['forbid']: $liveConfig['resume'];
        $params = [
            'Action' => $liveType,
            'AppName' => $app,
            'DomainName' => $domain,
            'StreamName' => $stream,
            'LiveStreamType' => 'publisher',
            'Version' => $liveConfig['version'],
            'AccessKeyId' => $liveConfig['accessKeyId'],
            'SignatureMethod' => $liveConfig['signatureMethod'],
            'Timestamp' => CommonHelper::getTimestamp(),
            'SignatureVersion' => $liveConfig['signatureVersion'],
            'SignatureNonce' => uniqid(),
            'ResourceOwnerAccount' => $liveConfig['account'],
            'Format' => $liveConfig['format'],
        ];

        $sign = CommonHelper::getAliSign($params, $liveConfig['accessKeySecret']);
        $params['Signature'] = $sign;
        $res = CommonHelper::curl($liveConfig['url'], $params);
        return json_decode($res, true);
    }



}
