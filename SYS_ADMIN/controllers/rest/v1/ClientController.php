<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 19:50
 */

namespace SYS_ADMIN\controllers\rest\v1;
use app\models\Comment;
use Codeception\Module\Cli;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\ClientAddr;
use SYS_ADMIN\models\ClientStart;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Order;
use SYS_ADMIN\models\OrderDetail;
use SYS_ADMIN\models\Product;
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
        $uid = 1;
        $addr_list = [];
        $addr = ClientAddr::find()
            ->where(['user_id' => $uid])
            ->asArray()
            ->all();

        if(count($addr)){
            foreach ($addr as $v){
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
        $user_id = 1;
        $aid = \Yii::$app->request->post('aid');

        $model = ClientAddr::find()
            ->where(['id' => $aid, 'user_id' => $user_id])
            ->one();

        if(empty($model)){
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
        $user_id = 1;
        $add_info = [];
        $add_info = ClientAddr::find()
            ->where(['user_id' => $user_id])
            ->orderBy('id desc')
            ->asArray()
            ->one();

        if($add_info){
            $common_addr =  ClientAddr::find()
                ->where(['user_id' => $user_id])
                ->where(['common' => 1])
                ->asArray()
                ->one();

            if($common_addr){
                $add_info = $common_addr;
            }
        }

        if($add_info){
            $add_info['sex'] = ConStatus::$SEX[$add_info['client_sex']];
        }
        return $this->successInfo($add_info);
    }

    /**
     * 新增 | 编辑地址
     */
    public function actionAddrSave()
    {
        $uid = 1;
        $aid = \Yii::$app->request->post('aid');
        $name = \Yii::$app->request->post('client_name');
        $mobile = \Yii::$app->request->post('mobile');
        $sex = \Yii::$app->request->post('client_sex');
        $addr = \Yii::$app->request->post('addr');
        $detail = \Yii::$app->request->post('detail');
        $common = \Yii::$app->request->post('common', 0);

        $model = new ClientAddr();
        $model->attributes = \Yii::$app->request->post();
        if(!$model->validate()){
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if(!empty($aid)){
            $model = ClientAddr::findOne($aid);
            if($model->user_id != $uid || empty($model)){
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

        if($model->save()){
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

    }

    /**
     * 提交订单
     */
    public function actionOrderSub()
    {
        $user_id = 1;
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
        $model->deliver_money = 20; //运费
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
            $detail_data = [];
            foreach ($products_list as $item){
                $detail_data[] = [
                    'client_id' => $this->user_info['uid'],
                    'product_id' => $item['id'],
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'cover_img' => $item['cover_img'],
                    'num' => $item['num'],
                ];
            }

            $res = Yii::$app->db->createCommand()
                ->batchInsert(
                    OrderDetail::tableName(),
                    ['client_id','product_id', 'title', 'price', 'cover_img', 'num'],
                    $detail_data)
                ->execute();
            if($res){
                $connection->commit();
                return $this->successInfo(true);
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


}