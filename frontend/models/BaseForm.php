<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/19
 * Time: 15:27
 */

namespace frontend\models;


use common\models\OauthAccessTokens;
use Yii;
use yii\base\Model;

class BaseForm extends Model
{
    public function getFromApp() {
        return isset($_COOKIE['from_app']) ? $_COOKIE['from_app'] : "default";
    }

    public function getClientId($user) {
        $token = OauthAccessTokens::findOne([
            'access_token' => $user->accessToken,
        ]);
        return !empty($token) ? $token->client_id : "other";
    }

}