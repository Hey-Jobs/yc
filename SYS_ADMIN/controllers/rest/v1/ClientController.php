<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 19:50
 */

namespace SYS_ADMIN\controllers\rest\v1;
use abei2017\wx\Application;
use app\models\Comment;
use Codeception\Module\Cli;
use SYS_ADMIN\components\ArrayHelper;
use SYS_ADMIN\components\BaseDataBuilder;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\Express;
use SYS_ADMIN\models\ClientAddr;
use SYS_ADMIN\models\ClientStart;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Log;
use SYS_ADMIN\models\Order;
use SYS_ADMIN\models\OrderDetail;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Product;
use SYS_ADMIN\models\User;
use SYS_ADMIN\models\Video;

/**
 * Class ClientController
 * @package SYS_ADMIN\controllers\rest\v1
 * 用户信息相关
 */
class ClientController extends CommonController
{
    /**
     *  用户地址管理
     */
    public function actionAddrList()
    {
        $uid = $this->user_info['uid'];
        $addr_list = [];
        $addr = ClientAddr::find()
            ->where(['user_id' => $uid])
            ->asArray()
            ->all();

        if (count($addr)) {
            foreach ($addr as $v) {
                $addr_list[] = [
                    'aid' => $v['id'],
                    'sex' => $v['client_sex'],
                    'name' => $v['client_name'],
                    'mobile' => $v['mobile'],
                    'addr' => $v['addr'],
                    'detail' => $v['detail'],
                    'common' => $v['common'],
                ];
            }
        }

        return $this->successInfo($addr_list);
    }

    /**
     * 设置默认地址
     */
    public function actionAddrDefault()
    {
        $user_id = $this->user_info['uid'];
        $aid = \Yii::$app->request->post('aid');

        $model = ClientAddr::find()
            ->where(['id' => $aid, 'user_id' => $user_id])
            ->one();

        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        ClientAddr::updateAll(['common' => 0], ['user_id' => $user_id]);
        $model->common = ConStatus::$ADDR_COMMON;
        $model->save();

        $this->successInfo(true);
    }

    /**
     * 获取当前收货地址
     */
    public function actionAddr()
    {
        $user_id = $this->user_info['uid'];
        $add_info = [];
        $add_info = ClientAddr::find()
            ->where(['user_id' => $user_id])
            ->orderBy('id desc')
            ->asArray()
            ->one();

        if ($add_info) {
            $common_addr = ClientAddr::find()
                ->where(['user_id' => $user_id])
                ->andWhere(['common' => 1])
                ->asArray()
                ->one();

            if ($common_addr) {
                $add_info = $common_addr;
            }
        }

        if ($add_info) {
            $add_info['sex'] = ConStatus::$SEX[$add_info['client_sex']];
        }
        return $this->successInfo($add_info);
    }

    /**
     * 新增 | 编辑地址
     */
    public function actionAddrSave()
    {
        $uid = $this->user_info['uid'];
        $aid = \Yii::$app->request->post('aid');
        $name = \Yii::$app->request->post('client_name');
        $mobile = \Yii::$app->request->post('mobile');
        $sex = \Yii::$app->request->post('client_sex');
        $addr = \Yii::$app->request->post('addr');
        $detail = \Yii::$app->request->post('detail');
        $common = \Yii::$app->request->post('common', 0);

        $model = new ClientAddr();
        $model->attributes = \Yii::$app->request->post();
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!empty($aid)) {
            $model = ClientAddr::findOne($aid);
            if ($model->user_id != $uid || empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
            }
        }

