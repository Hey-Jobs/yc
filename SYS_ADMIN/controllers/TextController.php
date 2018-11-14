<?php
namespace SYS_ADMIN\controllers;

use app\models\Test;
use yii\web\Controller;

class TextController extends \yii\rest\Controller
{
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

    /**
     * Site Index
     */
    public function actionIndex()
    {
        $model = new SysDemo();
        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function actionAdd()
    {
        $content = file_get_contents("php://input");

        $info = json_decode($content, true);
        $testM = new Test();
        $testM->content = $content;
        $testM->stream = $info['stream'];
        $testM->start_time = date('Y-m-d H:i:s', $info['start_time']);

        if (!$testM->save()) {
            return $this->errorInfo(400);
        }

        return $this->successInfo(true);
    }

    public function actionOne()
    {
        $stream = \Yii::$app->request->get("stream", null);
        $start_time = \Yii::$app->request->get("start_time", null);

        $testInfo = Test::find()
            ->filterWhere(['stream' => $stream])
            ->filterWhere(['>=', 'start_time', $start_time])
            ->limit(1)
            ->orderBy('id desc')
            ->asArray()
            ->all();

        foreach ($testInfo as $key => $row) {
            $testInfo[$key]['content'] = json_decode($row['content']);
        }
        return $this->successInfo($testInfo);
    }


    public function actionA()
    {
        $a = 6;
        $b = 12;
        $this->swap($a, $b);
        echo $a . '-' . $b;
        exit;
    }


}
