<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:51
 */

namespace SYS_ADMIN\controllers;


class ProductController extends CommonController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function list()
    {

    }
}