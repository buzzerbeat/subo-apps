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

class MobileValidForm extends Model
{
    public $mobile;
    public $client;
    public $client_secret;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['client', 'mobile'], 'required'],
            [['mobile', 'client'],  'string'],
            ['mobile', 'match', 'pattern' => '/^[\d]{11}$/i'],
            ['mobile', 'validateDuplicated'],
            ['client', 'validateClient'],
        ];
    }

    public function validateDuplicated($attribute, $params) {
        $exist = User::find()->where([
            'mobile'=>$this->mobile,
            'client_id'=>$this->client,
        ])->exists();
        if ($exist) {
            $this->addError($attribute, '手机号已注册');
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