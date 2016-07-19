<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/1
 * Time: 12:00
 */

namespace frontend\models;


use common\components\SmsHelper;
use Yii;
use yii\base\Model;

class VerifyForm extends Model
{
    public $mobile;
    public $code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile'], 'required'],
            ['code', 'required', 'when' => function ($model) {
                $cache = yii::$app->cache;
                $key = "mobile_60s_" . $model->mobile;
                return $cache->get($key) ? true : false;
            }],
            ['mobile', 'match', 'pattern' => '/^[\d]{11}$/i'],
            ['code', 'match', 'pattern' => '/^[\d]{6}$/i'],
        ];
    }

    public function request()
    {
        if ($this->validate()) {
            $cache = yii::$app->cache;
            $mobileKey = "mobile_60s_" . $this->mobile;
            $codeKey = "code_" . $this->mobile;
            $verifyCode = $cache->get($codeKey);
            if (!$verifyCode) {
                $verifyCode = rand(100000, 999999);
                $cache->set($codeKey, $verifyCode, 600);
                $cache->set($mobileKey, $verifyCode, 60);
            } else {
                if (!$cache->get($mobileKey)) {
                    $cache->set($mobileKey, $verifyCode, 60);
                }
            }
            $smsHelper = new SmsHelper();
            $this->code = $verifyCode;
            if (!$smsHelper->send($this->mobile, $verifyCode)) {
                $this->addError('',$smsHelper->errMsg);
                return false;
            }
            return true;
        }
        return false;
    }

    public function verify()
    {
        if ($this->validate()) {
            $cache = yii::$app->cache;
            $codeKey = "code_" . $this->mobile;
            $verifyCode = $cache->get($codeKey);
            if ($verifyCode) {
               if ($verifyCode == $this->code) {
                   $verifyKey = "verify_" . $this->mobile;
                   return $cache->set($verifyKey, true, 300);
               } else {
                   $this->addError('', '验证码不正确');
                   return false;
               }
            } else {
                $this->addError('', '验证码请求超时，请重新请求');
                return false;
            }
        }
        return false;
    }
}