<?php

namespace SYS_ADMIN\models;

use Yii;

/**
 * This is the model class for table "sys_order".
 *
 * @property integer $id
 * @property string $order_id
 * @property integer $room_id
 * @property integer $client_id
 * @property integer $order_status
 * @property string $product_money
 * @property string $deliver_type
 * @property string $deliver_money
 * @property string $total_money
 * @property string $real_total_money
 * @property integer $pay_type
 * @property integer $pay_from
 * @property integer $is_pay
 * @property string $user_name
 * @property string $user_address
 * @property string $user_phone
 * @property integer $is_invoice
 * @property string $invoice_client
 * @property string $order_remarks
 * @property integer $order_source
 * @property integer $is_appraise
 * @property string $cancel_reason_id
 * @property integer $is_finished
 * @property string $delivery_time
 * @property string $receive_time
 * @property integer $express_id
 * @property string $express_no
 * @property integer $trade_no
 * @property string $create_time
 * @property string $updated_time
 */
class Order extends CommonModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id'], 'required'],
            [['id', 'room_id', 'client_id', 'pay_type', 'pay_from', 'is_pay', 'is_invoice', 'order_source', 'is_appraise', 'is_finished', 'express_id', 'trade_no', 'order_status'], 'integer'],
            [['product_money', 'deliver_money', 'total_money', 'real_total_money'], 'number'],
            [['delivery_time', 'receive_time', 'create_time', 'updated_time'], 'safe'],
            [['order_id', 'user_name'], 'string', 'max' => 32],
            [['deliver_type', 'user_address', 'invoice_client', 'order_remarks', 'cancel_reason_id'], 'string', 'max' => 255],
            [['user_phone', 'express_no'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'room_id' => 'Room ID',
            'client_id' => 'ClientController ID',
            'order_status' => 'Order Status',
            'product_money' => 'Product Money',
            'deliver_type' => 'Deliver Type',
            'deliver_money' => 'Deliver Money',
            'total_money' => 'Total Money',
            'real_total_money' => 'Real Total Money',
            'pay_type' => 'Pay Type',
            'pay_from' => 'Pay From',
            'is_pay' => 'Is Pay',
            'user_name' => 'User Name',
            'user_address' => 'User Address',
            'user_phone' => 'User Phone',
            'is_invoice' => 'Is Invoice',
            'invoice_client' => 'Invoice ClientController',
            'order_remarks' => 'Order Remarks',
            'order_source' => 'Order Source',
            'is_appraise' => 'Is Appraise',
            'cancel_reason_id' => 'Cancel Reason ID',
            'is_finished' => 'Is Finished',
            'delivery_time' => 'Delivery Time',
            'receive_time' => 'Receive Time',
            'express_id' => 'Express ID',
            'express_no' => 'Express No',
            'trade_no' => 'Trade No',
            'create_time' => 'Create Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
