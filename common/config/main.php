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
            'enableSchemaCache' => true,
            // Duration of schema cache.
            'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => 'cache',
        ],
        'qsykDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=qsyk',
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
            'enableSchemaCache' => true,
            // Duration of schema cache.
            'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => 'cache',
        ],
        'wpDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wallpaper',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            // Duration of schema cache.
            'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => 'cache',
        ],
        'bDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=beauty',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],
        'tdDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=taskdist',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],

        'atDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=article',
            'username' => 'root',
            'password' => 'my,YZWX;87',
            'charset' => 'utf8',
        ],
        'redis' => 'common\components\RedisHelper',
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

    ],
];
