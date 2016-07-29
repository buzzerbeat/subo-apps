<?php
return [
    'aliases' => [
        '@imgUrl' => 'http://qy1.appcq.cn:8082/',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=subo_apps',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],
        'qsykDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=qsyk_160629',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],

        'mvDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=microvideo',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],
        'wpDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wallpaper',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],
        'bDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=beauty',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 100,
                ],
            ],
        ],

    ],
];
