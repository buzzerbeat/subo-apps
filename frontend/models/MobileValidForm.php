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

class MobileValidForm extends Model
{
    public $mobile;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['mobile'], 'required'],
            ['mobile', 'match', 'pattern' => '/^[\d]{11}$/i'],
            ['mobile', 'validateDuplicated'],
        ];
    }

    public function validateDuplicated($attribute, $params) {
        $exist = User::find()->where([
            'mobile'=>$this->mobile,
        ])->exists();
        if ($exist) {
            $this->addError($attribute, '手机号已注册');
        }
    }
}