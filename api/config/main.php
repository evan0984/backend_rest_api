<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/base.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'cookieValidationKey' => env('API_COOKIE_VALIDATION_KEY'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/xml' => 'yii\web\XmlParser',
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => 'json',
            'on beforeSend' => function ($event) {
                header("Access-Control-Allow-Origin: *");               
                header('Access-Control-Allow-Headers: authorization');
                header('Access-Control-Allow-Credentials: true');
                $response = $event->sender;
                if ($response->data !== null) {

                    $data = $response->data;
                    // Error handle
                    $error = '';
                    if( ! $response->isSuccessful) {
                        if(isset($data['message'])) {
                            $error = $data['message'];
                        } elseif(isset(current($data)['message'])) {
                            $error = current($data)['message'];
                        }
                    }
                    $response->data = [
                        'error' => !$response->isSuccessful,
                        //'code' => $response->statusCode,
                        'message' => $error,
                    ];
                    if($response->isSuccessful) {
                        $response->data['data'] = $data;
                    }
                    // $response->statusCode = 200;
                }
            },
            'formatters' => [
                'json' => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'login' => 'site/login',
                'signup' => 'site/signup',
                'update' => 'site/update',
                'profile' => 'site/profile',
                'forgot-sms-send' => 'site/forgot-sms-send',
                'forgot-sms-code' => 'site/forgot-sms-code',
                'new-pass-code' => 'site/new-pass-code',
                'verify-sms-send' => 'site/verify-sms-send',
                'verify-sms-code' => 'site/verify-sms-code',
                'login-sms-send' => 'site/login-sms-send',
                'login-sms-code' => 'site/login-sms-code',
                'get-chat' => 'chat/chat',
                'get-chat-msg' => 'chat/chat-message',
                'delete-chat' => 'chat/delete-chat',
                'send-msg' => 'chat/send-message',
                'update-msg' => 'chat/update-message',
                'delete-msg' => 'chat/delete-message',
            ],
        ],
    ],
    'params' => $params,
];
