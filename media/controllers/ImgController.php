<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/5
 * Time: 20:29
 */

namespace media\controllers;

use common\models\Image;
use Yii;
use yii\web\Controller;

class ImgController extends Controller
{
    /**
     * Lists all Album models.
     * @return mixed
     */
    public function actionShow()
    {
        $request = Yii::$app->request;
        $path = Image::url($request->get('sid'), $request->get('width', 0), $request->get('height', 0), $request->get('mode', 1));
        $pathInfo = pathinfo($path);
        $extension = $pathInfo["extension"];
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $contentType = 'image/jpeg';
        } else if ($extension == 'gif') {
            $contentType = 'image/gif';
        } else if ($extension == 'png') {
            $contentType = 'image/png';
        } else {
            $contentType = 'image';
        }


        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($path));

        readfile($path);
        exit;
    }
}