<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/22
 * Time: 13:53
 */

namespace common\components;


use Yii;
use yii\web\ResponseFormatterInterface;

class EncodeFormatter implements ResponseFormatterInterface
{
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', 'text/raw; charset=UTF-8');
        if ($response->data !== null) {
            $response->content = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(Yii::$app->params['encodeKey']),  json_encode($response->data), MCRYPT_MODE_ECB);
        }
    }
}