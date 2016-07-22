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

class ThirdBindCheckForm extends Model
{
    public $mobile;
    public $client;
    public $client_secret;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['mobile', 'client'],  'string'],
            ['mobile', 'match', 'pattern' => '/^[\d]{11}$/i'],
            ['client', 'validateClient'],
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


    public function isThirdBind() {
        if ($this->validate()) {
            $user = User::findByMobile($this->mobile, $this->client);
            if ($user) {
                if (empty($user->qq) && empty($user->weibo) && empty($user->weixin)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                $this->addError('mobile', '手机号未注册');
                return false;
            }
        }
        return false;
        
    }
}