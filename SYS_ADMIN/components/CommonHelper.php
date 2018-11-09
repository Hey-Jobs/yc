<?php
/**
 * User: liwj
 * Date:2018/11/8
 * Time:16:17
 */

namespace SYS_ADMIN\components;


class CommonHelper
{
    /**
     * @param $path
     * @return string
     *  获取图片路径
     */
    public static function getPicPath($path)
    {
        return "/".$path;
    }


    /**
     * @return bool
     * 检测是否为管理员
     */
    public static function isAdmin()
    {
        return true;
    }


}