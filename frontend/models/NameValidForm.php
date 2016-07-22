<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/6
 * Time: 11:48
 */

namespace frontend\models;


use common\models\OauthClients;
use common\models\User;
use yii\base\Model;

class NameValidForm extends Model
{
    public $username;
    public $client;
    public $client_secret;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['client','username'], 'required'],
            [['username'], 'trim'],
            [['username', 'client'],  'string'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_\-\x{4e00}-\x{9fa5}]{2,12}$/u'],
            ['username', 'validateDuplicate'],
            ['client', 'validateClient'],
        ];
    }

    public function validateDuplicate($attribute, $params)
    {
        $exist = User::find()->where([
            'username'=>$this->username,
            'client_id'=>$this->client,
        ])->exists();
        if ($exist) {
            $this->addError($attribute, "用户名已存在");
        }
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
}