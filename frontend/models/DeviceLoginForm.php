<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/20
 * Time: 16:46
 */

namespace frontend\models;


use common\models\OauthClients;
use common\models\User;
use yii\base\Model;

class DeviceLoginForm extends Model
{
    public $device_uuid;
    public $client;
    public $client_secret;
    public $user;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {

        return [
            [['device_uuid', 'client', 'client_secret'], 'required'],
            [['device_uuid', 'client', 'client_secret'],  'string'],
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


    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->user = User::findOne([
                'device_uuid'=>$this->device_uuid,
                'client_id'=>$this->client,
            ]);
            if (empty($this->user)) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }
}