<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/4/15
 * Time: 11:45
 */

namespace common\models;


use yii\base\Model;
use common\components\Ip2Location;


/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $rateEnable
 * @property integer $adEnable
 * @property string $rateTitle
 * @property string $rateConfirm
 * @property string $rateRefuse
 */
class ConfigInfo extends Model
{
    public static function getMobileInfo(){
        $ret = array();
        $ret['app'] = '';
        $ret['system'] = '';
        $ret['systemVersion'] = '';
        $ret['appversion'] = 0;
        $ret['browser'] = '';
        $ret['browserVersion'] = '';
        $ret['userid'] = '';
        $ret['channel'] = 'unknown';
		if (empty($_SERVER['HTTP_USER_AGENT'])) {
			return $ret;
		}
		$ua = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/([\w]+) ([\w]+) v([\d\.]+) /siU', $ua, $m)) {
	        $ret['app'] 		= $m[1];
	        $ret['system'] 		= $m[2];
	    	$versions = explode('.', $m[3]);
	       	if (count($versions) == 3) {
	    		$ret['appversion'] = $versions[0]*10000+$versions[1]*100+$versions[2];
	      	}
        }
        if (preg_match('/mid:([\d\-a-f]+)/si', $ua, $userid)) {
        	$ret['userid'] 	= $userid[1];
        }
        if (preg_match('/channel:([\w]+)/si', $ua, $channel)) {
        	$ret['channel'] 	= strtolower($channel[1]);
        }
        return $ret;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rateEnable'], 'integer'],
            [['rateTitle'], 'string', 'max' => 255],
            [['rateConfirm', 'rateRefuse'], 'string', 'max' => 64],
        ];
    }
    
    /* 
     * 获取ip地址
     * */
    public static function getIpLocation($ip){
        $location = new Ip2Location();
        $locationData = $location->getLocation($ip);
        
        return $locationData['country'] . ' ' . $locationData['area'];
    }
    
    /* 
     * 获取用户地址
     * */
    public static function getUserLocation(){
        $ip = '';
        if(isset($_SERVER['HTTP_X_REAL_IP'])){
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        elseif(isset($_SERVER['REMOTE_ADDR'])){
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return !empty($ip) ? self::getIpLocation($ip) : '';
    }
}
