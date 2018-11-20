<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 19:51
 */

namespace SYS_ADMIN\controllers\rest\v1;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\Product;

/**
 * Class productController
 * @package SYS_ADMIN\controllers\rest\v1
 * 商品相关
 */
class ProductController extends CommonController
{
    /**
     * 直播间商品
     */
    public function actionRoom()
    {
        $id = \Yii::$app->request->get('id');

        $liveRoom = LiveRoom::findOne($id);
        if(empty($liveRoom)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
        }

        $products = Product::find()
            ->where(['room_id' => $id])
            ->andWhere(['<>', 'status', ConStatus::$STATUS_DELETED])
            ->andWhere(['>', 'stock', 0])
            ->select(['title', 'desc', 'price', 'stock', 'cover_img'])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        if(count($products)) {
            $pic_id = array_column($products, 'cover_img');
            $pic_list = Pictrue::getPictrueList($pic_id);

            foreach ($products as &$p){
                $p['pic'] = isset($pic_list[$p['cover_img']]) ? $pic_list[$p['cover_img']]['pic_path'] : "";
                $p['buy_num'] = 0;
            }
        }

        return $this->successInfo($products);
    }
}