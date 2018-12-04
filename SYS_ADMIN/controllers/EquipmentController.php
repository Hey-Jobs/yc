<?php
namespace SYS_ADMIN\controllers;

class EquipmentController extends CommonController
{
    /**
     * Site Index
     */
    public function actionIndex()
    {
        return $this->render('list');
    }
}
