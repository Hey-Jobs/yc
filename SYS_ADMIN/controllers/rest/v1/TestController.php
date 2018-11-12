<?php
/**
 * Date: 2018/11/12
 * Time: 9:55
 */

namespace SYS_ADMIN\controllers\rest\v1;


use Firebase\JWT\JWT;

class TestController extends CommonController
{
    public $key = "wilbur_xu_key";

    /**
     * iss: 该JWT的签发者
     * sub: 该JWT所面向的用户
     * aud: 接收该JWT的一方
     * exp(expires): jwt的过期时间，过期时间必须要大于签发时间；什么时候过期，这里是一个Unix时间戳
     * iat(issued at): 在什么时候签发的
     * iat (Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
     */
    public function actionEncode1()
    {
        $token = array(
            "iss" => "test.yc.com",
            "aud" => "test.yc.com",
            "iat" => time(),
            "nbf" => time(),
            "exp" => time() + 7200,
            "data" => [
                "user_id" => 1,
                "username" => "巨星",
            ]
        );
        $jwt = JWT::encode($token, $this->key);
        echo $jwt;
        exit;
    }

    public function actionVerify1()
    {
        $jwtCode = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0LnljLmNvbSIsImF1ZCI6InRlc3QueWMuY29tIiwiaWF0IjoxNTQyMDEzMDUwLCJuYmYiOjE1NDIwMTMwNTAsImV4cCI6MTU0MjAyMDI1MCwiZGF0YSI6eyJ1c2VyX2lkIjoxLCJ1c2VybmFtZSI6Ilx1NWRlOFx1NjYxZiJ9fQ.orGFiIxhqE8GpaLJg2Vy8cV2mhcsGROCirVFX9Lg-Kk";
        try {
            JWT::$leeway = 60;  // 当前时间减去60，把时间留点余地
            $decoded = JWT::decode($jwtCode, $this->key, array('HS256'));; // HS256方式，这里要和签发的时候对应
        } catch (\Firebase\JWT\SignatureInvalidException $e) {  // 签名不正确
            echo 1;
            echo $e->getMessage();
        } catch (\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
            echo 2;
            echo $e->getMessage();
        } catch (\Firebase\JWT\ExpiredException $e) {  // token过期
            echo 3;
            echo $e->getMessage();
        } catch (\Exception $e) {  // 其他错误
            echo 4;
            echo $e->getMessage();
        }
        exit;
    }

    public function actionEncode()
    {
        $key = $this->key; //key
        $time = time(); //当前时间

        //公用信息
        $token = [
            'iss' => 'http://test.yc.com', //签发者 可选
            'iat' => $time, //签发时间
            'data' => [ //自定义信息，不要定义敏感信息
                'user_id' => 1,
            ]
        ];

        $access_token = $token;
        $access_token['scopes'] = 'role_access'; //token标识，请求接口的token
        $access_token['exp'] = $time + 7200; //access_token过期时间,这里设置2个小时

        $refresh_token = $token;
        $refresh_token['scopes'] = 'role_refresh'; //token标识，刷新access_token
        $refresh_token['exp'] = $time + (86400 * 30); //access_token过期时间,这里设置30天

        $jsonList = [
            'access_token' => JWT::encode($access_token, $key),
            'refresh_token' => JWT::encode($refresh_token, $key),
            'token_type' => 'bearer' // token_type：表示令牌类型，该值大小写不敏感，这里用bearer
        ];

        Header("HTTP/1.1 201 Created");
        echo json_encode($jsonList); // 返回给客户端token信息
        exit;
    }

    public function actionTest()
    {
        var_dump($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        exit;
    }
}