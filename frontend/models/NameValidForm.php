<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/6
 * Time: 11:48
 */

namespace frontend\models;


use common\models\User;
use yii\base\Model;

class NameValidForm extends Model
{
    public $username;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username'], 'required'],
            [['username'], 'trim'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_\-\x{4e00}-\x{9fa5}]{2,12}$/u'],
            ['username', 'validateDuplicate'],
        ];
    }

    public function validateDuplicate($attribute, $params)
    {
        $exist = User::find()->where([
            'username'=>$this->username,
        ])->exists();
        if ($exist) {
            $this->addError($attribute, "用户名已存在");
        }
    }
}