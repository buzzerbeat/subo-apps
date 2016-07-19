<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/19
 * Time: 15:08
 */

namespace frontend\controllers;


use common\components\Utility;
use common\models\Comment;
use frontend\models\CommentForm;
use frontend\models\CommentLikeForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

class CommentController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['send'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {


        return new ActiveDataProvider([
            'query' => Comment::find()
        ]);
    }


    public function actionView($sid)
    {
        return Comment::findOne(Utility::id($sid));
    }



    public function actionSend()
    {
        $sendForm = new CommentForm();
        if ($sendForm->load(Yii::$app->getRequest()->post(), '') && $sendForm->send()) {
            return ["status"=>0, "message"=>"", "data"=>$sendForm->retPost];
        }
        return ["status"=>1, "message"=>implode(",", $sendForm->getFirstErrors())];
    }

}