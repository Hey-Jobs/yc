<?php
namespace SYS_ADMIN\controllers;

use SYS_ADMIN\models\LiveRoom;

class FormController extends CommonController
{
    public function actionRoom()
    {
        $liveList = \SYS_ADMIN\models\LiveRoom::find()
            ->select(
                ['room_name as text',
                'id']
            )
            ->asArray()
            ->all();
        return $this->successInfo($liveList);
    }
}
