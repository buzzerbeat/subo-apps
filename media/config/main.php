<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-media',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'media\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'thumb/<width:\d+>/<height:\d+>/<mode:\d{1}>/<sid:.*>/<md5:.*>.<ext:(jpg|JPG|png|JPEG)>' => 'img/show',
                'thumb/<width:\d+>/<height:\d+>/<sid:.*>/<md5:.*>.<ext:(jpg|JPG|png|JPEG)>' => 'img/show',
                'thumb/<sid:.*>/<md5:.*>.<ext:(jpg|JPG|png|JPEG)>' => 'img/show',
            ],
        ],

    ],
    'params' => $params,
];
