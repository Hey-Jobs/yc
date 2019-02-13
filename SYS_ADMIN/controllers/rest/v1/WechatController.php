<?php
/**
 * User: liwj
 * Date:2018/11/26
 * Time:11:03
 */

namespace SYS_ADMIN\controllers\rest\v1;


use abei2017\wx\Application;
use Codeception\Module\Cli;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\Wechat;
use SYS_ADMIN\models\Client;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Order;
use SYS_ADMIN\models\OrderDetail;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\web\Controller;

class WechatController extends CommonController
{
    public $enableCsrfValidation = false;

    public function actionAuthLoginBack()
    {
        $refer = \Yii::$app->request->get('refer', '');
        $conf = \Yii::$app->params['wx']['mp'];

        $url = \Yii::$app->request->getUrl();
        $callback = \Yii::$app->urlManager->createAbsoluteUrl(['/wechat/oauth-login-back', 'url' => urlencode($url)]);
        $conf['oauth']['callback'] = $callback;
        $oauth = (new Application(['conf' => $conf]))->driver('mp.oauth');
        $wxLoginUser = \Yii::$app->session->get('wx_login_user');
        if ($wxLoginUser == null) {
            $oauth->send();
            die();
        }

        $user_info = $oauth->user();
        $check_info = Client::findOne(['open_id' => $user_info['openid']]);

        if (empty($check_info)) {
            $model = new Client();
            $model->client_name = $user_info['nickname'];
            $model->client_img = $user_info['headimgurl'];
            $model->open_id = $user_info['openid'];
            $model->client_name = $user_info['nickname'];
            $model->sex = $user_info['sex'];
            $model->city = $user_info['city'];
            $model->subscribe = $user_info['subscribe'];
            $model->save();

            $user_detail = [
                'user_name' => $user_info['nickname'],
                'user_img' => $user_info['headimgurl'],
                'open_id' => $user_info['openid'],
                'uid' => $model->id,
                'subscribe' => $user_info['subscribe'],
            ];

        } else {
            $check_info->subscribe = $user_info['subscribe'];
            $check_info->save();  // 更新是否关注
            $user_detail = [
                'user_name' => $check_info->client_name,
                'user_img' => $check_info->client_img,
                'open_id' => $check_info->open_id,
                'uid' => $check_info->id,
                'subscribe' => $user_info->subscribe,
            ];
        }

        $redis = \Yii::$app->redis;
        $redis->set($user_info['openid'], json_encode($user_detail));
        $redis->expire($user_info['openid'], 7200); // 缓存2小时
        setcookie('auth', $user_info['openid'], time() + 7200, '/');
        \Yii::$app->session->set('wx_login_user', json_encode($user_detail));
        $redirect = $refer ? $refer : CommonHelper::getDomain() . '/front/#/';
        //return $this->redirect($redirect);
        var_dump($refer);
    }


    public function actionAuthLogin()
    {
        $code = \Yii::$app->request->get('code');
        $refer = \Yii::$app->request->get('refer', '');
        $appid = Wechat::$APPID;
        $appsecret = Wechat::$APPSECRET;
        if (empty($code)) {
            //$redirec_url = CommonHelper::getDomain()."/rest/v1/wechat/auth-login";
            $redirec_url = CommonHelper::getDomain() . $_SERVER['REQUEST_URI'];
            $redirec_url = urlencode($redirec_url);

            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirec_url}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            return $this->redirect($url);
        }

        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
        $auth_info = file_get_contents($token_url);
        $auth_info = json_decode($auth_info, true);

        if (isset($auth_info['access_token'])) {
            $user_detail = [];
            $check_info = Client::findOne(['open_id' => $auth_info['openid']]);
            //获取用户信息
            $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$auth_info['access_token']}&openid={$auth_info['openid']}&lang=zh_CN";
            $user_info = file_get_contents($user_url);
            $user_info = json_decode($user_info, true);
            $user_info['subscribe'] = $user_info['subscribe'] ?? 0;
            if (empty($check_info)) {

                $model = new Client();
                $model->client_name = $user_info['nickname'];
                $model->client_img = $user_info['headimgurl'];
                $model->open_id = $user_info['openid'];
                $model->client_name = $user_info['nickname'];
                $model->sex = $user_info['sex'];
                $model->city = $user_info['city'];
                $model->subscribe = $user_info['subscribe'];
                $model->save();

                $user_detail = [
                    'user_name' => $user_info['nickname'],
                    'user_img' => $user_info['headimgurl'],
                    'open_id' => $user_info['openid'],
                    'uid' => $model->id,
                    'subscribe' => $user_info['subscribe'],
                ];

            } else {
                $check_info->subscribe = $user_info['subscribe'];
                $check_info->save();  // 更新是否关注
                $user_detail = [
                    'user_name' => $check_info->client_name,
                    'user_img' => $check_info->client_img,
                    'open_id' => $check_info->open_id,
                    'uid' => $check_info->id,
                    'subscribe' => $user_info['subscribe'],
                ];
            }

