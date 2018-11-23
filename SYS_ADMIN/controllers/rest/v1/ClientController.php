<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 19:50
 */

namespace SYS_ADMIN\controllers\rest\v1;
use Codeception\Module\Cli;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\ClientAddr;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Order;
use SYS_ADMIN\models\Product;

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
        $products_list = [];
        $check_product = [];

        $products = \Yii::$app->request->post('room_id');
        $products = \Yii::$app->request->post('products');
        $product_money = \Yii::$app->request->post('product_money');
        $deliver_money = \Yii::$app->request->post('deliver_money');


        if(empty($products)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $products = json_decode($products, true);
        if(empty($products)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        foreach ($products as $item){
            $products_id[] =  $item['product_id'];
        }

        $products_list = Product::find()
            ->where(['in', 'id', $products_id])
            ->andWhere(['room_id' => $room_id])
            ->andWhere(['>', 'stock', 0])
            ->asArray()
            ->all();

        if()
        $order_status = ConStatus::$ORDER_NO_PAY;
        $order_id =  date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $total_money = 0; //总价格
        $real_total_money = 0; // 实际付款



        $model = new Order();
        $model->order_id = $order_id;
        $model->pay_type = ConStatus::$PAY_ONLINE; //线上支付


        if($model->save()){

        } else {

        }

    }


}