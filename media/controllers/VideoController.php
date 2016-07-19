<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/5
 * Time: 20:49
 */

namespace media\controllers;

use common\components\Utility;
use common\models\Video;
use yii\web\Controller;
class VideoController extends Controller
{
    public function actionInfo() {
        return Video::findOne(Utility::id(\Yii::$app->request->get('sid')));
    }

}