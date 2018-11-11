<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:51
 */

namespace SYS_ADMIN\controllers;

use app\models\Comment;
use Yii;
use SYS_ADMIN\components\BaseDataBuilder;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;

class CommentController extends CommonController
{
    /**
     * Index
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('api')) {
            $list = Comment::find()
                ->select(['*'])
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->filterWhere(['user_id' => isset($this->isAdmin) ? null : \Yii::$app->user->id])
                ->asArray()
                ->all();

            $roomPairs = BaseDataBuilder::instance('LiveRoom');
            $productPairs = BaseDataBuilder::instance('Product');
            foreach ($list as $key => $row) {
                if ($row['type'] == ConStatus::$COMMENT_TYPE_ROOM) {
                    $list[$key]['source_name'] = '直播间';
                    $list[$key]['from_name'] = $roomPairs[$row['type']] ?? '';
                } else if ($row['type'] == ConStatus::$COMMENT_TYPE_PROD) {
                    $list[$key]['source_name'] = '商品';
                    $list[$key]['from_name'] = $productPairs[$row['type']] ?? '';
                } else {
                    $list[$key]['source_name'] = '';
                    $list[$key]['from_name'] = '';
                }

                $list[$key]['status_name'] = ConStatus::$STATUS_LIST[$row['status']] ?? '';
            }


            return $this->successInfo($list);
        } else {
            return $this->render('list');
        }
    }

    /**
     * delete
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');

        if (empty($id)) {
            return $this->errorInfo(400, 'Id is not empty');
        }

        $commentM = Comment::findOne($id);
//        if (!CommonHelper::checkRoomId($shoreMapM->room_id)) {
//            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
//        }

        $commentM->status = ConStatus::$STATUS_DELETED;
        if (!$commentM->save()) {
            print_r($commentM->getErrors());
            return $this->errorInfo('400', 'error');
        }

        return $this->successInfo(200);
    }
}