<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/5/30
 * Time: 19:53
 */

namespace frontend\models;


use common\models\OauthClients;
use common\models\User;
use Yii;
use yii\base\Model;

class TokenForm extends Model
{
    public $refresh_token;
    public $client;
    public $client_secret;

    public $user;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
//            [['mobile','password'], 'required'],
            [['refresh_token', 'client', 'client_secret'], 'required'],
            [['refresh_token', 'client', 'client_secret'], 'string'],
            ['client', 'validateClient'],
        ];
    }

    public function validateClient($attribute, $params)
    {
        $oClient = OauthClients::findOne([
            'client_id'=>$this->client,
            'client_secret'=>$this->client_secret,
        ]);
        if (empty($oClient)) {
            $this->addError($attribute, "Cannot find the spec client.");
        }
    }

    public function revoke()
    {
        $user = User::findIdentityByRefreshToken($this->refresh_token, $this->client);
        if ($this->validate() && !empty($user)) {
            $user->generateToken($this->client, $this->refresh_token);
            $user->client = $this->client;
            $this->user = $user;
            return true;
        } else {
            $this->addError('', '刷新token验证失败');
            return false;
        }
    }

    public function accessToken()
    {
        if ($this->validate()) {
            $this->user = User::findIdentityByAccessToken($this->access_token);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
//    public function getUser()
//    {
//        if ($this->_user === false) {
//            $this->_user = User::findByMobile($this->mobile);
//        }
//        return $this->_user;
//    }

}