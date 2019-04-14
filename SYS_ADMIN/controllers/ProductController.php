<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/10/20
 * Time: 11:51
 */

namespace SYS_ADMIN\controllers;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\SearchWidget;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Product;
use SYS_ADMIN\models\ProductDetail;
use SYS_ADMIN\models\ShoppingMall;
use yii\web\UploadedFile;

class ProductController extends CommonController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->get('api')) {
            $room_id = array_keys($this->user_room);
            // 管理员
            $model = Product::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED]);

            if (!$this->isAdmin) {
                $model->andWhere(['in', 'room_id', $room_id]);
            }

            $product_list = $model->asArray()->all();
            if (count($product_list) > 0) {
                $picid_list = array_column($product_list, 'cover_img');

                $pic_list = Pictrue::getPictrueList($picid_list);
                foreach ($product_list as &$product) {
                    $product['pic_path'] = isset($pic_list[$product['cover_img']]) ? $pic_list[$product['cover_img']]['pic_path'] : "";
                    $product['room_name'] = isset($this->user_room[$product['room_id']]) ? $this->user_room[$product['room_id']]['room_name'] : "";
                    $product['status'] = ConStatus::$STATUS_LIST[$product['status']];
                }

            }

            $this->successInfo($product_list);
        } else {
            return $this->render('list', [
                'is_admin' => $this->isAdmin
            ]);
        }
    }


    public function actionInfo()
    {
        $product_id = \Yii::$app->request->get('id', 0);
        $banner_info = [];
        $product_info = [];
        $product_detail = [];
        $pic_info = [];
        $room_id = 0;
        $mall_id = 0;


        if (!empty($product_id)) {
            $product_info = Product::find()
                ->where(['<>', 'status', ConStatus::$STATUS_DELETED])
                ->andWhere(['id' => $product_id])
                ->asArray()
                ->one();

            if (!CommonHelper::checkRoomId($product_info['room_id'])) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }

        }

        if (!empty($product_info)) {
            $pic_info = Pictrue::getPictrueById($product_info['cover_img']);
            $mall_id = $product_info['mall_id'];
        }

        $title = !empty($product_id) ? "编辑商品" : "新增商品";
        $mall_html = SearchWidget::instance()->mallList('mall_id', $mall_id);

        return $this->render("detail", [
            'info' => $product_info,
            'banner_info' => $banner_info,
            'product_id' => $product_id,
            'pic_info' => $pic_info,
            'title' => $title,
            'is_admin' => $this->isAdmin,
            'mall_html' => $mall_html,
        ]);
    }

    public function actionSave()
    {
        $id = \Yii::$app->request->post('id');
        $title = \Yii::$app->request->post('title');
        $desc = \Yii::$app->request->post('desc');
        $price = \Yii::$app->request->post('price');
        $sort_num = \Yii::$app->request->post('sort_num');
        $mall_id = \Yii::$app->request->post('mall_id');
        $cover_img = \Yii::$app->request->post('cover_img');
        $stock = \Yii::$app->request->post('stock');
        $status = \Yii::$app->request->post('status');

        $model = new Product();
        $model->attributes = \Yii::$app->request->post();
        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        $mall_info = ShoppingMall::findOne($mall_id);
        if (empty($mall_info)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (!empty($id)) { // 编辑
            $model = Product::findOne($id);
            if (empty($model)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, ConStatus::$ERROR_PARAMS_MSG);
            }

            if (!CommonHelper::checkRoomId($model->room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }

        }


        $model->title = $title;
        $model->desc = $desc;
        $model->price = $price;
        $model->stock = $stock;
        $model->status = $status;
        $model->cover_img = $cover_img;
        $model->sort_num = $sort_num;
        $model->mall_id = $mall_id;

        if (!empty($mall_info)) {
            $model->user_id = $mall_info->user_id;
            $model->room_id = $mall_info->room_id;
        }

        if (isset($_FILES['pcover_img']) && !empty($_FILES['pcover_img']['name'])) { // 上传封面
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstanceByName('pcover_img');
            $img_list = $picModel->upload();
            if (isset($img_list['images'])) {
                $model->cover_img = $img_list['images'];
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_Upload, $img_list['info']);
            }
        }

        if ($model->save()) {
            return $this->successInfo(['id' => $model->id]);
        } else {
            return $this->errorInfo(400);
        }
    }


    /**
     * 查看详情
     */
    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        $detail = [];
        $pic_list = [];

        $product_info = Product::findOne($id);
        $detail = ProductDetail::findOne(['product_id' => $id]);
        if (!empty($detail)) {
            if (!CommonHelper::checkRoomId($product_info->room_id)) {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
            }

            if (!empty($detail['banner_img'])) {
                $pic_id = explode(',', $detail['banner_img']);
                $pic_list = Pictrue::getPictrueList($pic_id);
            }
        }


        $title = "商品详情";
        return $this->render("ext_detail", [
            'info' => $detail,
            'title' => $title,
            'id' => $id,
            'pic_list' => $pic_list,
        ]);
    }

    /**
     * 保存详情
     */
    public function actionSaveDetail()
    {
        $id = \Yii::$app->request->post('product_id');
        $content = \Yii::$app->request->post('content');
        $banner_img = \Yii::$app->request->post('banner_img', ',');

        $banner = array_filter(explode(',', $banner_img));
        $model = new ProductDetail();
        $model->attributes = \Yii::$app->request->post();

        if (!$model->validate()) {
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        $product_info = Product::findOne($id);
        if (empty($product_info) || !CommonHelper::checkRoomId($product_info->room_id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, ConStatus::$ERROR_PARAMS_MSG);
        }

        if (isset($_FILES['pcover_img']) && isset($_FILES['pcover_img']['name'])
            && !empty($_FILES['pcover_img']['name'][0])) { // 多图上传
            $picModel = new Pictrue();
            $picModel->imageFile = UploadedFile::getInstancesByName('pcover_img');
            $img_list = $picModel->multiUpload();
            if (isset($img_list['images'])) {
                $banner = array_merge($banner, $img_list['images']);
            } else {
                return $this->errorInfo(ConStatus::$STATUS_ERROR_Upload, $img_list['info']);
            }
        }


        if (count($banner) > ConStatus::$PRODUCT_MAX_NUM) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_IMG_NUM, ConStatus::$ERROR_MSG_IMG_NUM);
        }

        $model = ProductDetail::findOne(['product_id' => $id]);
        if (empty($model)) {
            $model = new ProductDetail();
            $model->created_at = date('Y-m-d H:i:s');
        }

        $model->product_id = $id;
        $model->content = $content;
        $model->banner_img = implode($banner, ',');

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400);
        }
    }

    /**
     * @return array|void
     * 删除商品
     */
    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        $id = intval($id);

        if (empty($id)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ID, '参数错误');
        }

        $model = Product::find()
            ->where(['id' => $id])
            ->andWhere(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->one();

        if (empty($model)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_NONE, '参数错误');
        }

        if (!$this->isAdmin && array_key_exists($model->room_id, $this->user_room)) {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_ROOMID, '参数错误');
        }

        $model->status = ConStatus::$STATUS_DELETED;

        if ($model->save()) {
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(400, '操作失败，请稍后重试');
        }
    }
}