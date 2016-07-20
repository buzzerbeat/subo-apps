<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/20
 * Time: 17:03
 */

namespace common\components;


use common\models\User;
use yii\base\ActionFilter;
use yii\filters\AccessControl;

class LevelFilter extends AccessControl
{
    public $levels = [];

    public function init()
    {
        parent::init();

    }

    public function beforeAction($action)
    {
        $user = $this->user;
        $preRet = parent::beforeAction($action);
        $ret = true;
        if (in_array($action->id, $this->levels)) {

            if ($user->identity->type === User::DEVICE_TYPE) {
                $this->denyAccess($user);
                $ret = false;
            }
        }

        return  $preRet && $ret;
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }
}