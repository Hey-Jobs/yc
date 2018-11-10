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

    static $STATUS_LIST = [
        '1' => '正常',
        '2' => '暂停',
        '4' => '删除'
    ];
}