        $model->client_name = $name;
        $model->client_sex = $sex;
        $model->user_id = $uid;
        $model->mobile = $mobile;
        $model->addr = $addr;
        $model->detail = $detail;
        $model->common = $common;

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS);
        }
    }

    /**
     * 订单列表
     */
    public function actionOrders()
    {
        $last = \Yii::$app->request->get('last');
        $order_id = \Yii::$app->request->post('order_id');
        $clientId = $this->user_info['uid'];

        $query = Order::find()
            ->select([
                'id',
                'order_id',
                'room_id',
                'client_id',
                'order_status',
                'real_total_money',
                'total_money',
                'user_name',
                'user_address',
                'user_phone',
                'express_id',
                'deliver_money',
                'create_time'
            ])
            ->where(['in', 'order_status', [
                ConStatus::$ORDER_PENDING,
                ConStatus::$ORDER_SENDED,
                ConStatus::$ORDER_DELIVERY,
                ConStatus::$ORDER_USER_WAIT_DELIVERY,
                ConStatus::$ORDER_USER_DELIVERIED,
                ConStatus::$ORDER_USER_REJECT,
                ConStatus::$ORDER_PAY_FINISH,
                ]])
            ->andWhere(['client_id' => $clientId])
            ->orderBy('create_time desc');

        if ($last) {
            $query->limit(1);
        }

        if($order_id){ // 查看订单详情
            $query->andWhere(['order_id' => $order_id]);
        }

        $orderList = $query->asArray()->all();
        if(empty($orderList)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $orderDetails = OrderDetail::find()
            ->select(['order_id', 'title', 'price', 'num', 'cover_img'])
            ->where(['order_id' => array_column($orderList, 'id')])
            ->asArray()
            ->all();

        $pic_list = Pictrue::getPictrueList(array_column($orderDetails, 'cover_img'));
        if(count($pic_list)){
            foreach ($orderDetails as &$od){
                $pic_path = isset($pic_list[$od['cover_img']]) ? $pic_list[$od['cover_img']]['pic_path'] : "";
                $od['cover_img'] = $pic_path;
            }
        }
        $orderDetails = CommonHelper::array_group_by($orderDetails, 'order_id');

//        $roomPairs = BaseDataBuilder::instance('LiveRoom');

        $data = [];
        $room_list = LiveRoom::find()
            ->where(['in', 'id', array_column($orderList, 'room_id')])
            ->select(['id', 'room_name', 'logo_img', 'user_id'])
            ->indexBy('id')
            ->asArray()
            ->all();

        //$room_list = CommonHelper::array_group_by($room_info, 'id');
        $logo_pic = Pictrue::getPictrueList(array_column($room_list, 'logo_img'));
        $user_info = User::find()
            ->where(['in', 'id', array_column($room_list, 'user_id')])
            ->select(['id', 'phone'])
            ->indexBy('id')
            ->asArray()
            ->all();

       foreach ($orderList as $key => $row) {
            $logo_pic_tmp = isset($room_list[$row['room_id']]) ? $logo_pic[$room_list[$row['room_id']]['logo_img']]['pic_path'] : "";
            $data[$key]['order_id'] = $row['order_id'] ?? '';
            $data[$key]['room_name'] = isset($room_list[$row['room_id']]) ?  $room_list[$row['room_id']]['room_name']: "";
            $data[$key]['logo_img'] = $logo_pic_tmp;
            $data[$key]['mobile'] = isset($room_list[$row['room_id']]) ? $user_info[$room_list[$row['room_id']]['user_id']]['phone'] : '';
            $data[$key]['order_status'] = ConStatus::$ORDER_LIST[$row['order_status']] ?? '';
            $data[$key]['total_money'] = $row['real_total_money'] ?? '';
            $data[$key]['deliver_money'] = $row['deliver_money'] ?? '';
            $data[$key]['discount_money'] = round(($row['deliver_money'] + $row['total_money']) - $row['real_total_money'], 2);
            $data[$key]['user_name'] = $row['user_name'] ?? '';
            $data[$key]['user_address'] = $row['user_address'] ?? '';
            $data[$key]['user_phone'] = $row['user_phone'] ?? '';
            $data[$key]['create_time'] = $row['create_time'] ?? '';
            $data[$key]['express_name'] = Express::$EXPRESS[$row['express_id']] ?? '';
            $data[$key]['list'] = $orderDetails[$row['id']] ?? '';
        }

        return $this->successInfo($data);
    }

    /**
     * 提交订单
     */
    public function actionOrderSub()
    {
        $user_id = $this->user_info['uid'];
        $room_id = 0 ;

        $products_id = [];
        $product_num = [];
        $products_list = [];
        $check_product = [];

        $room_id = \Yii::$app->request->post('room_id');
        $products = \Yii::$app->request->post('products');
        $user_name = \Yii::$app->request->post('user_name');
        $user_sex = \Yii::$app->request->post('user_sex');
        $user_address = \Yii::$app->request->post('address');
        $user_phone = \Yii::$app->request->post('user_phone');


        if(empty($products)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $products = json_decode($products, true);
        if(empty($products)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        foreach ($products as $item){
            $products_id[] = $item['product_id'];
            $product_num[$item['product_id']] = $item['num'];
        }

        $products_list = Product::find()
            ->where(['in', 'id', $products_id])
            ->andWhere(['room_id' => $room_id])
            ->andWhere(['>', 'stock', 0])
            ->asArray()
            ->all();


        $order_status = ConStatus::$ORDER_NO_PAY;
        $order_id =  date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $total_money = 0; //总价格
        $real_total_money = 0; // 实际付款
        foreach ($products_list as &$pro){
            $pro['num'] = $product_num[$pro['id']];
            $total_money += round($pro['price'] * $product_num[$pro['id']], 2);
        }

        $connection = \Yii::$app->db->beginTransaction();
        $model = new Order();
        $model->order_id = $order_id;
        $model->client_id = $this->user_info['uid'];
        $model->order_status = ConStatus::$ORDER_NO_PAY;
        $model->deliver_money = 0; //运费
        $model->total_money = $total_money;
        $model->real_total_money = $model->deliver_money + $total_money;
        $model->pay_type = ConStatus::$PAY_ONLINE; //线上支付
        $model->pay_from = ConStatus::$PAY_WAY_WECHAT;
        $model->is_pay = 2;

        // 收件人
        $model->user_name = $user_name;
        $model->user_sex = $user_sex;
        $model->user_address = $user_address;
        $model->user_phone = $user_phone;


        // 订单详情
        if($model->save()){
            $product_detail = "";
            $detail_data = [];
            foreach ($products_list as $item){
                $product_detail .= $item['title'].'×'.$item['num'].'#';
                $detail_data[] = [
                    'order_id' => $model->id,
                    'client_id' => $this->user_info['uid'],
                    'product_id' => $item['id'],
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'cover_img' => $item['cover_img'],
                    'num' => $item['num'],
                ];
            }

            $res = \Yii::$app->db->createCommand()
                ->batchInsert(
                    OrderDetail::tableName(),
                    ['order_id', 'client_id','product_id', 'title', 'price', 'cover_img', 'num'],
                    $detail_data)
                ->execute();
            if($res){
                $connection->commit();

                $conf = \Yii::$app->params['wx']['mp'];
                $wechat = new Application(['conf'=>$conf]);
                $payment = $wechat->driver("mp.pay");

                $attributes = [
                    'body'=>$product_detail."#{$order_id}",
                    'detail'=>"商品购买#{$order_id}",
                    'out_trade_no'=>$order_id,
                    'total_fee'=> $model->real_total_money * 100,
                    'notify_url'=> \Yii::$app->urlManager->createAbsoluteUrl(['/rest/v1/wechat/notify']),
                    'openid'=> $this->user_info['open_id'],
                ];

                $jsApi = $payment->js($attributes);
                if($jsApi['return_code'] == 'SUCCESS' && $jsApi['result_code'] == 'SUCCESS'){
                    $prepayId = $jsApi['prepay_id'];
                    $arr = $payment->configForPayment($prepayId);
                }

                return $this->successInfo(['pay' => $arr, 'order_no'=> $order_id]);
            } else {
                $connection->rollBack();
                CommonHelper::writeOrderLog($detail_data);
                CommonHelper::writeOrderLog($res);
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ORDER_DETAIL, ConStatus::$ERROR_SYS_MSG);
            }
        } else {
            $connection->rollBack();
            CommonHelper::writeOrderLog($model->toArray());
            CommonHelper::writeOrderLog($model->getErrors());
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ORDER_CREATE, ConStatus::$ERROR_SYS_MSG);
        }

    }


    /**
     * 点赞
     */
    public function actionUserStart()
    {
        $user_id = $this->user_info['uid'];
        $id = \Yii::$app->request->post('id', 0);
        $stype = \Yii::$app->request->post('stype', 0);

        if(!array_key_exists($stype, ConStatus::$CLIENT_START)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        switch ($stype){
            case ConStatus::$CLIENT_START_VIDEO : // 视频
                $info = Video::findOne($id);
                break;
            case ConStatus::$CLIENT_START_COMMENT : // 评论
                $info = Comment::findOne($id);
                break;
        }

        if(empty($info)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $model = ClientStart::find()
            ->where(['target_id' => $id, 'client_id' => $user_id, 'stype' => $stype])
            ->one();

        if(empty($model)){ // 收藏
            $model = new ClientStart();
            $model->target_id = $id;
            $model->client_id = $user_id;
            $model->stype = $stype;
            $model->save();

        } else { // 取消收藏
            $model->delete();
        }

        $this->successInfo(true);
    }
    /**
     * 用户评论
     * 商品
     */
    public function actionComment()
    {
        $room_id = \Yii::$app->request->post('id');
        $content = \Yii::$app->request->post('content');

        $log['from_id'] = $room_id;
        $logM = new Log();
        $logM->content = json_encode($log);
        $logM->url = $this->action->controller->module->requestedRoute ?? '';
        $logM->save();

        $model = new Comment();
        $model->type = ConStatus::$COMMENT_TYPE_ROOM; // 评论直播间
        $model->from_id = $room_id;
        $model->client_id = $this->user_info['uid'];
        $model->nickname = $this->user_info['user_name'];
        $model->thumb_img = $this->user_info['user_img'];
        $model->content = $content;

        if($model->save()){
            $data = $model->toArray();
            return $this->successInfo($data);
        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_SYS_MSG);
        }
    }

    public function actionTest()
    {
        $product_detail = "test";
        $order_id = "123456";
        $attributes = [
            'body'=>$product_detail."#{$order_id}",
            'detail'=>"test#{$order_id}",
            'out_trade_no'=>$order_id,
            'total_fee'=> 1,
            'notify_url'=> \Yii::$app->urlManager->createAbsoluteUrl(['/rest/v1/wechat/notify']),
            'openid'=> $this->user_info['open_id'],
        ];

        $conf = \Yii::$app->params['wx']['mp'];
        $wechat = new Application(['conf'=>$conf]);
        $payment = $wechat->driver("mp.pay");
        $jsApi = $payment->js($attributes);
        if($jsApi['return_code'] == 'SUCCESS' && $jsApi['result_code'] == 'SUCCESS'){
            $prepayId = $jsApi['prepay_id'];
            $arr = $payment->configForPayment($prepayId);
        } else {
        }

        return false;
    }


}