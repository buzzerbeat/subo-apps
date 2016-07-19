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
class ThirdRegisterForm extends BaseForm
{
    public $username;
    public $avatar;
    public $avatarFile;
    public $user;

    public $client;
    public $client_secret;

    public $oid;
    public $from;

    public $token = '';

//    public $auth_key;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'client', 'client_secret', 'oid', 'from'], 'required'],
            [['username'], 'trim'],
            ['username', 'filter', 'filter' => function ($value) {
                // normalize phone input here
                ///^[a-zA-Z0-9_\-\x{4e00}-\x{9fa5}]{2,12}$/u
                return preg_replace('/[^a-zA-Z0-9_\-\x{4e00}-\x{9fa5}]/u', '', $this->username);
            }],
            [['avatarFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif'],
            [['username', 'avatar', 'client', 'client_secret', 'oid', 'from', 'token'],  'string'],
            ['client', 'validateClient'],
            ['from', function ($attribute, $params) {
                if (!in_array($this->$attribute, ['qq', 'weixin', 'weibo'])) {
                    $this->addError($attribute, 'Field \'from\' must be qq, weixin or weibo.');
                }
            }],
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

    private function validateNickname($nickname) {
        $exist = User::find()->where([
            'username'=>$nickname,
        ])->exists();
        return $exist;
    }


    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function thirdRegister()
    {
        if ($this->validate()) {

            $newUser = User::find()
                ->where([$this->from=>$this->oid])
                ->one();
            if (empty($newUser)) {
                if($this->validateNickname($this->username)) {
                    $this->username .= rand(100,999);
                }

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
                    'username'=>$this->username,
                    'created_at'=>time(),
                    'updated_at'=>time() - 86400 * 90,
                    'type'=>User::THIRD_TYPE,
                    'from_app'=>$this->getFromApp(),
                ], false);

                if (!empty($avatarModel)) {
                    $newUser->avatar = $avatarModel->id;
                }
                switch($this->from) {
                    case "qq":
                        $newUser->qq = $this->oid;
                        break;
                    case "weibo":
                        $newUser->weibo = $this->oid;
                        break;
                    case "weixin":
                        $newUser->weixin = $this->oid;
                        break;
                }

                $newUser->status = User::STATUS_ACTIVE;
                $newUser->genRandomPassword();
                $newUser->generateAuthKey();
                $newUser->generatePasswordResetToken();
                if (!$newUser->save() || !$newUser->generateToken($this->client)) {
                    $this->addErrors($newUser->getErrors());
                    return false;
                }
                $newUser->client = $this->client;
                $this->user = $newUser;

                return true;
            } else {
                $this->addError('', '用户已存在');
                return false;
            }
        }
        return false;
    }



}
