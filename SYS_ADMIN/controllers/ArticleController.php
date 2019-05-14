<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/13
 * Time: 8:45
 * 文章管理
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\SearchWidget;
use SYS_ADMIN\models\Article;
use SYS_ADMIN\models\Pictrue;
use yii\web\UploadedFile;

class ArticleController extends CommonController
{
    /**
     * 文章列表
     */
    public function actionIndex()
    {
        if (\Yii::$app->request->get('api')) {
            $list = Article::getList();
            return $this->successInfo($list);
        } else {
            $room_html = SearchWidget::instance()->liveRoom('room_id');
            return $this->render('list', [
                'room_html' => $room_html
            ]);
        }
    }

    /**
     * @return array|string
     * 文章详情
     */
    public function actionInfo()
    {
        $article_id = \Yii::$app->request->get('id');
        $article_id = intval($article_id);

        $room_id = 0;
        $pic_info = [];
        $model = Article::find()
            ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['id' => $article_id]);

        $info = $model->asArray()->one();
        if (!empty($info)) {
            if (!CommonHelper::checkRoomId($info['room_id'])) {
                return $this->render('/site/error', [
                    "message" => ConStatus::$ERROR_PARAMS_MSG,
                    "name" => "编辑直播间",
                ]);
            }

            $info['pic_path'] = "";
            $room_id = $info['room_id'];
            if (isset($info['cover'])) { // 封面图信息
                $pic_info = Pictrue::find()
                    ->where(['id' => $info['cover']])
                    ->asArray()
                    ->one();
            }


        }

        $room_html = SearchWidget::instance()->liveRoom('room_id', $room_id);
        return $this->render('info', [
            'info' => $info,
            'model' => $model,
            'pic_info' => $pic_info,
            'room_html' => $room_html,
            'room_id' => $room_id,
        ]);
    }


    /**
     * @return array|void
     * 删除
     */
    public function actionDel()
    {
        $id = \Yii::$app->request->post('id');
        $id = intval($id);

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        $model = Article::findOne(['id' => $id]);
        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, "参数错误");
        }

        if (!$this->isAdmin && !array_key_exists($model->room_id, $this->user_room)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, "参数错误");
        }

        $model->status = ConStatus::$STATUS_DELETED;
        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, "操作失败，请稍后重试");
        }
    }


    /**
     * 保存文章信息
     */
    public function actionSave()
    {
        $data = \Yii::$app->request->post();
        $id = \Yii::$app->request->post('id');
        $room_id = \Yii::$app->request->post('room_id');
        $title = \Yii::$app->request->post('title');
        $cover = \Yii::$app->request->post('cover');
        $content = \Yii::$app->request->post('content');
        $click_num = \Yii::$app->request->post('click_num', 10);
        $sort_num = \Yii::$app->request->post('sort_num', 10);
        $status = \Yii::$app->request->post('status', 1);

        $model = new Article();
        $model->attributes = $data;
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if (!empty($id)) {
            $model = Article::findOne($id);
            if (empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
            }

            if (!CommonHelper::checkRoomId($model->room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }
        }

        $model->title = $title;
        $model->content = $content;
        $model->sort_num = $sort_num;
        $model->click_num = $click_num;
        $model->room_id = $room_id;
        $model->status = $status;
        $model->cover = $cover;

        if (isset($_FILES['pcover']) && !empty($_FILES['pcover']['name'])) {
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('pcover');
            $img_list = $picModel->upload();
            if (isset($img_list['images'])) {
                $model->cover= $img_list['images'];
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_Upload, $img_list['info']);
            }
        }

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }

    }
}