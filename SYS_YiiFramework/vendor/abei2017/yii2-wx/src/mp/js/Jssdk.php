<?php
/**
 * User: liwj
 * Date:2018/11/28
 * Time:14:51
 */

namespace abei2017\wx\mp\js;
use Yii;


class Jssdk extends Js
{
    public function buildConfigJs($apis = [],$debug = false, $url){

        $signPackage = $this->signatureJs($url);
        $config = array_merge(['debug'=>$debug],$signPackage,['jsApiList'=>$apis]);

        return Json::encode($config);
    }

    public function signatureJs($url){

        $nonce = Yii::$app->security->generateRandomString(32);
        $timestamp = time();
        $ticket = $this->ticket();

        $sign = [
            'appId' => $this->conf['app_id'],
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
        ];

        return $sign;
    }
}