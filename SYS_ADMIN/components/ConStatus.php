<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/11/7
 * Time: 09:43
 */

namespace SYS_ADMIN\components;


class ConStatus
{
    // default page
    static $PAGE_NUM = 1;
    static $PAGE_SIZE = 10;

    // common 公共
    static $STATUS_PENDING = 0;
    static $STATUS_ENABLE = 1;
    static $STATUS_DISABLE = 2;
    static $STATUS_DELETED = 4;

    static $USER_ENABLE = 10;

    static $STATUS_LIST = [
        '1' => '正常',
        '2' => '暂停',
        '4' => '删除'
    ];

    // ORDER 订单
    static $ORDER_PENDING = 0;  // 待发货
    static $ORDER_SENDED = 1;   // 已发货
    static $ORDER_DELIVERY = 2; // 配送中
    static $ORDER_USER_WAIT_DELIVERY = 3;    // 用户待收货
    static $ORDER_USER_DELIVERIED = 4;   // 用户确认收货
    static $ORDER_USER_REJECT = 5;   // 用户拒收
    static $ORDER_NO_PAY = 6;   // 未付款的订单
    static $ORDER_CANCEL = 7;   // 用户取消

    static $ORDER_LIST = [
        0 => '待发货',
        1 => '已发货',
        2 => '配送中',
        3 => '用户待收货',
        4 => '用户确认收货',
        5 => '用户拒收',
        6 => '未付款的订单',
        7 => '用户取消',
    ];

    static $SEX = [
        1 => '男',
        2 => '女',
    ];

    // COMMENT 评论
    static $COMMENT_TYPE_ROOM = 1;
    static $COMMENT_TYPE_PROD = 2;


    //商品图片数量
    static $PRODUCT_MAX_NUM = 10;
    static $ERROR_MSG_IMG_NUM = "最多只能上传10张图片";

    //地址
    static $ADDR_COMMON = 1;

    // 状态码
    static $ERROR_PARAMS_MSG = "参数错误";

    static $STATUS_SUCCESS = 200; // 获取成功
    static $STATUS_ERROR_SYS = 4000; // 服务器错误
    static $STATUS_ERROR_PARAMS = 4001; //validate 校验不通过
    static $STATUS_ERROR_ROOMID = 4002; // 无效质检ID
    static $STATUS_ERROR_Upload = 4003; // 图片上传失败
    static $STATUS_ERROR_ID = 4004; // ID 不能为空
    static $STATUS_ERROR_NONE = 4005; // 信息不存在
    static $STATUS_ERROR_IMG_NUM = 4006; // 信息不存在
}