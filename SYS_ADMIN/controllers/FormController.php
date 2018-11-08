<?php
namespace SYS_ADMIN\controllers;

use SYS_ADMIN\components\BaseDataBuilder;
use SYS_ADMIN\models\LiveRoom;

class FormController extends CommonController
{
    public function actionRoom()
    {
        $liveList = BaseDataBuilder::instance('LiveRoom', false);
        return $this->successInfo($liveList);
    }
}
