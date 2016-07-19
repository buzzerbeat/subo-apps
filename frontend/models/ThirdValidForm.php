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

class ThirdValidForm extends Model
{
    public $oid;
    public $from;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['oid', 'from'], 'required'],
            ['from', function ($attribute, $params) {
                if (!in_array($this->$attribute, ['qq', 'weixin', 'weibo'])) {
                    $this->addError($attribute, 'Field \'from\' must be qq, weixin or weibo.');
                    return false;
                }
            }],
//            ['oid', 'validateDuplicated'],
        ];
    }

    public function validateDuplicated($attribute) {
        $exist = User::find()->where([
            $this->from=>$this->oid,
        ])->exists();
        if ($exist) {
            $this->addError($attribute, '该帐号已注册');
            return false;
        }
        return true;
    }
}