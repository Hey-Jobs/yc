<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/9
 * Time: 16:39
 */

namespace SYS_ADMIN\controllers\console;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Equipment;
use SYS_ADMIN\models\EquipmentTask;
use yii\console\Controller;

class EquipmentController extends Controller
{
    /**
     * 定时推断流
     */
    public function actionCron()
    {
        $curTime = date('H:i');
        $list = EquipmentTask::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['task_time' => $curTime])
            ->asArray()
            ->all();

        if (count($list)) {
            $taskType = array_column($list, 'task_type', 'equip_id');
            $equipId = array_column($list, 'equip_id');
            $equipList = Equipment::find()
                ->where(['status' => ConStatus::$STATUS_ENABLE])
                ->andWhere(['in', 'id', $equipId])
                ->asArray()
                ->all();

            $taskDict = array_flip(ConStatus::$STREAM_STATUS);
            foreach ($equipList as $item) {
                $pushTye = $taskDict[$taskType[$item['id']]];
                CommonHelper::httpGetLive($pushTye, $item['appname'], $item['stream'], $item['app']);
            }

        }
    }
}