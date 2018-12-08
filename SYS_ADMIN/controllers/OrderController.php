<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:51
 */

namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\BaseDataBuilder;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\Express;
use SYS_ADMIN\models\Order;
use SYS_ADMIN\models\Product;
use Yii;

class OrderController extends CommonController
{
    /**
     * index
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('api')) {

            $room_id = array_keys($this->user_room);
            $query = Order::find()
                ->select(['*']);

            if (!$this->isAdmin) {
                $query->andWhere(['in', 'room_id', $room_id]);
            }

            $list = $query->asArray()->all();

            $roomPairs = BaseDataBuilder::instance('LiveRoom');
            foreach ($list as $key => $row) {
                $list[$key]['room_name'] = $roomPairs[$row['room_id']] ?? '';
                $list[$key]['order_status_name'] = ConStatus::$ORDER_LIST[$row['order_status']] ?? '';
                $list[$key]['express_name'] = Express::$EXPRESS[$row['express_id']] ?? '';
                $list[$key]['create_time'] = date('Y-m-d H:i', strtotime($row['create_time']));
            }
            return $this->successInfo($list);
        } else {
            return $this->render('list');
        }
    }

    /**
     * get one
     */
    public function actionOne()
    {
        $id = Yii::$app->request->get('id');
        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, 'Id is not empty');
        }

        $list = Order::findOne($id)->toArray();
        if (!CommonHelper::checkRoomId($list['room_id'])) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        return $this->successInfo($list);
    }

    /**
     * 发货
     */
    public function actionSend()
    {
        $id = Yii::$app->request->post('id');
        $expressId = Yii::$app->request->post('express_id');
        $expressNo = Yii::$app->request->post('express_no');

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, 'Id is not empty');
        }

        $orderM = Order::findOne($id);
        if (!CommonHelper::checkRoomId($orderM->room_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $orderM->express_id = $expressId;
        $orderM->express_no = $expressNo;
        $orderM->order_status = ConStatus::$ORDER_SENDED;
        if (!$orderM->save()) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS, 'error');
        }

        return $this->successInfo(ConStatus::$STATUS_SUCCESS);
    }
}