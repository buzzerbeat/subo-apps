<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/1
 * Time: 12:37
 */

namespace frontend\models;


use common\models\ImageForm;
use common\models\User;
use yii\base\Model;
use yii\web\UploadedFile;

class UserInfoForm extends Model
{
    public $username;
    public $password;
    public $sex;
    public $avatar;
    public $avatarFile;
    public $personal_sign;
//    public $updated_at;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'string', 'min' => 6, 'max' => 20],
            [['sex'], 'integer'],
            [['avatar', 'personal_sign'], 'string'],
            ['username', 'trim'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_\-\x{4e00}-\x{9fa5}]{2,12}$/u'],
            ['username', 'validateDuplicate'],
            [['avatarFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif'],
            ['username', 'validateFrequency'],

        ];
    }

    public function validateDuplicate($attribute, $params)
    {
        $user = \Yii::$app->user->identity;
        $exist = User::find()->where([
            'username'=>$this->username,
        ])->andWhere(['!=', 'id', $user->id])->exists();
        if ($exist) {
            $this->addError($attribute, "昵称已存在");
        }
    }

    public function validateFrequency($attribute, $params)
    {
        $user = \Yii::$app->user->identity;
        if (!$user->isNameEditable()) {
            $this->addError($attribute, "用户名修改过于频繁");
        }
    }

    public function edit() {
        $user = \Yii::$app->user->identity;
        if ($this->validate()) {

            $this->avatarFile = UploadedFile::getInstanceByName('avatarFile');
//            var_dump($this->avatarFile);
//            exit;
            $avatarForm = new ImageForm();
            if (!empty($this->avatarFile)) {
                $avatarModel = $avatarForm->upload($this->avatarFile);
            } else if (!empty($this->avatar)) {
                $avatarForm->url = $this->avatar;
                $avatarModel = $avatarForm->save();
            }



            $attributes = [];
            foreach($this->getAttributes() as $attr=>$val) {
                if ($this->$attr != null && $this->$attr != false) {
                    if (in_array($attr, array_keys((new User())->attributes))) {
                        if ($attr == 'password') {
                            $user->setPassword($val);
                        } else {
                            $attributes[$attr] = $val;
                        }
                    }
                }
            }
            if (!empty($avatarModel)) {
                $user->avatar = $avatarModel->id;
            }
            $user->setAttributes($attributes, false);
            if (!empty($this->username)) {
                $user->updated_at = time();
            }
            if (!$user->save()) {
                $this->addError($user->getErrors());
                return false;
            }
            return true;
        }
        return false;
    }
}