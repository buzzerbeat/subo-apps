<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/9/6
 * Time: 19:03
 */

namespace common\components;
use yii\caching\DbDependency;

class BtDbDependency extends DbDependency
{
    public $db = "bDb";

}
