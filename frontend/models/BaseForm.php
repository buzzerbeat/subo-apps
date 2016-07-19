<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/19
 * Time: 15:27
 */

namespace frontend\models;


use Yii;
use yii\base\Model;

class BaseForm extends Model
{
    public function getFromApp() {
        return isset($_COOKIE['from_app']) ? $_COOKIE['from_app'] : "default";
    }

}