            $redis = \Yii::$app->redis;
            $redis->set($auth_info['openid'], json_encode($user_detail));
            $redis->expire($auth_info['openid'], 7200); // 缓存2小时
            setcookie('uid', $auth_info['openid'], time() + 7200, '/');
            setcookie('uname', $user_detail['user_name'], time() + 7200, '/');
            setcookie('uimg', $user_detail['user_img'], time() + 7200, '/');

            $redirect = $refer ? $refer : CommonHelper::getDomain() . '/front/#/';
            return $this->redirect($redirect);

        } else { // 授权失败
            //return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $auth_info['errmsg']);
            echo "授权失败:" . $auth_info['errmsg'];
        }

    }

    public function actionNotify()
    {
        date_default_timezone_set("Asia/Shanghai");
        $conf = \Yii::$app->params['wx']['mp'];
        $pay = (new Application(['conf' => $conf]))->driver("mp.pay");

        $response = $pay->handleNotify(function ($notify, $isSuccess) {
            if ($isSuccess) {
                //CommonHelper::writeOrderLog($notify);
                //$notify_data = json_decode($notify, true);

                $order_id = $notify['out_trade_no'];
                $order_info = Order::find()
                    ->where(['order_id' => $order_id])
                    ->one();

                $total_fee = $order_info->real_total_money * 100;
                if ($total_fee != $notify['total_fee']) {
                    CommonHelper::writeOrderLog(['order_id' => $order_id, 'msg' => 'fee error', 'data' => $notify]);
                    return false;
                }

                if ($order_info->is_pay != ConStatus::$ORDER_PAY) { // 更新订单状态
                    $order_info->is_pay = ConStatus::$ORDER_PAY;
                    $order_info->order_status = ConStatus::$ORDER_PAY_FINISH;
                    $order_info->pay_from = ConStatus::$PAY_WAY_WECHAT;
                    $order_info->trade_no = $notify['transaction_id'];
                    if ($order_info->save()) {
                        // 获取订单详情
                        $product_title = "";
                        $order_detail = OrderDetail::find()
                            ->where(['order_id' => $order_info->id])
                            ->asArray()
                            ->all();

                        foreach ($order_detail as $od) {
                            $product_title .= $od['title'] . '×' . $od['num'] . '、';
                        }

                        // 消息通知
                        $template_id = \Yii::$app->params['wx']['template'];
                        $client_info = Client::findOne($order_info->client_id);
                        $template = (new Application(['conf' => \Yii::$app->params['wx']['mp']]))->driver("mp.template");
                        $notify_url = CommonHelper::getDomain() . "/front/#/order/mylist";

                        $msg_data = [
                            'first' => '商品购买成功，请您注意物流信息，及时收取货物',
                            'keyword1' => $product_title,
                            'keyword2' => $order_id,
                            'keyword3' => $order_info->real_total_money . "元",
                            'keyword4' => date('Y-m-d H:i:s'),
                            'keyword5' => $order_info->user_name . " " . $order_info->user_phone . " " . $order_info->user_address,
                        ];
                        if ($client_info->open_id) {
                            try {
                                $result = $template->send($client_info->open_id, $template_id['order_success'],
                                    $notify_url, $msg_data);
                                CommonHelper::writeOrderLog(['type' => 'send template msg', 'data' => $result]);
                            } catch (Exception $exception) {
                                CommonHelper::writeOrderLog([
                                    'type' => 'send template error',
                                    'data' => [
                                        'code' => $exception->getCode(),
                                        'msg' => $exception->getMessage()
                                    ]
                                ]);
                            }
                        } else {
                            CommonHelper::writeOrderLog(['type' => 'client no openid', 'data' => $order_id]);
                        }


                        // 通知管理员
                        $room_info = LiveRoom::findOne($order_info->room_id);
                        $msg_data['first'] = "您有新订单，请尽快安排服务。";
                        $msg_data['keyword2'] = date('Y年m月d日');
                        $msg_data['keyword3'] = $order_info->user_address;
                        $msg_data['keyword4'] = $order_info->user_name . ' ' . $order_info->user_phone;
                        $msg_data['keyword5'] = '已付款';
                        $msg_data['remark'] = "订单来自：" . $room_info->room_name; // 直播间
                        $result = $template->send(\Yii::$app->params['wx']['notify'], $template_id['admin_notice'],
                            $notify_url, $msg_data);
                        CommonHelper::writeOrderLog(['type' => 'send admin msg', 'data' => $result]);
                        return true;
                    } else {
                        CommonHelper::writeOrderLog([
                            'order_id' => $order_id,
                            'msg' => 'fee error',
                            'data' => $order_info->getFirstErrors()
                        ]);
                        return false;
                    }

                }

                return true;

            }
        });

        return $response;
    }

    public function actionTest()
    {
        //$notify = '{"appid":"wx2e4c11f43a7669eb","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1313582001","nonce_str":"BN404ck_pWaHatr-nknOMa0ALRZ5m7Qw","openid":"omIqUv9pP6EaM3tqd4UoAs4J4Ncw","out_trade_no":"2018112892002","result_code":"SUCCESS","return_code":"SUCCESS","sign":"9F5707EA90BA5CE2AB0EB83B7B99473E","time_end":"20181128203928","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000220201811287915942939"}';
        $order_info = Order::findOne(18);
        $notify_url = CommonHelper::getDomain() . "/front/#/order/mylist";
        $conf = \Yii::$app->params['wx']['mp'];
        $template_id = \Yii::$app->params['wx']['template'];
        $order_id = $order_info->order_id;
        $product_title = "";
        $order_detail = OrderDetail::find()
            ->where(['order_id' => $order_info->id])
            ->asArray()
            ->all();

        foreach ($order_detail as $od) {
            $product_title .= $od['title'];
        }

        $template_id = \Yii::$app->params['wx']['template'];
        $client_info = Client::findOne($order_info->client_id);
        $template = (new Application(['conf' => \Yii::$app->params['wx']['mp']]))->driver("mp.template");
        $notify_url = CommonHelper::getDomain() . "/front/#/order/mylist";

        $msg_data = [
            'first' => '商品购买成功，请您注意物流信息，及时收取货物',
            'keyword1' => $product_title,
            'keyword2' => $order_id,
            'keyword3' => $order_info->real_total_money . "元",
            'keyword4' => date('Y-m-d H:i:s'),
            'keyword5' => $order_info->user_name . " " . $order_info->user_phone . " " . $order_info->user_address,
        ];
        if ($client_info->open_id) {
            try {
                $result = $template->send($client_info->open_id, $template_id['order_success'], $notify_url, $msg_data);
                CommonHelper::writeOrderLog(['type' => 'send template msg', 'data' => $result]);
            } catch (Exception $exception) {
                CommonHelper::writeOrderLog([
                    'type' => 'send template error',
                    'data' => [
                        'code' => $exception->getCode(),
                        'msg' => $exception->getMessage()
                    ]
                ]);
            }

        } else {
            CommonHelper::writeOrderLog(['type' => 'client no openid', 'data' => $order_id]);
        }


        // 通知管理员
        $room_info = LiveRoom::findOne($order_info->room_id);
        $msg_data['first'] = "您有新订单，请尽快安排服务。";
        $msg_data['keyword2'] = date('Y年m月d日');
        $msg_data['keyword3'] = $order_info->user_address;
        $msg_data['keyword4'] = $order_info->user_phone;
        $msg_data['keyword5'] = '已付款';
        $msg_data['remark'] = "订单来自：" . $room_info->room_name; // 直播间
        $result = $template->send(\Yii::$app->params['wx']['notify'], $template_id['admin_notice'], $notify_url,
            $msg_data);
        CommonHelper::writeOrderLog(['type' => 'send admin msg', 'data' => $result]);

    }

    public function actionJssdk()
    {
        $url = \Yii::$app->request->post('url');
        $apis = \Yii::$app->request->post('apis');
        $js = (new Application(['conf' => \Yii::$app->params['wx']['mp']]))->driver("mp.jssdk");

        $apis = explode(',', $apis);
        $sdk = $js->buildConfigJs($apis, false, $url);
        return $this->successInfo($sdk);
    }


    /**
     * 添加菜单栏
     */
    public function actionAddMenu()
    {
        $conf = \Yii::$app->params['wx']['mp'];
        $menu = (new Application(['conf' => $conf]))->driver("mp.menu");
        $buttons = [
            'button' => [
                [
                    'type' => 'click',
                    'name' => '溯源直播',
                    'url' => 'https://yc.adaxiang.com/front/?from=singlemessage#/',
                ],
            ]
        ];
        $result = $menu->create($buttons);
    }

    /**
     * 接收微信消息
     */
    public function actionMessage()
    {
        $conf = \Yii::$app->params['wx']['mp'];
        $server = (new Application(['conf' => $conf]))->driver("mp.server");
        $server->setMessageHandler(function($message) {
            //file_put_contents('wx.txt', json_encode($message), FILE_APPEND);
            if ($message['MsgType'] == 'event') { // 事件
                switch ($message['Event']) {
                    case 'SCAN': // 扫码
                        break;
                    case 'subscribe': // 关注

                        break;
                    case 'VIEW':
                        break;
                }
            } elseif ($message['MsgType'] == 'text') { // 文本内容

            }

            /*$redis = \Yii::$app->redis;
            $key = 'bindWechat:'.$userId;
            $redis->set($key, json_encode($user_detail));
            $redis->expire($key, 7200); // 缓存2小时*/

            //return "欢迎你";
        });

        $response = $server->serve();
        return $response;
    }
}