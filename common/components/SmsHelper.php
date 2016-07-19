<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/6/1
 * Time: 17:08
 */


namespace common\components;

require_once __DIR__ . "/CCP/CCPRestSDK.php";

use REST;
use yii\base\Component;

class SmsHelper extends Component
{
    public $errMsg;
    public function send($mobile, $code) {
        $params = \Yii::$app->params;
        $sendSdk = new REST($params['ccpServerIp'],$params['ccpServerPort'],$params['ccpSoftVersion']);
        $sendSdk->setAccount($params['ccpAccountSid'],$params['ccpAccountToken']);
        $sendSdk->setAppId($params['ccpAppId']);

        $result = $sendSdk->sendTemplateSMS($mobile, [$code, 5], $params['ccpSMSTemplate']);
        if(empty($result)) {
            $this->errMsg = '获取验证码失败';
            return false;
        }
        if($result->statusCode !=  0){
            $logTxt =  "发送到手机{$mobile}验证短信失败:错误代码：" . $result->statusCode . ' 错误消息:'. $result->statusMsg . "\n";
            $sendSdk->showlog($logTxt);
            $this->errMsg = '验证码发送失败';
            return false;
        }
        else{
            $logTxt = "发送到手机{$mobile}验证短信成功：发送时间：" . $result->dateCreated . ' 信息id：' . $result->smsMessageSid . "\n";
            $sendSdk->showlog($logTxt);
            return true;
        }
    }

}