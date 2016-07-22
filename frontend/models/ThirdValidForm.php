<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/6
 * Time: 11:48
 */

namespace frontend\models;


use common\models\OauthClients;
use common\models\User;
use yii\base\Model;

class ThirdValidForm extends Model
{
    public $oid;
    public $from;
    public $client;
    public $client_secret;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['oid', 'from', 'client', 'client_secret'], 'required'],
            [['oid', 'client', 'client_secret'],  'string'],
            ['from', function ($attribute, $params) {
                if (!in_array($this->$attribute, ['qq', 'weixin', 'weibo'])) {
                    $this->addError($attribute, 'Field \'from\' must be qq, weixin or weibo.');
                    return false;
                }
            }],
            ['client', 'validateClient'],
//            ['oid', 'validateDuplicated'],
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

    public function validateDuplicated($attribute) {
        $exist = User::find()->where([
            $this->from=>$this->oid,
            'client_id'=>$this->client,
        ])->exists();
        if ($exist) {
            $this->addError($attribute, '该帐号已注册');
            return false;
        }
        return true;
    }
}