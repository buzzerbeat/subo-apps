<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/1
 * Time: 15:16
 */

namespace frontend\models;


use common\models\User;
use Yii;
use yii\base\Model;

class BindForm extends Model
{

    public $mobile;
    public $password;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'password'],'required'],
            ['mobile', 'match', 'pattern' => '/^[\d]{11}$/i'],
            ['password', 'string', 'min' => 6, 'max' => 20],
            ['mobile', 'validateIsVerified'],
            ['mobile','validateDuplicate'],
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

    public function validateDuplicate($attribute, $params)
    {
        $user = \Yii::$app->user->identity;
        $exist = User::find()->where([
            'mobile'=>$this->mobile,
        ])->andWhere(['!=', 'id', $user->id])->exists();
        if ($exist) {
            $this->addError($attribute, "手机号已绑定");
        }
    }

    public function bind() {
        if ($this->validate()) {
            $user = \Yii::$app->user->identity;
            $user->mobile = $this->mobile;
            $user->setPassword($this->password);
            if (!$user->save()) {
                $this->addError($user->getErrors());
                return false;
            }
            return true;
        }
        return false;
    }
}