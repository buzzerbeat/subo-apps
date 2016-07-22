<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/20
 * Time: 14:33
 */

namespace frontend\models;


use common\components\Utility;
use common\models\OauthClients;
use common\models\User;
use yii\base\Model;

class DeviceRegisterForm extends Model
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
//            ['device_uuid', 'validateUuid'],
            ['client', 'validateClient'],
        ];
    }

//    public function validateUuid($attribute, $params)
//    {
//       if (User::find()->where([
//            'device_uuid'=>$this->device_uuid,
//            'client_id'=>$this->client,
//        ])->exists()) {
//            $this->addError($attribute, '设备已经注册');
//            return false;
//        }
//    }

    public function validateClient($attribute, $params)
    {
        $oClient = OauthClients::findOne([
            'client_id'=>$this->client,
            'client_secret'=>$this->client_secret,
        ]);
        if (empty($oClient)) {
            $this->addError($attribute, "Cannot find the spec client.");
            return false;
        }
    }


    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function register()
    {
        if ($this->validate()) {
            $user = User::findOne([
                'device_uuid'=>$this->device_uuid,
                'client_id'=>$this->client,
            ]);
            if (empty($user)) {
                $user = new User();
                $user->setAttributes([
                    'device_uuid' => $this->device_uuid,
                    'username' => "duser_" . Utility::getRandNumber(8),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'type' => User::DEVICE_TYPE,
                    'status' => User::STATUS_ACTIVE,
                    'client_id'=>$this->client,
                ]);
                $user->genRandomPassword();
                $user->generateAuthKey();
                $user->generatePasswordResetToken();
                if (!$user->save() || !$user->generateToken($this->client)) {
                    $this->addErrors($user->getErrors());
                    return false;
                }
            }
            $this->user = $user;
            return true;
        }
        return false;
    }

}