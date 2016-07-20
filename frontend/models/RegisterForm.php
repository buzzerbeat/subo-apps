<?php

namespace frontend\models;

use common\models\ImageForm;
use common\models\OauthClients;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * LoginForm is the model behind the login form.
 */
class RegisterForm extends BaseForm
{
    public $mobile;
    public $password;
    public $username;
    public $avatar;
    public $avatarFile;
    public $user;

    public $client;
    public $client_secret;

    public $token = '';



    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'client', 'client_secret', 'mobile', 'password'], 'required'],
            ['mobile', 'match', 'pattern' => '/^[\d]{11}$/i'],
            [['username'], 'trim'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_\-\x{4e00}-\x{9fa5}]{2,12}$/u'],
            ['password', 'string', 'min' => 6, 'max' => 20],
            [['username', 'avatar', 'client', 'client_secret', 'token'],  'string'],
            ['client', 'validateClient'],
            [['avatarFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif'],
            ['username', 'validateNickname'],
            ['mobile', 'validateIsVerified'],
        ];
    }


    public function validateIsVerified($attribute, $params)
    {
        $cache = yii::$app->cache;
        $verifyKey = "verify_" . $this->mobile;
        if (!$cache->get($verifyKey)) {
            $this->addError($attribute, "该手机号注册超时，请重新请求验证码");
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

    public function validateNickname($attribute, $params) {
        $exist = User::find()->where([
            'username'=>$this->$attribute,
        ])->exists();
        if ($exist) {
            $this->addError($attribute, '用户名已存在');
        }
    }

    public function register()
    {
        if ($this->validate()) {
            $newUser = User::find()->where(['mobile'=>$this->mobile])->one();
            if (empty($newUser)) {
                $this->avatarFile = UploadedFile::getInstanceByName('avatarFile');
                $avatarForm = new ImageForm();
                if (!empty($this->avatarFile)) {
                    $avatarModel = $avatarForm->upload($this->avatarFile);
                } else if (!empty($this->avatar)) {
                    $avatarForm->url = $this->avatar;
                    $avatarModel = $avatarForm->save();
                }

                $newUser = new User();
                $newUser->setAttributes([
                    'mobile'=>$this->mobile,
                    'username'=>$this->username,
                    'created_at'=>time(),
                    'updated_at'=>time() - 86400 * 90,
                    'type'=>User::MOBILE_TYPE,
                    'client_id'=> $this->client,
                ], false);

                if (!empty($avatarModel)) {
                    $newUser->avatar = $avatarModel->id;
                }


                $newUser->status = User::STATUS_ACTIVE;
                $newUser->setPassword($this->password);
                $newUser->generateAuthKey();
                $newUser->generatePasswordResetToken();
                if (!$newUser->save()) {
                    $this->addErrors($newUser->getErrors());
                    return false;
                }
                if (!$newUser->generateToken($this->client)) {
                    $this->addErrors($newUser->getErrors());
                    return false;
                }
                $this->user = $newUser;

                return true;
            } else {
                $this->addError('mobile', '用户已存在');
                return false;
            }
        }
        return false;
    }







}
