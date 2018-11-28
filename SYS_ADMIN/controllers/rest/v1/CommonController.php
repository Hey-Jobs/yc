<?php
namespace SYS_ADMIN\controllers\rest\v1;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Client;
use SYS_ADMIN\models\LiveRoom;

class CommonController extends \yii\rest\Controller
{
//    public $isAdmin = false;
//    public $user_room = [];
//    public function init()
//    {
//        parent::init(); // TODO: Change the autogenerated stub
//        $this->isAdmin = CommonHelper::isAdmin();
//        $this->user_room = LiveRoom::getUserRoomId();
//    }

    public $user_info;
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $base_url = \Yii::$app->request->getPathInfo();
        $action_name = str_replace("rest/v1/wechat/","", $base_url);
        $action_name = trim($action_name, '/');
        $no_auth = ['jssdk', 'auth-login', 'notify'];

        /*$open_id = "omIqUv9pP6EaM3tqd4UoAs4J4Ncw";
        $check_info = Client::findOne(['open_id' => $open_id]);
        $user_detail = [
            'user_name' => $check_info->client_name,
            'user_img' => $check_info->client_img,
            'open_id' => $check_info->open_id,
            'uid' => $check_info->id,
        ];
        $this->user_info = $user_detail;*/
        $open_id = \Yii::$app->request->post('openid');
        if(empty($open_id) && !in_array($action_name, $no_auth)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_OPENID, ConStatus::$ERROR_PARAMS_MSG);
        }

        $redis = \Yii::$app->redis;
        $user_info = $redis->get($open_id);
        if(empty($user_info) && !in_array($action_name, $no_auth)){
            return $this->errorInfo(ConStatus::$STATUS_ERROR_USER_EXIT, ConStatus::$ERROR_PARAMS_MSG);
        }

        $this->user_info = json_decode($user_info, true);
    }

    /**
     * 返回错误信息
     * {
     *   error: "请求参数有误。",
     *   message: "日志ID不能为空。",
     *   code: 4001,
     *   status: 400
     * }
     */
    public function errorInfo($code, $extend_message = '')
    {
        $route = $this->action->controller->module->requestedRoute ?? '';
        $result = [
            'timestamp' => time(),
            'status' => 400,
            'error' => $code,
            'message' => is_array($extend_message) ? join("\n", $extend_message) : $extend_message,
            'code' => $code,
            'path' => $route,
        ];
        echo json_encode($result);
        exit;
    }

    /**
     * 返回成功的信息
     * @param  array $data 返回的数据
     * @return array
     */
    public function successInfo($data = [])
    {
        $result = [
            'timestamp' => time(),
            'status' => 200,
            'data' => $data,
        ];
        echo json_encode($result);
        exit;
    }
}
