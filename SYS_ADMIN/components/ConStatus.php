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

    static $STATUS_ENABLE = 1;
    static $STATUS_DISABLE = 2;
    static $STATUS_DELETED = 4;

    static $USER_ENABLE = 10;

    static $STATUS_LIST = [
        '1' => '正常',
        '2' => '暂停',
        '4' => '删除'
    ];

    Static $ERROR_PARAMS_MSG = "参数错误";
    static $STATUS_SUCCESS = 200; // 获取成功
    static $STATUS_ERROR_PARAMS = 4001; //validate 校验不通过
    static $STATUS_ERROR_ROOMID = 4002; // 无效质检ID
    static $STATUS_ERROR_Upload = 4003; // 图片上传失败
    static $STATUS_ERROR_ID = 4004; // ID 不能为空
    static $STATUS_ERROR_NONE = 4005; // 信息不存在


}